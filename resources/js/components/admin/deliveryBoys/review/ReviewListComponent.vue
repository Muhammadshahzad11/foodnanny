<template>
    <LoadingComponent :props="loading" />
    <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.reviews") }}</h3>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">
                                {{ $t("label.rating") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.review") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.date") }}
                            </th>
                            <th class="db-table-head-th hidden-print" v-if="permissionChecker('delivery-boys_show')">
                                {{ $t("label.action") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="reviews.length > 0">
                        <tr class="db-table-body-tr" v-for="review in reviews" :key="review">
                            <td class="db-table-body-td">
                                <span class="text-lg" v-for="starIndex in 5" :key="starIndex">
                                    <span v-if="starIndex <= review.star" class="text-[18px] text-[#facc15]">★</span>
                                    <span v-else class="text-[18px] text-[#6e7190]">★</span>
                                </span>
                            </td>
                            <td class="db-table-body-td">
                                {{ textShortener(review.review, 30) }}
                            </td>
                            <td class="db-table-body-td">
                                {{ review.date }}
                            </td>
                            <td class="db-table-body-td hidden-print" v-if="permissionChecker('delivery-boys_show')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <button class="db-table-action view" @click="showModal(review)"  v-if="permissionChecker('delivery-boys_show')">
                                    <i class="lab lab-line-eye"></i>
                                    <span class="db-tooltip">{{ $t('button.view') }}</span>
                                   </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td" colspan="5">
                                <div class="p-4">
                                    <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
                                    <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found')}}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>

   <div id="reviewModal" class="modal">
        <div class="modal-dialog max-w-lg">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.review") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                        @click="hideModal"></button>
            </div>
            <div class="modal-body">
                <div class="row py-2 flex flex-col gap-6" v-if="modalData !== null">
                    <div class="col-12 !py-1.5 flex items-center gap-4">
                        <img :src="modalData.profile" v-if="modalData.profile" class="w-11 h-11 object-fill"
                             alt="review-image"/>
                        <div class="p-0" v-if="modalData.type === enums.reviewTypeEnum.RESTAURANT">
                            <span class="db-list-item-text after:content-none block text-[#6E7191]"> {{
                                    $t("label.restaurant")
                                }} </span>
                            <span class="db-list-item-title after:content-none block"> {{ modalData.name }} </span>
                        </div>
                        <div class="p-0" v-if="modalData.type === enums.reviewTypeEnum.DELIVERY_BOY">
                            <span class="db-list-item-text after:content-none block text-[#6E7191]"> {{
                                    $t("label.delivery_boy")
                                }} </span>
                            <span class="db-list-item-title after:content-none block"> {{ modalData.name }}  </span>
                        </div>
                    </div>
                    <div class="col-12 py-0 flex flex-col">
                        <span class="db-list-item-title after:content-none mr-2 ">{{
                                $t('label.your_rating')
                            }} :</span>
                        <span class="db-list-item-text">
                            <span v-for="starIndex in 5" :key="starIndex">
                                <i class="lab-fill-star-round mr-1 text-xl"
                                   :class="starIndex <= modalData.star ? 'text-yellow-400' : 'text-[#D9DBE9]'"></i>
                            </span>
                        </span>
                    </div>
                    <div class="col-12 py-0 flex flex-col">
                        <span class="db-list-item-title after:content-none mr-2">{{
                                $t('label.rating_details')
                            }}:</span>
                        <span class="db-list-item-text">{{ modalData.review }}</span>
                    </div>
                    <div class="col-12 py-0 flex flex-col">
                        <span class="db-list-item-title after:content-none mr-2">{{ $t('label.rating_by') }}:</span>
                        <span class="db-list-item-text">{{ modalData.reviewer }}</span>
                        <span class="db-list-item-text">{{ modalData.date }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import { useFrontendSettingStore } from "../../../../stores/frontendSetting.js";
import { useReviewStore } from "../../../../stores/review.js";
import appService from "../../../../services/appService.js";
import modelTypeEnum from "../../../../enums/modules/modelTypeEnum.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import {useModal} from "../../../../composables/modal.js";
import reviewTypeEnum from "../../../../enums/modules/reviewTypeEnum.js";

export default {
    name: "ReviewListComponent",
    components: {
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const reviewStore = useReviewStore();
        const {openModal, closeModal} = useModal();

        return {
            frontendSettingStore,
            reviewStore,
            openModal,
            closeModal
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                modelTypeEnum: modelTypeEnum,
                reviewTypeEnum: reviewTypeEnum
            },
            props: {
                search: {
                    paginate       : 1,
                    page           : 1,
                    per_page       : 10,
                    order_column   : "id",
                    order_type     : "desc",
                    type           : null,
                    delivery_boy_id: null
                }
            },
            modalData: null,
        };
    },
    mounted() {
        if(this.$route.params.id) {
            this.props.search.type            = modelTypeEnum.DELIVERY_BOY;
            this.props.search.delivery_boy_id = this.$route.params.id;
        }
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        reviews: function () {
            return this.reviewStore.lists;
        },
        pagination: function () {
            return this.reviewStore.pagination;
        },
        paginationPage: function () {
            return this.reviewStore.page;
        }
    },
    methods: {
        showModal: function (review) {
            this.modalData = review;
            this.openModal("reviewModal");
        },
        hideModal: function () {
            this.modalData = null;
            this.closeModal('reviewModal');
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.reviewStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
};
</script>
