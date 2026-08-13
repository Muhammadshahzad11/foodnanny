<template>
    <div class="zone-map-wrap">
        <LoadingContentComponent :props="loading"/>

        <div class="relative rounded-lg border border-gray-200 overflow-hidden bg-white">
            <!-- Floating hand / shape toolbar (matches reference UX, no DrawingManager) -->
            <div class="absolute z-10 top-3 ltr:left-3 rtl:right-3 flex flex-col gap-1 bg-white rounded-md shadow-md border border-gray-200 p-1">
                <button
                    type="button"
                    class="w-9 h-9 rounded flex items-center justify-center transition"
                    :class="tool === 'hand' ? 'bg-primary text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                    :title="$t('label.hand_tool')"
                    @click="setTool('hand')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-4 0"/><path d="M14 10V4a2 2 0 0 0-4 0v2"/><path d="M10 10.5V6a2 2 0 0 0-4 0v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg>
                </button>
                <button
                    type="button"
                    class="w-9 h-9 rounded flex items-center justify-center transition"
                    :class="tool === 'shape' ? 'bg-primary text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                    :title="$t('label.shape_tool')"
                    @click="setTool('shape')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4.5v9L12 20l-8-4.5v-9L12 2z"/><circle cx="12" cy="2" r="1.5" fill="currentColor"/><circle cx="20" cy="6.5" r="1.5" fill="currentColor"/><circle cx="20" cy="15.5" r="1.5" fill="currentColor"/><circle cx="12" cy="20" r="1.5" fill="currentColor"/><circle cx="4" cy="15.5" r="1.5" fill="currentColor"/><circle cx="4" cy="6.5" r="1.5" fill="currentColor"/></svg>
                </button>
            </div>

            <!-- Search overlay -->
            <div class="absolute z-10 top-3 left-1/2 -translate-x-1/2 w-[min(100%-7rem,22rem)]">
                <div class="relative">
                    <i class="lab-line-search absolute ltr:left-3 rtl:right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        ref="autocompleteInput"
                        type="text"
                        class="w-full h-10 rounded-md border border-gray-200 bg-white shadow-sm text-sm ltr:pl-10 ltr:pr-10 rtl:pr-10 rtl:pl-10"
                        :placeholder="$t('label.search_location')"
                    />
                    <button
                        type="button"
                        class="lab-line-gps absolute ltr:right-2 rtl:left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded text-primary"
                        :title="$t('label.use_my_location')"
                        @click="goCurrentLocation"
                    ></button>
                </div>
            </div>

            <div ref="zoneMap" class="w-full h-[420px] sm:h-[520px]"></div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 mt-3">
            <p class="text-xs text-slate-500 m-0">
                {{ $t('label.draw_zone_hint') }}
                <span class="text-slate-700 font-medium"> — {{ $t('label.points') }}: {{ pointCount }}</span>
            </p>
            <div class="flex gap-2">
                <button type="button" class="db-btn py-1.5 px-3 text-sm text-slate-700 bg-slate-100" @click="finishDraft" :disabled="draftPoints.length < 3">
                    {{ $t('button.finish_polygon') }}
                </button>
                <button type="button" class="db-btn py-1.5 px-3 text-sm text-slate-700 bg-white border border-gray-200" @click="resetPolygon">
                    {{ $t('button.reset') }}
                </button>
            </div>
        </div>
        <small class="db-field-alert block mt-1" v-if="error">{{ error }}</small>
    </div>
</template>

<script>
import LoadingContentComponent from "../../../common/LoadingContentComponent.vue";
import ENV from "../../../../config/env.js";

/**
 * Custom Hand / Shape polygon drawing — does NOT use deprecated DrawingManager.
 * Shape mode: click to add vertices, Finish (or double-click) to close (>= 3 points).
 * Hand mode: pan/zoom only; existing polygon remains editable.
 */
export default {
    name: "ZonePolygonMapComponent",
    components: {LoadingContentComponent},
    props: {
        modelValue: {type: Array, default: () => []},
        restaurantLocation: {type: Object, default: () => ({lat: null, lng: null})},
    },
    emits: ['update:modelValue'],
    data() {
        return {
            loading: {isActive: false},
            map: null,
            polygon: null,
            draftPolyline: null,
            draftMarkers: [],
            draftPoints: [],
            restaurantMarker: null,
            clickListener: null,
            dblClickListener: null,
            tool: 'hand',
            syncing: false,
            error: null,
        };
    },
    computed: {
        pointCount() {
            if (this.draftPoints.length) {
                return this.draftPoints.length;
            }
            const pts = this.modelValue || [];
            if (pts.length < 2) return pts.length;
            const first = pts[0];
            const last = pts[pts.length - 1];
            if (first && last && Number(first.lat) === Number(last.lat) && Number(first.lng) === Number(last.lng)) {
                return pts.length - 1;
            }
            return pts.length;
        },
    },
    mounted() {
        this.initMap();
    },
    beforeUnmount() {
        this.detachClickListeners();
        this.clearDraftVisuals();
        if (this.polygon) {
            this.polygon.setMap(null);
        }
    },
    watch: {
        modelValue: {
            deep: true,
            handler(val) {
                if (!this.map || this.syncing || this.draftPoints.length) return;
                this.renderPolygon(val || [], false);
            },
        },
        restaurantLocation: {
            deep: true,
            handler() {
                this.updateRestaurantMarker();
            },
        },
    },
    methods: {
        async initMap() {
            if (!ENV.GOOGLE_MAP_KEY || typeof google === 'undefined') {
                this.error = 'Google Maps is not configured.';
                return;
            }
            this.loading.isActive = true;
            this.error = null;
            try {
                await google.maps.importLibrary('maps');
                await google.maps.importLibrary('places');
                await google.maps.importLibrary('marker');

                const center = this.resolveCenter();
                this.map = new google.maps.Map(this.$refs.zoneMap, {
                    zoom: 14,
                    center,
                    mapId: ENV.GOOGLE_MAP_KEY,
                    gestureHandling: 'greedy',
                    clickableIcons: false,
                    mapTypeControl: true,
                    fullscreenControl: true,
                    streetViewControl: true,
                    disableDoubleClickZoom: true,
                });

                await this.updateRestaurantMarker();
                this.setupAutocomplete();
                this.renderPolygon(this.modelValue || [], true);
                this.setTool('hand');
            } catch (e) {
                this.error = e?.message || 'Map failed to load';
            } finally {
                this.loading.isActive = false;
            }
        },
        resolveCenter() {
            if (this.restaurantLocation?.lat && this.restaurantLocation?.lng) {
                return {
                    lat: parseFloat(this.restaurantLocation.lat),
                    lng: parseFloat(this.restaurantLocation.lng),
                };
            }
            if (this.modelValue?.length) {
                return {
                    lat: parseFloat(this.modelValue[0].lat),
                    lng: parseFloat(this.modelValue[0].lng),
                };
            }
            return {lat: 33.6844, lng: 73.0479};
        },
        async updateRestaurantMarker() {
            if (!this.map || !this.restaurantLocation?.lat || !this.restaurantLocation?.lng) return;
            const position = {
                lat: parseFloat(this.restaurantLocation.lat),
                lng: parseFloat(this.restaurantLocation.lng),
            };
            if (this.restaurantMarker) {
                this.restaurantMarker.position = position;
                return;
            }
            const {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary('marker');
            const pin = new PinElement({scale: 1.05, background: '#1AB759'});
            this.restaurantMarker = new AdvancedMarkerElement({
                map: this.map,
                position,
                content: pin.element,
                title: 'Restaurant',
            });
        },
        setupAutocomplete() {
            const input = this.$refs.autocompleteInput;
            if (!input || !this.map) return;
            const autocomplete = new google.maps.places.Autocomplete(input, {
                fields: ['geometry', 'name'],
            });
            autocomplete.bindTo('bounds', this.map);
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry?.location) return;
                this.map.panTo(place.geometry.location);
                this.map.setZoom(15);
            });
        },
        setTool(tool) {
            this.tool = tool;
            if (!this.map) return;

            if (tool === 'hand') {
                this.map.setOptions({draggableCursor: null, draggingCursor: null});
                this.detachClickListeners();
                if (this.polygon) {
                    this.polygon.setEditable(true);
                }
            } else {
                this.map.setOptions({draggableCursor: 'crosshair'});
                if (this.polygon && !this.draftPoints.length) {
                    this.polygon.setEditable(false);
                }
                this.attachClickListeners();
            }
        },
        attachClickListeners() {
            this.detachClickListeners();
            this.clickListener = this.map.addListener('click', (e) => this.onMapClick(e));
            this.dblClickListener = this.map.addListener('dblclick', (e) => {
                e.stop();
                this.finishDraft();
            });
        },
        detachClickListeners() {
            if (this.clickListener) {
                google.maps.event.removeListener(this.clickListener);
                this.clickListener = null;
            }
            if (this.dblClickListener) {
                google.maps.event.removeListener(this.dblClickListener);
                this.dblClickListener = null;
            }
        },
        onMapClick(e) {
            if (this.tool !== 'shape' || !e.latLng) return;
            const point = {lat: e.latLng.lat(), lng: e.latLng.lng()};

            // First click of a new draft replaces any finished polygon
            if (!this.draftPoints.length && this.polygon) {
                this.polygon.setMap(null);
                this.polygon = null;
            }

            // Close if user clicks near the first point with >= 3 vertices
            if (this.draftPoints.length >= 3) {
                const first = this.draftPoints[0];
                const dist = Math.hypot(point.lat - first.lat, point.lng - first.lng);
                if (dist < 0.00015) {
                    this.finishDraft();
                    return;
                }
            }

            this.draftPoints.push(point);
            this.drawDraft();
        },
        drawDraft() {
            this.clearDraftVisuals(false);
            if (!this.draftPoints.length) return;

            this.draftPolyline = new google.maps.Polyline({
                path: this.draftPoints,
                strokeColor: '#1AB759',
                strokeOpacity: 0.9,
                strokeWeight: 2,
                map: this.map,
                clickable: false,
            });

            this.draftMarkers = this.draftPoints.map((p, idx) => {
                return new google.maps.Marker({
                    position: p,
                    map: this.map,
                    clickable: idx === 0 && this.draftPoints.length >= 3,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: idx === 0 ? 7 : 5,
                        fillColor: '#ffffff',
                        fillOpacity: 1,
                        strokeColor: '#1AB759',
                        strokeWeight: 2,
                    },
                });
            });

            if (this.draftMarkers[0] && this.draftPoints.length >= 3) {
                this.draftMarkers[0].addListener('click', () => this.finishDraft());
            }
        },
        clearDraftVisuals(clearPoints = true) {
            if (this.draftPolyline) {
                this.draftPolyline.setMap(null);
                this.draftPolyline = null;
            }
            (this.draftMarkers || []).forEach((m) => m.setMap(null));
            this.draftMarkers = [];
            if (clearPoints) {
                this.draftPoints = [];
            }
        },
        finishDraft() {
            if (this.draftPoints.length < 3) {
                this.error = this.$t('message.zone_polygon_required');
                return;
            }
            this.error = null;
            const closed = [...this.draftPoints];
            const first = closed[0];
            const last = closed[closed.length - 1];
            if (first.lat !== last.lat || first.lng !== last.lng) {
                closed.push({...first});
            }
            this.clearDraftVisuals(true);
            this.renderPolygon(closed, true);
            this.emitPoints(closed);
            this.setTool('hand');
        },
        resetPolygon() {
            this.error = null;
            this.clearDraftVisuals(true);
            if (this.polygon) {
                this.polygon.setMap(null);
                this.polygon = null;
            }
            this.emitPoints([]);
            if (this.tool === 'shape') {
                this.attachClickListeners();
            }
        },
        renderPolygon(points, fitBounds) {
            if (!this.map) return;
            if (!points?.length) {
                if (this.polygon) {
                    this.polygon.setMap(null);
                    this.polygon = null;
                }
                return;
            }
            const path = points.map((p) => ({
                lat: parseFloat(p.lat ?? p.latitude),
                lng: parseFloat(p.lng ?? p.longitude),
            }));

            if (this.polygon) {
                this.polygon.setPath(path);
                this.polygon.setEditable(this.tool === 'hand');
                this.polygon.setMap(this.map);
            } else {
                this.polygon = new google.maps.Polygon({
                    paths: path,
                    editable: this.tool === 'hand',
                    draggable: false,
                    fillColor: '#1AB759',
                    fillOpacity: 0.28,
                    strokeWeight: 2,
                    strokeColor: '#1AB759',
                    map: this.map,
                });
                this.bindPolygonEvents(this.polygon);
            }

            if (fitBounds) {
                const bounds = new google.maps.LatLngBounds();
                path.forEach((p) => bounds.extend(p));
                this.map.fitBounds(bounds);
            }
        },
        bindPolygonEvents(poly) {
            const path = poly.getPath();
            const emit = () => this.emitPath(poly);
            google.maps.event.addListener(path, 'set_at', emit);
            google.maps.event.addListener(path, 'insert_at', emit);
            google.maps.event.addListener(path, 'remove_at', emit);
        },
        emitPath(poly) {
            const path = poly.getPath();
            const points = [];
            for (let i = 0; i < path.getLength(); i++) {
                const ll = path.getAt(i);
                points.push({lat: ll.lat(), lng: ll.lng()});
            }
            if (points.length >= 3) {
                const first = points[0];
                const last = points[points.length - 1];
                if (first.lat !== last.lat || first.lng !== last.lng) {
                    points.push({...first});
                }
            }
            this.emitPoints(points);
        },
        emitPoints(points) {
            this.syncing = true;
            this.$emit('update:modelValue', points);
            this.$nextTick(() => {
                this.syncing = false;
            });
        },
        goCurrentLocation() {
            if (!navigator.geolocation || !this.map) return;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.map.panTo({lat: pos.coords.latitude, lng: pos.coords.longitude});
                    this.map.setZoom(15);
                },
                () => {
                    this.error = 'Geolocation failed or was denied.';
                }
            );
        },
    },
};
</script>
