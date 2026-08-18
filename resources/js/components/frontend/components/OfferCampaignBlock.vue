<template>
    <article class="offer-block">
        <section class="offer-block__hero" :style="bannerStyle">
            <div class="offer-block__shade"></div>
            <div class="offer-block__decor" aria-hidden="true"></div>
            <component
                :is="linkable ? 'router-link' : 'div'"
                v-bind="heroAttrs"
                class="offer-block__hero-inner"
            >
                <div class="offer-block__art">
                    <img :src="item.thumb || giftFallback" alt="" class="offer-block__gift">
                </div>
                <div class="offer-block__copy">
                    <span class="offer-block__tag">
                        {{ displayTag }}
                        <i class="lab-fill-star"></i>
                    </span>
                    <h3 class="offer-block__title">{{ displayHeadline }}</h3>
                    <p v-if="plainDescription" class="offer-block__desc">{{ plainDescription }}</p>
                    <p v-if="item.start_date && item.end_date" class="offer-block__dates">
                        <i class="lab-line-calendar"></i>
                        {{ $t('message.valid_from_to', { start: item.start_date, end: item.end_date }) }}
                    </p>
                </div>
            </component>
        </section>

        <section v-if="visibleRestaurants.length" class="offer-block__restaurants">
            <div v-if="variant === 'grid'" class="offer-block__grid">
                <RestaurantCardComponent
                    v-for="restaurant in visibleRestaurants"
                    :key="restaurant.id || restaurant.slug"
                    :restaurant="restaurant"
                    :offer-restaurant="offerRestaurant"
                />
            </div>
            <Swiper
                v-else
                :dir="dir"
                :loop="false"
                :speed="800"
                :nested="true"
                :pagination="{ clickable: true }"
                :modules="modules"
                :breakpoints="restaurantBreakPoints"
                class="offer-block__resto-swiper"
            >
                <SwiperSlide v-for="restaurant in visibleRestaurants" :key="restaurant.id || restaurant.slug">
                    <RestaurantCardComponent :restaurant="restaurant" :offer-restaurant="offerRestaurant"/>
                </SwiperSlide>
            </Swiper>
        </section>
    </article>
</template>

<script>
import {Pagination} from 'swiper/modules';
import {Swiper, SwiperSlide} from 'swiper/vue';
import RestaurantCardComponent from './RestaurantCardComponent.vue';

export default {
    name: 'OfferCampaignBlock',
    components: {RestaurantCardComponent, Swiper, SwiperSlide},
    props: {
        item: {type: Object, required: true},
        restaurants: {type: Array, default: () => []},
        offerRestaurant: {type: Object, default: () => ({})},
        giftFallback: {type: String, default: ''},
        dir: {type: String, default: 'ltr'},
        variant: {type: String, default: 'carousel'},
        linkable: {type: Boolean, default: true},
    },
    setup() {
        return {
            modules: [Pagination],
            restaurantBreakPoints: {
                0: {slidesPerView: 'auto', spaceBetween: 12},
                640: {slidesPerView: 2, spaceBetween: 16},
                768: {slidesPerView: 3, spaceBetween: 16},
                1024: {slidesPerView: 4, spaceBetween: 18},
            },
        };
    },
    computed: {
        routeType() {
            const t = this.item?.type;
            return (t === 'offer' || t === 'campaign') ? t : 'offer';
        },
        heroAttrs() {
            if (!this.linkable) return {};
            return {
                to: {name: 'frontend.offerAndCampaign', params: {slug: this.item.slug, type: this.routeType}},
            };
        },
        hasDiscount() {
            return this.routeType === 'offer' && parseFloat(this.item?.amount || 0) > 0;
        },
        displayTag() {
            const tag = String(this.item?.tag || '').trim();
            return tag || this.$t('message.exclusive_deal');
        },
        displayHeadline() {
            if (this.hasDiscount) {
                const amount = String(this.item.amount).replace(/\.0+$/, '');
                return this.$t('message.up_to_percent_off', {discount: amount});
            }
            return this.item?.title || '';
        },
        plainDescription() {
            let raw = String(this.item?.description || '');
            raw = raw.replace(/<[^>]+>/g, ' ');
            raw = raw
                .replace(/&nbsp;/gi, ' ')
                .replace(/&amp;/gi, '&')
                .replace(/&quot;/gi, '"')
                .replace(/&#39;|&apos;/gi, "'")
                .replace(/&lt;/gi, '<')
                .replace(/&gt;/gi, '>')
                .replace(/\s+/g, ' ')
                .trim();
            if (!raw) return '';
            return raw.length > 120 ? raw.slice(0, 117) + '…' : raw;
        },
        bannerStyle() {
            const cover = this.item?.cover;
            if (!cover) return {};
            return {backgroundImage: `url('${cover}')`};
        },
        visibleRestaurants() {
            const fromProp = Array.isArray(this.restaurants) ? this.restaurants : [];
            const fromItem = Array.isArray(this.item?.restaurants) ? this.item.restaurants : [];
            const list = fromProp.length ? fromProp : fromItem;
            return list.filter((r) => r && Number(r.id) > 0);
        },
    },
};
</script>

<style scoped>
.offer-block {
    overflow: hidden;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 28px rgba(16, 24, 40, 0.08);
}
.offer-block__hero {
    position: relative;
    min-height: 176px;
    background-color: #e7f6ec;
    background-size: cover;
    background-position: center;
}
.offer-block__shade {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(231, 246, 236, 0.96) 0%, rgba(231, 246, 236, 0.88) 48%, rgba(231, 246, 236, 0.58) 100%);
}
.offer-block__decor {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image:
        radial-gradient(circle at 86% 28%, rgba(22, 163, 74, 0.12) 0 22px, transparent 23px),
        radial-gradient(circle at 78% 72%, rgba(22, 163, 74, 0.08) 0 10px, transparent 11px);
}
.offer-block__hero-inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 18px;
    min-height: 176px;
    padding: 22px 72px 20px 22px;
    text-decoration: none;
    color: inherit;
}
.offer-block__art {
    flex-shrink: 0;
    width: 96px;
    height: 96px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.offer-block__gift {
    max-width: 96px;
    max-height: 96px;
    object-fit: contain;
    filter: drop-shadow(0 8px 12px rgba(20, 83, 45, 0.15));
}
.offer-block__copy {
    min-width: 0;
    flex: 1;
}
.offer-block__tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    padding: 4px 12px;
    border-radius: 999px;
    background: #14532d;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.offer-block__tag i {
    font-size: 10px;
}
.offer-block__title {
    margin: 0 0 6px;
    font-size: 28px;
    line-height: 1.15;
    font-weight: 800;
    color: #111827;
}
.offer-block__desc {
    margin: 0 0 10px;
    font-size: 14px;
    line-height: 1.45;
    color: #6b7280;
}
.offer-block__dates {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    padding: 5px 12px;
    border: 1px solid #86efac;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.7);
    font-size: 12px;
    font-weight: 600;
    color: #15803d;
}
.offer-block__restaurants {
    padding: 16px 16px 8px;
    background: #fff;
}
.offer-block__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    padding-bottom: 8px;
}
.offer-block__resto-swiper {
    padding-bottom: 28px;
}
.offer-block__resto-swiper :deep(.swiper-slide) {
    width: 220px;
    height: auto;
}
.offer-block__resto-swiper :deep(.swiper-pagination) {
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.offer-block__resto-swiper :deep(.swiper-pagination-bullet) {
    width: 8px;
    height: 8px;
    margin: 0 !important;
    opacity: 1;
    background: #bbf7d0;
    box-shadow: none;
    color: transparent;
    font-size: 0;
    line-height: 0;
}
.offer-block__resto-swiper :deep(.swiper-pagination-bullet-active) {
    width: 10px;
    height: 10px;
    background: #166534;
}
.offer-block__resto-swiper :deep(.swiper-pagination-lock) {
    display: none;
}
@media (min-width: 768px) {
    .offer-block__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }
}
@media (min-width: 1024px) {
    .offer-block__grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}
@media (max-width: 640px) {
    .offer-block__hero-inner {
        gap: 12px;
        padding: 16px 16px 14px;
        min-height: 148px;
    }
    .offer-block__art,
    .offer-block__gift {
        width: 64px;
        height: 64px;
        max-width: 64px;
        max-height: 64px;
    }
    .offer-block__title {
        font-size: 20px;
    }
    .offer-block__desc {
        font-size: 13px;
    }
    .offer-block__resto-swiper :deep(.swiper-slide) {
        width: 180px;
    }
}
</style>
