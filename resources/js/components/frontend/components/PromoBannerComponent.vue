<template>
    <router-link
        :to="{ name: 'frontend.offerAndCampaign', params: { slug: item.slug, type: routeType } }"
        class="promo-banner"
        :style="bannerStyle"
    >
        <div class="promo-banner__shade"></div>
        <div class="promo-banner__inner">
            <div class="promo-banner__art">
                <img :src="item.thumb || giftFallback" alt="" class="promo-banner__gift">
            </div>
            <div class="promo-banner__copy">
                <span class="promo-banner__tag">{{ displayTag }}</span>
                <h3 class="promo-banner__title">{{ displayHeadline }}</h3>
                <p v-if="plainDescription" class="promo-banner__desc">{{ plainDescription }}</p>
                <p v-if="item.start_date && item.end_date" class="promo-banner__dates">
                    {{ $t('message.valid_from_to', { start: item.start_date, end: item.end_date }) }}
                </p>
            </div>
        </div>
    </router-link>
</template>

<script>
export default {
    name: "PromoBannerComponent",
    props: {
        item: { type: Object, required: true },
        giftFallback: { type: String, default: '' },
        linkType: { type: String, default: '' },
    },
    computed: {
        routeType() {
            if (this.linkType) return this.linkType;
            const t = this.item?.type;
            return (t === 'offer' || t === 'campaign') ? t : 'offer';
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
                return this.$t('message.up_to_percent_off', { discount: amount });
            }
            return this.item?.title || '';
        },
        plainDescription() {
            const raw = String(this.item?.description || '')
                .replace(/<[^>]+>/g, ' ')
                .replace(/&nbsp;/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
            if (!raw) return '';
            return raw.length > 110 ? raw.slice(0, 107) + '…' : raw;
        },
        bannerStyle() {
            const cover = this.item?.cover;
            if (!cover) return {};
            return {
                backgroundImage: `url('${cover}')`,
            };
        },
    },
};
</script>

<style scoped>
.promo-banner {
    position: relative;
    display: block;
    overflow: hidden;
    min-height: 168px;
    border-radius: 18px;
    background-color: #e7f6ec;
    background-size: cover;
    background-position: center;
    color: #14532d;
    text-decoration: none;
}
.promo-banner__shade {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(231, 246, 236, 0.94) 0%, rgba(231, 246, 236, 0.82) 48%, rgba(231, 246, 236, 0.55) 100%);
}
.promo-banner__inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 16px;
    min-height: 168px;
    padding: 18px 22px;
}
.promo-banner__art {
    flex-shrink: 0;
    width: 88px;
    height: 88px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.promo-banner__gift {
    max-width: 88px;
    max-height: 88px;
    object-fit: contain;
    filter: drop-shadow(0 8px 12px rgba(20, 83, 45, 0.15));
}
.promo-banner__copy {
    min-width: 0;
    flex: 1;
}
.promo-banner__tag {
    display: inline-flex;
    align-items: center;
    margin-bottom: 6px;
    padding: 3px 10px;
    border-radius: 999px;
    background: #14532d;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.promo-banner__title {
    margin: 0 0 4px;
    font-size: 26px;
    line-height: 1.15;
    font-weight: 800;
    color: #14532d;
}
.promo-banner__desc {
    margin: 0 0 6px;
    font-size: 13px;
    line-height: 1.4;
    color: #166534;
}
.promo-banner__dates {
    margin: 0;
    font-size: 12px;
    font-weight: 600;
    color: #15803d;
}
@media (max-width: 640px) {
    .promo-banner__inner {
        gap: 12px;
        padding: 14px 16px;
        min-height: 148px;
    }
    .promo-banner__art {
        width: 64px;
        height: 64px;
    }
    .promo-banner__gift {
        max-width: 64px;
        max-height: 64px;
    }
    .promo-banner__title {
        font-size: 20px;
    }
}
</style>
