import askEnum from "../enums/modules/askEnum.js";

export const RESTAURANT_HIGHLIGHT_KEYS = [
    {key: "premium_quality", icon: "lab-fill-verify"},
    {key: "authentic_flavors", icon: "lab-fill-new"},
    {key: "on_time_delivery", icon: "lab-line-bike"},
    {key: "hygienic_kitchen", icon: "lab-line-shield"},
    {key: "loved_by_customers", icon: "lab-line-customers"},
    {key: "cuisines", icon: "lab-line-cuisine"},
];

export function defaultRestaurantHighlights() {
    const highlights = {};
    RESTAURANT_HIGHLIGHT_KEYS.forEach(({key}) => {
        highlights[key] = {
            enabled: askEnum.YES,
            title: "",
        };
    });
    return highlights;
}

export function normalizeRestaurantHighlights(saved = {}) {
    const defaults = defaultRestaurantHighlights();
    Object.keys(defaults).forEach((key) => {
        const row = saved?.[key] || {};
        defaults[key] = {
            enabled: Number(row.enabled ?? askEnum.YES) === askEnum.NO ? askEnum.NO : askEnum.YES,
            title: row.title || "",
        };
    });
    return defaults;
}

export function emptyRestaurantPageDisplay() {
    return {
        show_important_notice: askEnum.YES,
        important_notice: "",
        important_notice_emphasis: "",
        show_highlights: askEnum.YES,
        highlights: defaultRestaurantHighlights(),
        enable_pos: askEnum.YES,
        enable_kitchen: askEnum.YES,
        enable_waiter: askEnum.YES,
    };
}
