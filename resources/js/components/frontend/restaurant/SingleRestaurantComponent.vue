<template>
    <div v-if="isMenuFixed && carts.length > 0" @click.prevent="openCanvas('cart-canvas')"
        class="fixed lg:block hidden top-1/2 -translate-y-1/2 ltr:right-0 rtl:left-0 z-30 overflow-hidden cursor-pointer restaurant-float-cart">
        <div class="flex flex-col items-center justify-center text-center gap-1 py-2.5 px-4 bg-primary text-white">
            <i class="lab-fill-bag text-2xl leading-none"></i>
            <span class="text-sm font-medium">{{ carts.length }} {{ $t('label.items') }}</span>
        </div>
        <span class="text-sm font-semibold py-2.5 px-4 bg-heading text-white w-full block text-center">
            {{
                currencyFormat(subtotal, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol,
                    setting.site_currency_position)
            }}
        </span>
    </div>

    <section class="restaurant-page mb-24 md:mb-14">
        <div class="container">
            <DineInBannerComponent
                :visible="isDineInSession"
                :table-label="dineInContextStore.tableLabel"
                :zone="dineInContextStore.context?.zone || ''"
                :allow-exit="true"
                @exit="exitDineIn"/>

            <div class="restaurant-hero mb-5 sm:mb-8">
                <img :src="restaurant.cover" alt="" class="restaurant-hero__media">
                <div class="restaurant-hero__shade" aria-hidden="true"></div>
                <div class="restaurant-hero__copy">
                    <div class="restaurant-hero__brand">
                        <img :src="restaurant.logo" alt="" class="restaurant-hero__logo">
                        <span
                            :class="restaurant.availability === enums.availabilityEnum.OPEN
                                ? 'restaurant-hero__status restaurant-hero__status--open'
                                : 'restaurant-hero__status restaurant-hero__status--closed'">
                            <span class="restaurant-hero__dot"></span>
                            {{
                                restaurant.availability === enums.availabilityEnum.OPEN
                                    ? $t('label.open_now')
                                    : $t('label.close_now')
                            }}
                        </span>
                    </div>
                    <h1 class="restaurant-hero__title">
                        <span>{{ restaurant.name }}</span>
                        <i class="lab-fill-verify restaurant-hero__verified" aria-hidden="true"></i>
                    </h1>
                    <p class="restaurant-hero__meta">
                        <template v-if="restaurant.cuisine">{{ restaurant.cuisine }} • </template>
                        {{ restaurant.distance }} {{ $t('label.km') }} {{ $t('label.away') }}
                    </p>
                    <p class="restaurant-hero__intro">{{ $t('label.restaurant_intro') }}</p>
                    <div class="restaurant-hero__stats">
                        <div class="restaurant-hero__stat">
                            <i class="lab-line-clock"></i>
                            <span>
                                <small>{{ $t('label.todays_timing') }}</small>
                                <strong>{{ restaurant.single_time_slots || restaurant.today }}</strong>
                            </span>
                        </div>
                        <span class="restaurant-hero__stat-line" aria-hidden="true"></span>
                        <div class="restaurant-hero__stat">
                            <i class="lab-fill-bag"></i>
                            <span>
                                <small>{{ $t('label.min_order') }}</small>
                                <strong>{{ minimumOrderLimit || '—' }}</strong>
                            </span>
                        </div>
                    </div>
                    <nav class="restaurant-hero__actions">
                        <button type="button" @click.prevent="openInfoModal" class="restaurant-hero__btn restaurant-hero__btn--primary">
                            <i class="lab-fill-call"></i>
                            <span>{{ $t('label.contact_info') }}</span>
                        </button>
                        <button
                            type="button"
                            :aria-label="$t('label.save')"
                            @click.prevent="favorite(restaurant, restaurant.favorite = !restaurant.favorite)"
                            class="restaurant-hero__btn restaurant-hero__btn--icon"
                        >
                            <i :class="restaurant.favorite ? 'lab-fill-heart' : 'lab-line-heart'"></i>
                        </button>
                        <button
                            type="button"
                            :aria-label="$t('label.share')"
                            @click.prevent="shareRestaurant"
                            class="restaurant-hero__btn restaurant-hero__btn--icon"
                        >
                            <svg class="restaurant-hero__share-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="18" cy="5" r="2.6" fill="currentColor"/>
                                <circle cx="6" cy="12" r="2.6" fill="currentColor"/>
                                <circle cx="18" cy="19" r="2.6" fill="currentColor"/>
                                <path d="M8.4 10.8l7.2-4.2M8.4 13.2l7.2 4.2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </nav>
                </div>
                <div class="restaurant-hero__rating">
                    <div class="restaurant-hero__rating-score">
                        <i class="lab-fill-star-round"></i>
                        <strong>{{ ratingAverage || '0.0' }}</strong>
                        <span>/ 5</span>
                    </div>
                    <small>({{ restaurant.rating_star_count || 0 }} {{ (restaurant.rating_star_count || 0) === 1 ? $t('label.review') : $t('label.reviews') }})</small>
                </div>
            </div>

            <section v-if="pageHighlights.length" class="restaurant-highlights mb-5 sm:mb-8">
                <h2 class="restaurant-section-title">
                    <svg class="restaurant-section-title__leaf" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M17.5 3.2c-4.3.2-8.4 2.4-10.6 6.1-1.7 2.8-2 6.3-.7 9.3.2.5.9.6 1.3.2 1.8-1.8 3.1-4.1 3.6-6.7 2.7 1.6 4.6 4.2 5.2 7.3.1.6.8.9 1.3.5C21.3 16.8 22.4 9.6 17.5 3.2Z"/>
                    </svg>
                    {{ $t('label.restaurant_highlights') }}
                </h2>
                <div class="restaurant-highlights__track">
                    <article
                        v-for="item in pageHighlights"
                        :key="item.key"
                        :class="['restaurant-highlight', 'restaurant-highlight--' + item.key]"
                    >
                        <span class="restaurant-highlight__icon">
                            <i :class="item.icon"></i>
                        </span>
                        <p class="restaurant-highlight__title">{{ highlightTitle(item) }}</p>
                        <p v-if="highlightSubtitle(item)" class="restaurant-highlight__sub">{{ highlightSubtitle(item) }}</p>
                    </article>
                </div>
            </section>

            <aside v-if="showImportantNotice" class="restaurant-notice mb-5 sm:mb-8">
                <div class="restaurant-notice__icon shrink-0">!</div>
                <div class="min-w-0 flex-1">
                    <p class="restaurant-notice__title">{{ $t('label.important_notice') }}</p>
                    <p class="restaurant-notice__body">{{ noticeBody }}</p>
                    <p v-if="noticeEmphasis" class="restaurant-notice__emphasis">{{ noticeEmphasis }}</p>
                </div>
                <div class="restaurant-notice__art" aria-hidden="true">
                    <i class="lab-fill-bag"></i>
                    <i class="lab-line-shield"></i>
                </div>
            </aside>

            <div v-if="coupons.length > 0" class="mb-7 sm:mb-10 overflow-hidden">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="restaurant-section-title mb-0">
                        <i class="lab-line-coupon"></i>
                        {{ $t('label.exclusive_deals') }}
                    </h2>
                    <button type="button" class="restaurant-deals__all" @click.prevent="openCouponModal(coupons[0].id)">
                        {{ $t('label.view_all_deals') }} →
                    </button>
                </div>
                <Swiper :dir="displayMode" :loop="false" :speed="800" :navigation="true" :modules="modules" :breakpoints="couponBreakPoints" class="middle-navigate restaurant-deals !overflow-hidden">
                    <SwiperSlide @click.prevent="openCouponModal(coupon.id)" v-for="coupon in coupons" :key="coupon.id" class="!w-[85%] xs:!w-72 sm:!w-auto mobile:!w-64 cursor-pointer">
                        <div class="restaurant-deal h-full">
                            <span class="restaurant-deal__icon">
                                <i v-if="coupon.type === enums.discountEnum.DEFAULT" :class="coupon.discount_type === enums.discountTypeEnum.PERCENTAGE ? 'lab-line-offers' : 'lab-line-coupon'"></i>
                                <i v-else class="lab-line-bike"></i>
                            </span>
                            <div class="min-w-0">
                                <p v-if="coupon.type === enums.discountEnum.DEFAULT" class="restaurant-deal__amount">
                                    {{ coupon.discount_alt }} {{ $t('label.off') }}
                                </p>
                                <p v-else class="restaurant-deal__amount">{{ textShortener(coupon.name, 18) }}</p>
                                <p class="restaurant-deal__code">{{ coupon.code }}</p>
                                <p v-if="coupon.type === enums.discountEnum.DEFAULT" class="restaurant-deal__copy">
                                    {{
                                        coupon.minimum_order > 0 ? $t('message.discount_off_above', {
                                            discount: coupon.discount_alt,
                                            min_order: coupon.minimum_order_currency_amount
                                        }) : $t('message.discount_off', { discount: coupon.discount_alt })
                                    }}
                                </p>
                                <p v-else class="restaurant-deal__copy">
                                    {{
                                        coupon.minimum_order > 0 ? textShortener($t('message.discount_delivery_off_above',
                                            { min_order: coupon.minimum_order_currency_amount }), 48) :
                                            $t('message.discount_delivery_off')
                                    }}
                                </p>
                                <p class="restaurant-deal__use">
                                    <i class="lab-fill-check"></i>
                                    {{ $t('label.use_in_checkout') }}
                                </p>
                            </div>
                        </div>
                    </SwiperSlide>
                </Swiper>
            </div>

            <div
                :class="isMenuFixed ? 'restaurant-menu-bar--fixed' : 'restaurant-menu-bar'"
                v-if="categoryWiseItems && categoryWiseItems.length"
            >
                <div :class="isMenuFixed ? 'container' : 'w-full'">
                    <div :class="isMenuFixed ? 'mb-3' : 'mb-5'" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <router-link v-if="isMenuFixed" :to="{ name: 'frontend.restaurant' }" class="text-heading hover:text-primary">
                                <i class="lab-line-chevron-left text-lg font-bold"></i>
                            </router-link>
                            <div class="min-w-0">
                                <p v-if="!isMenuFixed" class="text-xs font-semibold uppercase tracking-[0.14em] text-primary mb-1">Menu</p>
                                <h2 class="text-xl sm:text-2xl font-semibold capitalize text-heading truncate">
                                    {{ isMenuFixed ? restaurant.name : $t('label.restaurant_menu') }}
                                </h2>
                            </div>
                        </div>

                        <nav class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
                            <button
                                @click.prevent="itemType === enums.itemTypeEnum.NON_VEG ? itemType = null : itemType = enums.itemTypeEnum.NON_VEG"
                                :class="itemType === enums.itemTypeEnum.NON_VEG ? 'restaurant-filter--on' : 'restaurant-filter'"
                                class="inline-flex shrink-0 items-center gap-2 h-9 px-3 transition">
                                <img :src="setting.image_non_vag" alt="food-type" class="h-4 drop-shadow-mealtype">
                                <span class="capitalize text-sm font-medium">{{ $t('label.non_veg') }}</span>
                            </button>
                            <button
                                @click.prevent="itemType === enums.itemTypeEnum.VEG ? itemType = null : itemType = enums.itemTypeEnum.VEG"
                                :class="itemType === enums.itemTypeEnum.VEG ? 'restaurant-filter--on' : 'restaurant-filter'"
                                class="inline-flex shrink-0 items-center gap-2 h-9 px-3 transition">
                                <img :src="setting.image_vag" alt="food-type" class="h-4 drop-shadow-mealtype">
                                <span class="capitalize text-sm font-medium">{{ $t('label.veg') }}</span>
                            </button>
                            <form @submit.prevent="search"
                                class="group flex h-9 w-full sm:w-auto sm:min-w-[180px] sm:max-w-[260px] flex-1 items-center gap-2 px-3 restaurant-search focus-within:ring-1 focus-within:ring-primary/40">
                                <button type="submit" class="lab-line-search text-lg shrink-0 text-paragraph"></button>
                                <input type="search" @keyup="search" v-model="searchItem" :placeholder="$t('label.search_in_menu')"
                                    class="w-full bg-transparent text-sm placeholder:text-paragraph/70 outline-none">
                                <button type="button" @click.prevent="searchReset"
                                    class="lab-fill-close-circle text-danger opacity-0 group-focus-within:opacity-100 transition"></button>
                            </form>
                        </nav>
                    </div>

                    <Swiper :dir="displayMode" :speed="700" :spaceBetween="8" :navigation="true" :modules="modules"
                        slidesPerView="auto" class="middle-navigate menu-categories restaurant-cats">
                        <SwiperSlide v-for="(categoryWiseItem, categoryWiseItemIndex) in categoryWiseItems"
                            :key="categoryWiseItemIndex"
                            :class="{ 'restaurant-cat--active': currentSectionId === categoryWiseItem.slug }"
                            class="restaurant-cat !w-fit"
                            @click="handleMenuCategory($event, categoryWiseItem.slug)">
                            {{ categoryWiseItem.name }}
                        </SwiperSlide>
                    </Swiper>
                </div>
            </div>
            <div class="text-center text-paragraph py-10" v-else>
                <h3 class="sm:text-xl font-medium capitalize">{{ $t('message.no_menu_available') }}</h3>
            </div>

            <dl v-if="searchItem.length > 0" class="mt-8 restaurant-section">
                <dt class="mb-5 text-2xl font-semibold capitalize text-heading">
                    {{ $t("message.we_found", { length: searchItems.length, search: searchItem }) }}
                </dt>
                <dd class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 items-stretch restaurant-menu-grid restaurant-menu-grid--search">
                    <ItemComponent :offer="checkOffer" :type="itemType" :itemIndex="100000" :items="searchItems" />
                </dd>
            </dl>

            <dl v-for="(categoryWiseItem, categoryWiseItemIndex) in categoryWiseItems" :key="categoryWiseItemIndex"
                :id="categoryWiseItem.slug" class="mt-10 restaurant-section">
                <dt class="mb-5 flex items-center gap-3">
                    <span class="h-6 w-1 bg-primary"></span>
                    <span class="text-2xl font-semibold capitalize text-heading">{{ categoryWiseItem.name }}</span>
                </dt>
                <dd class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 items-stretch restaurant-menu-grid">
                    <div v-if="categoryWiseItem.items.length === 0" class="text-lg font-normal text-paragraph">{{ $t('message.item_not_found') }}</div>
                    <div v-else-if="categoryWiseItem.items.filter(item => itemType == null || item.item_type === itemType).length === 0" class="text-lg font-normal text-paragraph">{{ $t('message.item_not_found') }}</div>
                    <ItemComponent v-if="categoryWiseItem.items.length > 0" :offer="checkOffer" :type="itemType" :itemIndex="categoryWiseItemIndex" :items="categoryWiseItem.items" />
                </dd>
            </dl>

            <article v-if="restaurant.id" class="mt-10 rounded-2xl border border-gray-100 bg-white p-4 sm:p-5">
                <h3 class="text-lg font-semibold capitalize text-heading mb-2">{{ restaurant.name }}</h3>
                <p v-if="restaurant.address" class="flex items-start gap-2 text-sm text-paragraph mb-2">
                    <i class="lab-fill-location text-base mt-0.5"></i>
                    <span>{{ restaurant.address }}</span>
                </p>
                <p v-if="restaurant.phone" class="flex items-center gap-2 text-sm text-paragraph mb-3">
                    <i class="lab-fill-call text-base"></i>
                    <span>{{ restaurant.country_code }}{{ restaurant.phone }}</span>
                </p>
                <p v-if="restaurant.fssai_number" class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-paragraph">
                    <i class="lab-line-shield text-base"></i>
                    <span>{{ $t('label.fssai_number') }}: {{ restaurant.fssai_number }}</span>
                </p>
            </article>
        </div>
    </section>

    <Teleport to="body">
    <div id="coupon-info-modal"
        class="fixed inset-0 z-[120] p-3 w-screen h-dvh overflow-y-auto bg-black/55 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-2xl mx-auto bg-white transition-all duration-300 shadow-2xl">
            <div class="flex items-center justify-between gap-4 py-4 px-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.offer_details') }}</h3>
                <button @click.prevent="closeCouponModal" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>
            <div class="px-6 py-4">
                <h3 class="flex items-center gap-1.5 mb-4">
                    <i v-if="coupon.type === enums.discountEnum.DEFAULT" :class="coupon.discount_type === enums.discountTypeEnum.PERCENTAGE ? 'lab-line-offers' : 'lab-line-coupon'" class="text-xl text-primary"></i>
                    <i v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="lab-line-bike text-xl text-primary"></i>
                    <span v-if="coupon.type === enums.discountEnum.DEFAULT" class="text-xl font-semibold">
                        {{ coupon.discount_alt }} {{ $t('label.off') }} ({{ coupon.code }})
                    </span>
                    <span v-if="coupon.type === enums.discountEnum.FREE_DELIVERY" class="text-xl font-semibold">
                        {{ coupon.name }} ({{ coupon.code }})
                    </span>
                </h3>
                <p class="text-sm mb-1 text-heading">{{ $t('message.new_and_existing_customers') }}</p>
                <p class="text-sm mb-4 text-heading">{{ $t('message.valid_for_all_items') }}</p>
                <p class="text-sm mb-4 text-heading">{{ $t('label.valid_from') }} {{ coupon.convert_start_date }} - {{ coupon.convert_end_date }}</p>
                <p v-if="coupon.minimum_order > 0" class="text-sm mb-1 text-heading">{{ $t('label.minimum_order') }} {{ coupon.minimum_order_currency_amount }}</p>
                <p v-if="coupon.maximum_discount > 0" class="text-sm mb-1 text-heading">{{$t('label.maximum_discount') }} {{ coupon.maximum_discount_currency_amount }}</p>
                <p class="text-sm mb-5 font-medium text-primary">{{ $t('label.use_in_checkout') }}</p>
                <button v-if="coupon.description" @click.prevent="handleSlide('coupon-terms-description-list')" class="flex items-center gap-2 transition-all duration-300 hover:text-primary">
                    <span class="text-base font-medium capitalize">{{ $t('label.terms_and_conditions') }}</span>
                    <i :class="toggleSlide ? 'rotate-180' : 'rotate-0'" class="lab-line-chevron-down font-semibold text-primary"></i>
                </button>
                <div v-if="coupon.description" id="coupon-terms-description-list"
                    class="h-0 overflow-hidden transition-all duration-300">
                    <div class="ql-ul-set editor-show-design px-2 py-1 overflow-hidden transition-all duration-300 text-xs"
                        v-html="coupon.description"></div>
                </div>
            </div>
        </div>
    </div>
    </Teleport>

    <Teleport to="body">
    <div id="more-information"
        class="fixed inset-0 z-[120] p-3 w-screen h-dvh overflow-y-auto bg-black/55 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-2xl mx-auto bg-white transition-all duration-300 max-w-2xl shadow-2xl">
            <div class="flex items-center justify-between gap-4 py-4 px-6">
                <h3 class="text-lg font-semibold capitalize">{{ restaurant.name }}</h3>
                <button @click.prevent="closeInfoModal" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>

            <nav class="flex items-center px-6 py-2 border-b border-gray-100">
                <button @click.prevent="handleTab($event, 'information')"
                    class="tab-button tab-active text-sm capitalize rounded-2xl px-4 h-8 text-primary">
                    {{ $t('label.information') }}
                </button>
                <button @click.prevent="handleTab($event, 'reviews')"
                    class="tab-button text-sm capitalize rounded-2xl px-4 h-8 text-primary">
                    {{ $t('label.reviews') }}
                </button>
            </nav>

            <div id="information" class="tab-content tab-active p-6">
                <MapComponent v-if="mapShow" :location="{ lat: restaurant.latitude, lng: restaurant.longitude }"
                    :setting="{ autocomplete: false, mouseEvent: false, currentLocation: false }" />
                <ul class="w-full block">
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-location text-2xl text-paragraph"></i>
                        <span>{{ restaurant.address }}</span>
                    </li>
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-mail text-2xl text-paragraph"></i>
                        <span>{{ restaurant.email }}</span>
                    </li>
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-call text-2xl text-paragraph"></i>
                        <span v-if="restaurant.phone" class="db-list-item-text w-full sm:w-1/2">{{
                            restaurant.country_code + "" + restaurant.phone
                        }}</span>
                    </li>
                    <li v-if="restaurant.fssai_number" class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-line-shield text-2xl text-paragraph"></i>
                        <span>{{ $t('label.fssai_number') }}: {{ restaurant.fssai_number }}</span>
                    </li>
                    <li class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <i class="lab-fill-clock text-2xl text-paragraph"></i>
                        <span>{{ $t('label.opening_time') }}</span>
                    </li>
                </ul>

                <ul class="w-full ltr:pl-9 rtl:pr-9 flex flex-col" v-if="enums.dayEnum.length > 0">
                    <li class="flex flex-col sm:flex-row" v-for="dayEnum in enums.dayEnum"
                    :key="dayEnum.id">
                        <p class="flex-shrink-0 w-24 mt-5 capitalize text-heading">
                            {{ dayEnum.name }}
                        </p>

                        <div class="flex items-center mr-4 flex-wrap">
                            <div class="flex items-center flex-wrap" v-if="restaurant.time_slots && restaurant.time_slots.filter(slot => slot.day === dayEnum.id).length > 0">
                                <div class="relative flex items-start gap-8 py-2 px-3 rounded-lg border border-[#EFF0F6] time-slot-gap"
                                    v-for="timeSlot in restaurant.time_slots.filter(slot => slot.day === dayEnum.id)"
                                    :key="timeSlot.id || (timeSlot.opening_time + '-' + timeSlot.closing_time)">
                                    <p class="text-paragraph">
                                        {{ timeSlot.opening_time + ' - ' + timeSlot.closing_time }}
                                    </p>
                                </div>
                            </div>
                            <div class="relative flex items-start gap-8 py-2 px-3 rounded-lg border border-[#EFF0F6] time-slot-gap" v-else>
                                <p class="text-paragraph">
                                    {{ $t('label.closed') }}
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div id="reviews" class="tab-content p-6">
                <h3 class="text-2xl font-medium capitalize mb-3">{{ $t('label.rating_and_reviews') }}</h3>

                <div v-if="restaurant.rating_star > 0 && restaurant.rating_star_count > 0"
                    class="flex items-baseline gap-1 mb-5">
                    <i class="lab-fill-star-round text-xl text-amber-500"></i>
                    <span class="text-lg font-medium">
                        {{ (restaurant.rating_star / restaurant.rating_star_count).toFixed(1) }}
                    </span>
                    <small class="text-sm capitalize text-paragraph">
                        ({{ restaurant.rating_star_count }}
                        {{ restaurant.rating_star_count > 1 ? $t('label.reviews') : $t('label.review') }})
                    </small>
                </div>
                <p class="text-sm text-heading mt-2" v-else>{{ $t('message.no_review_available') }}</p>

                <ul class="w-full flex flex-col gap-4">
                    <li v-for="(review, index) in restaurant.reviews" :key="index"
                        class="p-3 rounded-lg border border-gray-100">
                        <h4 class="text-lg font-medium capitalize mb-1">{{ review.name }}</h4>
                        <div class="flex items-center gap-0.5 mb-3">
                            <i :class="review.star >= 1 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 2 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 3 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 4 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <i :class="review.star >= 5 ? 'text-amber-500' : 'text-slate-500'"
                                class="lab-fill-star-round text-sm"></i>
                            <span class="text-xs text-paragraph">{{ review.created_at }}</span>
                        </div>
                        <p class="text-sm text-heading">{{ review.review }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </Teleport>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { useModal } from "../../../composables/modal.js";
import { useTab } from "../../../composables/tab.js";
import { useSlide } from "../../../composables/slide.js";
import { Navigation } from 'swiper/modules';
import dayEnum from "../../../enums/modules/dayEnum.js";
import availabilityEnum from "../../../enums/modules/availabilityEnum.js";
import appService from "../../../services/appService.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import { useCommonStore } from "../../../stores/common.js";
import DisplayModeEnum from "../../../enums/modules/displayModeEnum.js";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum.js";
import discountTypeEnum from "../../../enums/modules/discountTypeEnum.js";
import discountEnum from "../../../enums/modules/discountEnum.js";
import { useFrontendCouponStore } from "../../../stores/frontendCoupon.js";
import { useFrontendRestaurantStore } from "../../../stores/frontendRestaurant.js";
import { useFrontendItemCategoryStore } from "../../../stores/frontendItemCategory.js";
import ItemComponent from "../components/ItemComponent.vue";
import DineInBannerComponent from "../components/DineInBannerComponent.vue";
import { useFrontendItemStore } from "../../../stores/frontendItem.js";
import { useFrontendFavoriteStore } from "../../../stores/frontendFavorite.js";
import router from "../../../router/index.js";
import MapComponent from "../../common/MapComponent.vue";
import { useCanvas } from "../../../composables/canvas.js";
import { useFrontendCartStore } from "../../../stores/frontendCart.js";
import { useDineInContextStore } from "../../../stores/dineInContext.js";
import { useFrontendOfferStore } from "../../../stores/frontendOffer.js";
import alertService from "../../../services/alertService.js";


export default {
    name: "SingleRestaurantComponent",
    components: {
        MapComponent,
        ItemComponent,
        DineInBannerComponent,
        Swiper,
        SwiperSlide,
    },
    setup() {
        const { openModal, closeModal } = useModal();
        const { handleTab } = useTab();
        const { openCanvas } = useCanvas();
        const { handleSlide, toggleSlide } = useSlide(false);
        const commonStore = useCommonStore();
        const frontendCartStore = useFrontendCartStore();
        const dineInContextStore = useDineInContextStore();
        const frontendItemStore = useFrontendItemStore();
        const frontendOfferStore = useFrontendOfferStore();
        const frontendCouponStore = useFrontendCouponStore();
        const frontendSettingStore = useFrontendSettingStore();
        const frontendFavoriteStore = useFrontendFavoriteStore();
        const frontendRestaurantStore = useFrontendRestaurantStore();
        const frontendItemCategoryStore = useFrontendItemCategoryStore();

        return {
            handleTab,
            openModal,
            closeModal,
            openCanvas,
            handleSlide,
            toggleSlide,
            commonStore,
            frontendCartStore,
            dineInContextStore,
            frontendItemStore,
            frontendOfferStore,
            frontendCouponStore,
            frontendSettingStore,
            frontendFavoriteStore,
            frontendRestaurantStore,
            frontendItemCategoryStore,
            modules: [Navigation]
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                dayEnum: dayEnum,
                availabilityEnum: availabilityEnum,
                itemTypeEnum: itemTypeEnum,
                discountTypeEnum: discountTypeEnum,
                discountEnum: discountEnum
            },
            minimumOrderLimit: 0,
            mapShow: false,
            isMenuFixed: false,
            itemType: null,
            searchItem: "",
            currentSectionId: null,
            couponBreakPoints: {
                0: { slidesPerView: 'auto', spaceBetween: 16 },
                640: { slidesPerView: 2, spaceBetween: 24 },
                768: { slidesPerView: 3, spaceBetween: 24 },
                1024: { slidesPerView: 4, spaceBetween: 24 }
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        restaurant: function () {
            return this.frontendRestaurantStore.show;
        },
        showImportantNotice: function () {
            return this.restaurant?.show_important_notice !== false;
        },
        noticeBody: function () {
            return this.restaurant?.important_notice || this.$t('message.restaurant_platform_disclaimer');
        },
        noticeEmphasis: function () {
            if (this.restaurant?.important_notice_emphasis) {
                return this.restaurant.important_notice_emphasis;
            }
            return this.restaurant?.important_notice ? '' : this.$t('message.restaurant_contact_disclaimer');
        },
        pageHighlights: function () {
            if (this.restaurant?.show_highlights === false) {
                return [];
            }
            return Array.isArray(this.restaurant?.highlights) ? this.restaurant.highlights : [];
        },
        ratingAverage: function () {
            if (!this.restaurant?.rating_star_count) {
                return '';
            }
            return (this.restaurant.rating_star / this.restaurant.rating_star_count).toFixed(1);
        },
        categoryWiseItems: function () {
            return this.frontendItemCategoryStore.categoryWiseItems;
        },
        searchItems: function () {
            return this.frontendItemStore.searchItems;
        },
        displayMode: function () {
            return this.commonStore.display_mode === DisplayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        coupon: function () {
            return this.frontendCouponStore.show;
        },
        coupons: function () {
            return this.frontendCouponStore.lists;
        },
        carts: function () {
            return this.frontendCartStore.lists;
        },
        subtotal: function () {
            return this.frontendCartStore.subtotal;
        },
        checkOffer: function () {
            return this.frontendOfferStore.check;
        },
        isDineInSession: function () {
            return this.dineInContextStore.isActive
                && this.dineInContextStore.matchesRestaurant(this.$route.params.slug);
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleMenuFixed);
        this.syncDineInContext();
        if (typeof this.$route.params.slug !== "undefined") {
            this.loading.isActive = true;
            this.frontendRestaurantStore.view({
                slug: this.$route.params.slug,
                search: {
                    latitude: this.commonStore.latitude,
                    longitude: this.commonStore.longitude
                }
            }).then(res => {
                this.frontendOfferStore.fetchCheck({
                    id: res.data.data.id,
                    latitude: this.commonStore.latitude,
                    longitude: this.commonStore.longitude
                });
                this.frontendCouponStore.fetch(res.data.data.id);
                this.syncDineInContext();
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            this.frontendItemCategoryStore.fetchCategoryWiseItems({
                slug: this.$route.params.slug,
                order_column: "sort",
                order_type: "asc"
            }).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    },
    methods: {
        syncDineInContext: function () {
            if (!this.dineInContextStore.isActive) {
                return;
            }
            if (!this.dineInContextStore.matchesRestaurant(this.$route.params.slug)) {
                this.dineInContextStore.clear();
                this.frontendCartStore.clearDineInContext();
                return;
            }
            this.frontendCartStore.applyDineInContext(this.dineInContextStore.context);
        },
        exitDineIn: function () {
            this.dineInContextStore.clear();
            this.frontendCartStore.clearDineInContext();
        },
        textShortener: function (text, number) {
            return appService.textShortener(text, number);
        },
        highlightTitle: function (item) {
            if (item?.title) {
                return item.title;
            }
            const key = 'label.highlight_' + item.key;
            const translated = this.$t(key);
            return translated === key ? item.key : translated;
        },
        highlightSubtitle: function (item) {
            if (item?.key === 'cuisines' && this.restaurant?.cuisine) {
                return this.restaurant.cuisine;
            }
            if (item?.key === 'loved_by_customers' && this.restaurant?.rating_star_count > 0) {
                return this.$t('label.happy_foodies', { count: this.restaurant.rating_star_count });
            }
            const key = 'label.highlight_' + item.key + '_sub';
            const translated = this.$t(key);
            return translated === key ? '' : translated;
        },
        shareRestaurant: function () {
            const url = window.location.href;
            const title = this.restaurant?.name || document.title;
            const copyLink = () => {
                const fallback = () => {
                    const input = document.createElement('textarea');
                    input.value = url;
                    input.setAttribute('readonly', '');
                    input.style.position = 'fixed';
                    input.style.opacity = '0';
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                };
                const done = () => alertService.success(this.$t('message.link_copied'));
                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(url).then(done).catch(() => {
                        fallback();
                        done();
                    });
                    return;
                }
                fallback();
                done();
            };
            if (typeof navigator.share === 'function') {
                navigator.share({ title, text: title, url }).catch((err) => {
                    if (err?.name !== 'AbortError') {
                        copyLink();
                    }
                });
                return;
            }
            copyLink();
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        handleMenuFixed: function () {
            let scrollHeight = this.coupons.length > 0 ? 920 : 620;
            this.isMenuFixed = window.scrollY > scrollHeight;
            let section = document.querySelectorAll("dl");
            section.forEach((sec) => {
                let top = window.scrollY;
                let offset = sec.offsetTop - 150;
                let height = sec.offsetHeight;
                let id = sec.getAttribute("id");
                if (top >= offset && top < offset + height) {
                    this.currentSectionId = id;
                }
            });
        },
        openCouponModal: function (id) {
            this.loading.isActive = true;
            this.frontendCouponStore.view(id).then(res => {
                this.loading.isActive = false;
                this.openModal('coupon-info-modal');
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        closeCouponModal: function () {
            this.closeModal('coupon-info-modal');
        },
        openInfoModal: function () {
            this.mapShow = true;
            this.openModal('more-information');
        },
        closeInfoModal: function () {
            this.mapShow = false;
            this.closeModal('more-information');
        },
        search: function () {
            let scrollHeight = this.coupons.length > 0 ? 500 : 350;
            window.scrollTo(0, scrollHeight);

            if (typeof this.$route.params.slug !== "undefined") {
                this.frontendItemStore.fetchSearchItems({
                    slug: this.$route.params.slug,
                    name: this.searchItem,
                }).then(res => {
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                })
            }
        },
        searchReset: function () {
            this.searchItem = "";
        },
        handleMenuCategory: function (event, id) {
            let scrollDiv = document.getElementById(id).offsetTop - 120 - (!this.isMenuFixed ? 200 : 0);
            window.scrollTo({ top: scrollDiv, behavior: 'smooth' });
        },
        favorite: function (restaurant, toggle) {
            this.frontendFavoriteStore.toggle({
                restaurant_id: restaurant.id,
                toggle: toggle
            }).then(res => {
            }).catch((err) => {
                if (err.response.status === 401) {
                    restaurant.favorite = false;
                    router.push({ name: "auth.login" });
                }
            });
        }
    },
    watch: {
        restaurant: {
            deep: true,
            handler(restaurant) {
                if (restaurant.order_setup != null && (typeof restaurant.order_setup === 'object' && Object.keys(restaurant.order_setup).length > 0)) {
                    if (typeof restaurant.order_setup.minimum_order_limit !== "undefined") {
                        this.minimumOrderLimit = restaurant.order_setup.currency_minimum_order_limit;
                    }
                }
            }
        }
    }
}
</script>

<style scoped>
.restaurant-page {
    position: relative;
}
.restaurant-page::before {
    content: none;
}
.restaurant-section-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin-bottom: 0.9rem;
    font-size: 1.05rem;
    font-weight: 800;
    color: #14532d;
}
.restaurant-section-title i,
.restaurant-section-title__leaf {
    color: #15803d;
    width: 1.15rem;
    height: 1.15rem;
    flex-shrink: 0;
}
.restaurant-section-title i {
    font-size: 1.15rem;
}
.restaurant-hero {
    position: relative;
    z-index: 1;
    min-height: 420px;
    background: #050505;
    border-radius: 1.5rem;
    overflow: hidden;
    box-shadow: 0 18px 40px rgb(10 10 10 / 0.22);
    animation: restaurant-fade 600ms ease both;
}
.restaurant-hero__media {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center right;
}
.restaurant-hero__shade {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(
            90deg,
            #000 0%,
            #000 28%,
            rgb(0 0 0 / 0.92) 42%,
            rgb(0 0 0 / 0.62) 58%,
            rgb(0 0 0 / 0.22) 78%,
            rgb(0 0 0 / 0.04) 92%,
            transparent 100%
        ),
        linear-gradient(
            180deg,
            rgb(0 0 0 / 0.18) 0%,
            transparent 28%,
            transparent 62%,
            rgb(0 0 0 / 0.38) 100%
        );
}
.restaurant-hero__copy {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    max-width: 36rem;
    min-height: 420px;
    padding: 1.25rem 1.15rem 1.35rem;
    color: #fff;
}
.restaurant-hero__brand {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.65rem;
    margin-bottom: 0.85rem;
}
.restaurant-hero__logo {
    width: 3.1rem;
    height: 3.1rem;
    object-fit: cover;
    border-radius: 0.7rem;
    background: #fff;
    border: 2px solid #fff;
}
.restaurant-hero__status {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.28rem 0.7rem;
    border-radius: 999px;
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #fff;
}
.restaurant-hero__dot {
    width: 0.4rem;
    height: 0.4rem;
    border-radius: 999px;
    background: #fff;
}
.restaurant-hero__status--open { background: #16a34a; }
.restaurant-hero__status--closed { background: #dc2626; }
.restaurant-hero__title {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin: 0 0 0.3rem;
    font-size: 1.65rem;
    line-height: 1.12;
    font-weight: 800;
    letter-spacing: -0.02em;
    text-transform: capitalize;
    color: #fff;
}
.restaurant-hero__verified {
    color: #f5c518;
    font-size: 1.15rem;
}
.restaurant-hero__meta {
    margin: 0 0 0.55rem;
    font-size: 0.8rem;
    color: rgb(255 255 255 / 0.72);
}
.restaurant-hero__intro {
    margin: 0 0 1rem;
    max-width: 34rem;
    font-size: 0.8rem;
    line-height: 1.55;
    color: rgb(255 255 255 / 0.82);
}
.restaurant-hero__stats {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.85rem 1.05rem;
    max-width: 100%;
    margin-bottom: 1.05rem;
    padding: 0.7rem 0.95rem;
    background: rgb(0 0 0 / 0.28);
    border: 1px solid rgb(255 255 255 / 0.28);
    border-radius: 0.8rem;
}
.restaurant-hero__stat {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #fff;
}
.restaurant-hero__stat i {
    font-size: 1.15rem;
    color: rgb(255 255 255 / 0.9);
}
.restaurant-hero__stat small {
    display: block;
    font-size: 0.65rem;
    font-weight: 500;
    color: rgb(255 255 255 / 0.62);
}
.restaurant-hero__stat strong {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #fff;
}
.restaurant-hero__stat-line {
    display: none;
    width: 1px;
    height: 2.15rem;
    background: rgb(255 255 255 / 0.28);
}
.restaurant-hero__actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 0.55rem;
}
.restaurant-hero__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    height: 2.75rem;
    font-weight: 700;
}
.restaurant-hero__btn--primary {
    min-width: 9.75rem;
    padding: 0 1.15rem;
    border: 0;
    border-radius: 0.7rem;
    font-size: 0.88rem;
    color: #fff;
    background: #056429;
}
.restaurant-hero__btn--icon {
    width: 2.75rem;
    padding: 0;
    color: #111827;
    background: #fff;
    border: 1.5px solid #111827;
    border-radius: 0.65rem;
    font-size: 1.05rem;
}
.restaurant-hero__btn--icon .lab-fill-heart {
    color: #15803d;
}
.restaurant-hero__share-icon {
    width: 1.15rem;
    height: 1.15rem;
    display: block;
}
.restaurant-hero__rating {
    position: absolute;
    right: 0.9rem;
    bottom: 0.9rem;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.05rem;
    min-width: 6.4rem;
    padding: 0.55rem 0.7rem 0.5rem;
    background: #fff;
    border-radius: 0.75rem;
    box-shadow: 0 10px 24px rgb(0 0 0 / 0.22);
}
.restaurant-hero__rating-score {
    display: flex;
    align-items: center;
    gap: 0.22rem;
}
.restaurant-hero__rating i {
    color: #22c55e;
    font-size: 1rem;
}
.restaurant-hero__rating strong {
    font-size: 0.98rem;
    font-weight: 800;
    color: #111827;
}
.restaurant-hero__rating-score span {
    font-size: 0.82rem;
    font-weight: 600;
    color: #6b7280;
}
.restaurant-hero__rating small {
    font-size: 0.66rem;
    color: #6b7280;
}
.restaurant-highlights {
    position: relative;
    z-index: 1;
    padding: 1.1rem 1rem 1.2rem;
    background: #fefaf0;
    border-radius: 1.15rem;
}
.restaurant-highlights__track {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.6rem;
}
.restaurant-highlight {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.28rem;
    min-height: 7.1rem;
    padding: 0.85rem 0.55rem 0.8rem;
    background: #fff;
    border: 1px solid transparent;
    border-radius: 0.85rem;
    box-shadow: 0 2px 10px rgb(15 23 42 / 0.05);
}
.restaurant-highlight--cuisines {
    border-color: #f59e0b;
}
.restaurant-highlight__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    margin-bottom: 0.1rem;
    background: transparent;
    font-size: 1.35rem;
    color: #15803d;
}
.restaurant-highlight--premium_quality .restaurant-highlight__icon { color: #0f766e; }
.restaurant-highlight--authentic_flavors .restaurant-highlight__icon { color: #ea580c; }
.restaurant-highlight--on_time_delivery .restaurant-highlight__icon { color: #16a34a; }
.restaurant-highlight--hygienic_kitchen .restaurant-highlight__icon { color: #0f766e; }
.restaurant-highlight--loved_by_customers .restaurant-highlight__icon { color: #16a34a; }
.restaurant-highlight--cuisines .restaurant-highlight__icon { color: #ea580c; }
.restaurant-highlight__title {
    font-size: 0.78rem;
    line-height: 1.25;
    font-weight: 800;
    color: #111827;
}
.restaurant-highlight__sub {
    font-size: 0.66rem;
    line-height: 1.35;
    color: #6b7280;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.restaurant-notice {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.95rem;
    padding: 1.05rem 1.15rem;
    background: #fff0f0;
    border-radius: 1.15rem;
}
.restaurant-notice__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.7rem;
    height: 2.7rem;
    color: #fff;
    background: #dc2626;
    border-radius: 999px;
    font-size: 1.25rem;
    font-weight: 800;
    line-height: 1;
    flex-shrink: 0;
}
.restaurant-notice__title {
    margin: 0;
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #dc2626;
}
.restaurant-notice__body {
    margin: 0.35rem 0 0;
    font-size: 0.8rem;
    line-height: 1.55;
    color: #3f3f46;
}
.restaurant-notice__emphasis {
    margin: 0.45rem 0 0;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1.5;
    color: #dc2626;
}
.restaurant-notice__art {
    position: relative;
    display: none;
    width: 3.6rem;
    height: 3.6rem;
    color: #b45309;
    flex-shrink: 0;
    margin-left: auto;
}
.restaurant-notice__art .lab-fill-bag {
    font-size: 2.5rem;
}
.restaurant-notice__art .lab-line-shield {
    position: absolute;
    right: -0.15rem;
    bottom: -0.1rem;
    font-size: 1.2rem;
    color: #16a34a;
    background: #fff;
    border-radius: 999px;
}
.restaurant-deals__all {
    flex-shrink: 0;
    font-size: 0.78rem;
    font-weight: 700;
    color: rgb(var(--primary));
    white-space: nowrap;
}
.restaurant-deal {
    display: flex;
    gap: 0.75rem;
    padding: 0.9rem;
    border: 1px solid #dceee3;
    border-radius: 1rem;
    background: #f4fbf6;
    box-shadow: 0 6px 16px rgb(15 23 42 / 0.04);
}
.restaurant-deal__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.7rem;
    height: 2.7rem;
    flex-shrink: 0;
    border-radius: 0.7rem;
    color: #fff;
    background: rgb(var(--primary));
    font-size: 1.15rem;
}
.restaurant-deal__amount {
    margin: 0 0 0.25rem;
    font-size: 0.95rem;
    font-weight: 800;
    color: #111827;
}
.restaurant-deal__code {
    display: inline-block;
    margin-bottom: 0.35rem;
    padding: 0.1rem 0.5rem;
    border: 1px dashed rgb(var(--primary) / 0.45);
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: rgb(var(--primary));
}
.restaurant-deal__copy {
    margin: 0 0 0.4rem;
    font-size: 0.72rem;
    line-height: 1.4;
    color: #6b7280;
}
.restaurant-deal__use {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    margin: 0;
    font-size: 0.7rem;
    font-weight: 600;
    color: rgb(var(--primary));
}
.restaurant-deals {
    overflow: hidden;
}
.restaurant-menu-bar {
    position: relative;
    z-index: 1;
    padding: 1rem 0 0.75rem;
    border-top: 1px solid rgb(10 61 40 / 0.06);
}
.restaurant-menu-bar--fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 45;
    padding: 0.75rem 0.75rem;
    background: rgb(255 255 255 / 0.96);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgb(10 61 40 / 0.08);
    box-shadow: 0 8px 24px rgb(10 61 40 / 0.06);
}
.restaurant-filter {
    color: rgb(var(--heading));
    background: #eef6f0;
    border-radius: 0.55rem;
}
.restaurant-filter--on {
    color: #fff;
    background: rgb(var(--primary));
    border-radius: 0.55rem;
}
.restaurant-search {
    background: #eef6f0;
    border: 1px solid transparent;
    border-radius: 0.65rem;
}
.restaurant-cat {
    cursor: pointer;
    height: 2.15rem;
    line-height: 2.15rem;
    padding: 0 0.95rem;
    font-size: 0.875rem;
    white-space: nowrap;
    text-transform: capitalize;
    color: rgb(var(--heading));
    background: #fff;
    border: 1px solid rgb(10 61 40 / 0.1);
    border-radius: 999px;
    transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}
.restaurant-cat:hover {
    border-color: rgb(var(--primary) / 0.35);
    color: rgb(var(--primary));
}
.restaurant-cat--active {
    color: #fff !important;
    background: rgb(var(--primary)) !important;
    border-color: rgb(var(--primary)) !important;
}
.restaurant-float-cart {
    border-radius: 0.85rem 0 0 0.85rem;
    box-shadow: 0 12px 28px rgb(10 61 40 / 0.2);
    z-index: 50;
}
.restaurant-menu-grid--search {
    padding: 1rem;
    background: #f3faf5;
    border-radius: 1rem;
}
.restaurant-menu-grid {
    position: relative;
    z-index: 1;
}
.restaurant-section {
    position: relative;
    z-index: 1;
}
@keyframes restaurant-fade {
    from { opacity: 0; }
    to { opacity: 1; }
}
@media (min-width: 640px) {
    .restaurant-hero,
    .restaurant-hero__copy { min-height: 460px; }
    .restaurant-hero { border-radius: 1.5rem; }
    .restaurant-hero__copy {
        justify-content: center;
        padding: 1.7rem 1.5rem 1.75rem 1.7rem;
    }
    .restaurant-hero__logo { width: 3.4rem; height: 3.4rem; }
    .restaurant-hero__title { font-size: 2.05rem; }
    .restaurant-hero__stat-line { display: block; }
    .restaurant-highlights { padding: 1.25rem 1.2rem 1.35rem; }
    .restaurant-highlights__track { gap: 0.7rem; }
    .restaurant-notice__art { display: block; }
}
@media (min-width: 768px) {
    .restaurant-hero,
    .restaurant-hero__copy { min-height: 400px; }
    .restaurant-hero__copy { max-width: 38rem; }
    .restaurant-hero__title { font-size: 2.25rem; }
    .restaurant-highlights__track { grid-template-columns: repeat(6, minmax(0, 1fr)); }
}
@media (min-width: 1024px) {
    .restaurant-hero,
    .restaurant-hero__copy { min-height: 430px; }
    .restaurant-hero { border-radius: 1.75rem; }
    .restaurant-hero__copy { padding: 2rem 1.75rem 2rem 2.15rem; }
    .restaurant-hero__title { font-size: 2.55rem; }
    .restaurant-hero__intro { font-size: 0.88rem; }
    .restaurant-hero__shade {
        background:
            linear-gradient(
                90deg,
                #000 0%,
                #000 30%,
                rgb(0 0 0 / 0.94) 44%,
                rgb(0 0 0 / 0.55) 62%,
                rgb(0 0 0 / 0.16) 80%,
                transparent 100%
            );
    }
}
@media (prefers-reduced-motion: reduce) {
    .restaurant-hero { animation: none !important; }
}
</style>

