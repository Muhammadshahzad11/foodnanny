<template>
    <article class="resto-card group">
        <div class="resto-card__media">
            <span v-if="offerBadgeAmount" class="resto-card__badge">
                {{ $t('message.percentage_off', {discount : offerBadgeAmount }) }}
            </span>
            <button type="button" @click.prevent="favorite(restaurant, restaurant.favorite = !restaurant.favorite)" class="resto-card__heart">
                <i :class="restaurant.favorite ? 'lab-fill-heart text-primary' : 'lab-line-heart text-gray-500'"></i>
            </button>
            <div v-if="restaurant.status === enums.statusEnum.INACTIVE" class="resto-card__overlay">
                <h3 class="resto-card__overlay-title">{{ $t('label.temporary_closed') }}</h3>
            </div>
            <div v-if="restaurant.status === enums.statusEnum.ACTIVE && restaurant.availability === enums.availabilityEnum.CLOSE" class="resto-card__overlay">
                <h3 class="resto-card__overlay-title">{{ $t('label.close_now') }}</h3>
                <button type="button" @click.prevent="handleGoToRestaurant(restaurant)" class="resto-card__schedule">
                    {{ $t('label.schedule_order') }}
                </button>
            </div>
            <img @click.prevent="handleGoToRestaurant(restaurant)" :src="restaurant.thumb" :alt="restaurant.name" class="resto-card__image">
        </div>
        <div class="resto-card__body">
            <h3 @click.prevent="handleGoToRestaurant(restaurant)" class="resto-card__name">
                {{ restaurant.name }}
            </h3>
            <p v-if="cardDescription" class="resto-card__desc">{{ cardDescription }}</p>
            <p class="resto-card__meta">
                <span v-if="restaurant.distance" class="resto-card__meta-item">
                    <i class="lab-line-map"></i>
                    {{ restaurant.distance }} {{ $t('label.km') }}
                </span>
                <span v-if="restaurant.preparation_time" class="resto-card__meta-item">
                    <i class="lab-line-clock"></i>
                    {{ restaurant.preparation_time }} {{ $t('label.minute') }}
                </span>
            </p>
            <p v-if="ratingValue" class="resto-card__rating">
                <i class="lab-fill-star"></i>
                <span class="resto-card__rating-value">{{ ratingValue }}</span>
                <span class="resto-card__rating-divider"></span>
                <span class="resto-card__reviews">{{ ratingCount }} {{ $t('label.reviews') }}</span>
            </p>
        </div>
    </article>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum.js";
import availabilityEnum from "../../../enums/modules/availabilityEnum.js";
import router from "../../../router/index.js";
import {useFrontendFavoriteStore} from "../../../stores/frontendFavorite.js";

export default {
    name : "RestaurantCardComponent",
    props: {
        restaurant: Object,
        offerRestaurant: {
            type: Object,
            default: () => ({}),
            required: false
        }
    },
    setup() {
        const frontendFavoriteStore = useFrontendFavoriteStore();

        return {
            frontendFavoriteStore
        }
    },
    data() {
        return {
            enums: {
                statusEnum : statusEnum,
                availabilityEnum : availabilityEnum
            }
        }
    },
    computed: {
        offerBadgeAmount() {
            const offer = this.offerRestaurant?.[this.restaurant?.id];
            const amount = parseFloat(offer?.amount || 0);
            return amount > 0 ? offer.amount : 0;
        },
        cardDescription() {
            const raw = String(this.restaurant?.description || this.restaurant?.cuisine || '')
                .replace(/<[^>]+>/g, ' ')
                .replace(/&nbsp;/gi, ' ')
                .replace(/&amp;/gi, '&')
                .replace(/\s+/g, ' ')
                .trim();
            if (!raw) return '';
            return raw.length > 90 ? raw.slice(0, 87) + '…' : raw;
        },
        ratingCount() {
            return Number(this.restaurant?.rating_star_count || 0);
        },
        ratingValue() {
            const total = Number(this.restaurant?.rating_star || 0);
            if (total <= 0 || this.ratingCount <= 0) return '';
            return (total / this.ratingCount).toFixed(1);
        },
    },
    methods: {
        handleGoToRestaurant: function(restaurant) {
            if(restaurant.status === this.enums.statusEnum.ACTIVE) {
                this.$router.push({
                    name: 'frontend.singleRestaurant',
                    params: {
                        slug: restaurant.slug
                    }
                });
            }
        },
        favorite: function (restaurant, toggle) {
            this.frontendFavoriteStore.toggle({
                restaurant_id: restaurant.id,
                toggle: toggle
            }).then((res) => {
            }).catch((err) => {
                if (err.response.status === 401) {
                    restaurant.favorite = false;
                    router.push({name: "auth.login"});
                }
            })
        }
    }
}
</script>

<style scoped>
.resto-card {
    display: block;
    overflow: hidden;
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 16px;
    box-shadow: 0 8px 18px rgba(16, 24, 40, 0.06);
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}
.resto-card:hover {
    border-color: #86efac;
    box-shadow: 0 12px 24px rgba(16, 24, 40, 0.1);
}
.resto-card__media {
    position: relative;
    overflow: hidden;
}
.resto-card__image {
    display: block;
    width: 100%;
    height: 148px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.4s ease;
}
.resto-card:hover .resto-card__image {
    transform: scale(1.05);
}
.resto-card__badge {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 20;
    padding: 4px 8px;
    border-radius: 8px;
    background: #16a34a;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}
.resto-card__heart {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 20;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(16, 24, 40, 0.12);
    cursor: pointer;
}
.resto-card__overlay {
    position: absolute;
    inset: 0;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.58);
    padding: 10px;
}
.resto-card__overlay-title {
    margin: 0 0 8px;
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    text-align: center;
}
.resto-card__schedule {
    padding: 6px 12px;
    border-radius: 999px;
    background: #fff;
    color: #16a34a;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}
.resto-card__body {
    padding: 12px 12px 14px;
    background: #fff;
}
.resto-card__name {
    margin: 0 0 6px;
    color: #14532d;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.3;
    cursor: pointer;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.resto-card__desc {
    margin: 0 0 8px;
    min-height: 32px;
    color: #6b7280;
    font-size: 12px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.resto-card__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    margin: 0 0 8px;
    color: #6b7280;
    font-size: 12px;
}
.resto-card__meta-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.resto-card__meta-item i {
    color: #16a34a;
    font-size: 13px;
}
.resto-card__rating {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    font-size: 12px;
}
.resto-card__rating i {
    color: #f59e0b;
}
.resto-card__rating-value {
    color: #111827;
    font-weight: 600;
}
.resto-card__rating-divider {
    width: 1px;
    height: 12px;
    background: #d1d5db;
}
.resto-card__reviews {
    color: #16a34a;
    font-weight: 600;
}
</style>
