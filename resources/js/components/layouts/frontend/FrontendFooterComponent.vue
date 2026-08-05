<template>
    <LoadingComponent :props="loading"/>

    <footer class="pt-14 bg-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12 md:col-4">
                    <div class="w-full max-w-sm tablet:text-center tablet:mx-auto">
                        <router-link aria-current="page" class="flex-shrink-0 inline-flex tablet:justify-center" :to="{ name: 'frontend.home' }">
                            <span class="inline-flex items-center rounded-2xl bg-white px-4 py-3 shadow-sm">
                                <img
                                    class="h-14 sm:h-16 md:h-20 w-auto max-w-[260px] sm:max-w-[320px] object-contain object-left"
                                    :src="setting.theme_footer_logo"
                                    alt="Cost to Cost Foods"
                                >
                            </span>
                        </router-link>

                        <h4 class="mt-5 mb-3 text-xs text-white">{{ $t('message.subscribe_to_our_newsletter') }}</h4>
                        <form @submit.prevent="saveSubscription" class="flex w-full h-12 rounded-lg p-2 mb-6 bg-white">
                            <input type="email" v-model="subscriptionProps.post.email"
                                   :placeholder="$t('label.your_email_address')"
                                   class="w-full h-full px-2 placeholder:text-xs placeholder:font-medium">
                            <button type="submit"
                                    class="text-xs font-medium capitalize flex-shrink-0 px-3 h-full rounded-md bg-primary text-white">
                                {{ $t('button.subscribe') }}
                            </button>
                        </form>

                        <h5 v-if="setting.social_media_facebook || setting.social_media_twitter || setting.social_media_instagram || setting.social_media_youtube"
                            class="text-xs mb-2 text-white">{{ $t('label.follow_us_on') }}</h5>
                        <nav
                            v-if="setting.social_media_facebook || setting.social_media_twitter || setting.social_media_instagram || setting.social_media_youtube"
                            class="flex flex-wrap items-center gap-6 tablet:justify-center">
                            <a v-if="setting.social_media_facebook" target="_blank"
                               :href="setting.social_media_facebook">
                                <i class="lab-fill-facebook text-2xl text-white transition-all duration-500 hover:text-primary"></i>
                            </a>
                            <a v-if="setting.social_media_twitter" target="_blank" :href="setting.social_media_twitter">
                                <i class="lab-fill-x text-2xl text-white transition-all duration-500 hover:text-primary"></i>
                            </a>
                            <a v-if="setting.social_media_instagram" target="_blank"
                               :href="setting.social_media_instagram">
                                <i class="lab-fill-instagram text-2xl text-white transition-all duration-500 hover:text-primary"></i>
                            </a>
                            <a v-if="setting.social_media_youtube" target="_blank" :href="setting.social_media_youtube">
                                <i class="lab-fill-youtube text-2xl text-white transition-all duration-500 hover:text-primary"></i>
                            </a>
                        </nav>
                    </div>
                </div>

                <div class="col-12 sm:col-6 md:col-4">
                    <div class="w-fit ltr:mr-auto rtl:ml-auto sm:mx-auto max-md:mt-5">
                        <h4 class="text-lg font-semibold capitalize mb-6 text-white">{{ $t('label.legal') }}</h4>
                        <nav v-if="pages.length > 0" class="flex flex-col gap-4">
                            <router-link v-for="page in pages"
                                         class="capitalize text-white transition-all duration-300 hover:text-primary"
                                         :to="{ name: 'frontend.page', params: { slug: page.slug } }">
                                {{ page.title }}
                            </router-link>
                        </nav>
                    </div>
                </div>

                <div class="col-12 sm:col-6 md:col-4">
                    <div class="w-fit ltr:mr-auto rtl:ml-auto sm:mx-auto max-md:mt-5">
                        <dl class="mb-7">
                            <dt class="text-lg font-semibold capitalize mb-4 text-white">
                                {{ $t('label.download_our_apps') }}
                            </dt>
                            <dd class="flex gap-3">
                                <a target="_blank" :href="setting.frontend_app_section_android_app_link">
                                    <img class="h-11 rounded-lg" :src="setting.image_play_store" alt="store">
                                </a>
                                <a target="_blank" :href="setting.frontend_app_section_iso_app_link">
                                    <img class="h-11 rounded-lg" :src="setting.image_app_store" alt="store">
                                </a>
                            </dd>
                        </dl>
                        <ul class="flex flex-col gap-4">
                            <li class="flex items-start gap-2.5">
                                <i class="lab-line-location text-xl flex-shrink-0 -mt-[1px] text-white"></i>
                                <span class="text-white">{{ setting.company_address }}</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="lab-line-mail text-xl flex-shrink-0 -mt-[1px] text-white"></i>
                                <span class="text-white">{{ setting.company_email }}</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="lab-line-calling text-xl flex-shrink-0 -mt-[1px] text-white"></i>
                                <span class="text-white">{{ setting.company_phone }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-4 mt-14 text-center border-t border-white/5">
            <p class="text-sm text-white">
                {{ setting.site_copyright }}
            </p>
        </div>
    </footer>
</template>


<script>
import axios from "axios";
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import alertService from "../../../services/alertService.js";
import {useFrontendPageStore} from "../../../stores/frontendPage.js";
import statusEnum from "../../../enums/modules/statusEnum.js";
import menuSectionEnum from "../../../enums/modules/menuSectionEnum.js";

export default {
    name: "FrontendFooterComponent",
    components: {LoadingComponent},
    setup() {
        const frontendPageStore    = useFrontendPageStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            frontendPageStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            subscriptionProps: {
                post: {
                    email: ""
                }
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        pages: function () {
            return this.frontendPageStore.lists;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.frontendPageStore.fetch({
            paginate: 0,
            order_column: "id",
            order_type: "asc",
            menu_section_id: menuSectionEnum.FOOTER,
            status: statusEnum.ACTIVE
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        })
    },
    methods: {
        saveSubscription: function () {
            this.loading.isActive = true;
            axios.post('/frontend/subscriber', this.subscriptionProps.post).then(res => {
                this.loading.isActive             = false;
                this.subscriptionProps.post.email = "";
                alertService.success(this.$t("message.subscribe"));
            }).catch((err) => {
                alertService.error(err.response.data.message);
                this.loading.isActive = false;
            });
        }
    }
}
</script>
