import ENV from "../config/env.js";

/**
 * Browser GPS + reverse/forward geocode.
 * Uses Google when VITE_GOOGLE_MAP_KEY is set; otherwise OpenStreetMap Nominatim.
 */
const locationService = {
    hasGoogleMaps() {
        return Boolean(ENV.GOOGLE_MAP_KEY && typeof window !== "undefined" && window.google?.maps);
    },

    getCurrentPosition(options = {}) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error("geolocation_unsupported"));
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                    });
                },
                () => reject(new Error("geolocation_failed")),
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    // Prefer a fresh fix — stale cache is a common cause of "wrong city".
                    maximumAge: 0,
                    ...options,
                }
            );
        });
    },

    async getCurrentPositionReliable() {
        try {
            return await this.getCurrentPosition({
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0,
            });
        } catch (e) {
            if (e?.message === "geolocation_unsupported") {
                throw e;
            }
            // Second attempt: allow a short cache + network location as fallback.
            return await this.getCurrentPosition({
                enableHighAccuracy: false,
                timeout: 15000,
                maximumAge: 30000,
            });
        }
    },

    googleComponent(components, type, useShort = false) {
        const match = (components || []).find((c) => c.types?.includes(type));
        if (!match) return "";
        return useShort ? (match.short_name || match.long_name || "") : (match.long_name || "");
    },

    areaFromGoogleComponents(components = []) {
        const city =
            this.googleComponent(components, "locality")
            || this.googleComponent(components, "postal_town")
            || this.googleComponent(components, "sublocality")
            || this.googleComponent(components, "administrative_area_level_3");
        const district =
            this.googleComponent(components, "administrative_area_level_2")
            || this.googleComponent(components, "administrative_area_level_3")
            || city;
        const state = this.googleComponent(components, "administrative_area_level_1");
        return { city, district, state };
    },

    areaFromNominatim(address = {}) {
        const city =
            address.city
            || address.town
            || address.village
            || address.municipality
            || address.suburb
            || "";
        const district =
            address.county
            || address.state_district
            || address.city_district
            || address.district
            || city
            || "";
        const state = address.state || address.region || "";
        return { city, district, state };
    },

    areaFromPlace(place) {
        if (!place) {
            return { city: "", district: "", state: "" };
        }
        if (place.address_components) {
            return this.areaFromGoogleComponents(place.address_components);
        }
        if (place.address) {
            return this.areaFromNominatim(place.address);
        }
        return { city: "", district: "", state: "" };
    },

    /**
     * Prefer a result that has a city/locality over an ultra-specific street-only hit.
     */
    pickBestGeocodeResult(results = []) {
        if (!results?.length) return null;
        const withCity = results.find((r) =>
            (r.address_components || []).some((c) =>
                c.types?.includes("locality")
                || c.types?.includes("postal_town")
                || c.types?.includes("administrative_area_level_2")
            )
        );
        return withCity || results[0];
    },

    async reverseGeocode(lat, lng) {
        if (this.hasGoogleMaps()) {
            try {
                const geocoder = new google.maps.Geocoder();
                const res = await geocoder.geocode({
                    location: new google.maps.LatLng(lat, lng),
                });
                const place = this.pickBestGeocodeResult(res.results);
                if (place) {
                    const area = this.areaFromPlace(place);
                    return {
                        name: place.formatted_address,
                        address: place.formatted_address,
                        ...area,
                    };
                }
            } catch (e) {
                // fall through to Nominatim
            }
        }

        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`;
        const res = await fetch(url, {
            headers: {
                Accept: "application/json",
                "User-Agent": "FoodNanny/1.0",
            },
        });
        if (!res.ok) {
            return { name: "Current location", address: "Current location", city: "", district: "", state: "" };
        }
        const data = await res.json();
        const label = data.display_name || "Current location";
        return {
            name: label,
            address: label,
            ...this.areaFromNominatim(data.address || {}),
        };
    },

    async forwardGeocode(query) {
        const q = String(query || "").trim();
        if (!q) {
            throw new Error("empty_query");
        }

        if (this.hasGoogleMaps() && google.maps.Geocoder) {
            try {
                const geocoder = new google.maps.Geocoder();
                const res = await geocoder.geocode({
                    address: q,
                    componentRestrictions: { country: "in" },
                });
                const place = this.pickBestGeocodeResult(res.results);
                if (place) {
                    return {
                        name: place.formatted_address,
                        address: place.formatted_address,
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng(),
                        ...this.areaFromPlace(place),
                    };
                }
            } catch (e) {
                // fall through
            }
        }

        const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&addressdetails=1&limit=1&countrycodes=in&q=${encodeURIComponent(q)}`;
        const res = await fetch(url, {
            headers: {
                Accept: "application/json",
                "User-Agent": "FoodNanny/1.0",
            },
        });
        if (!res.ok) {
            throw new Error("geocode_failed");
        }
        const data = await res.json();
        if (!data?.length) {
            throw new Error("geocode_not_found");
        }
        return {
            name: data[0].display_name,
            address: data[0].display_name,
            lat: parseFloat(data[0].lat),
            lng: parseFloat(data[0].lon),
            ...this.areaFromNominatim(data[0].address || {}),
        };
    },
};

export default locationService;
