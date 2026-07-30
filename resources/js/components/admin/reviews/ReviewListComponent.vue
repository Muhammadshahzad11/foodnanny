<template>
    <LoadingComponent :props="loading"/>

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.reviews") }}</h3>
                <nav class="flex flex-wrap gap-2 mobile:justify-center">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                    <FilterComponent @click.prevent="handleSlide('review-filter')"/>
                    <div class="paper-group">
                        <ExportComponent @click.prevent="handlePaper"/>
                        <nav
                            class="paper-content absolute top-9 right-1/2 translate-x-1/2 z-30 min-w-[80px] w-fit rounded-md shadow-paper bg-white">
                            <PrintComponent :props="printObj"/>
                            <ExcelComponent :method="xls"/>
                        </nav>
                    </div>
                    <ReviewCreateComponent :props="props" v-if="permissionChecker('reviews_create')"/>
                </nav>
            </div>
            <div class="table-filter-div" id="review-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStar" class="db-field-title after:hidden">{{ $t('label.star') }}</label>
                            <input id="searchStar" v-model="props.search.star" type="text" class="db-field-control">
                        </div>
                        <div v-if="roleId === this.enums.roleEnum.ADMIN && defaultRestaurant === 0"
                             class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchModelType" class="db-field-title after:hidden">{{
                                    $t('label.type')
                                }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchModelType"
                                        v-model="props.search.type" :options="[
                                    { id: enums.modelTypeEnum.RESTAURANT, name: $t('label.restaurant') },
                                    { id: enums.modelTypeEnum.DELIVERY_BOY, name: $t('label.delivery_boy') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                        :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden"> {{
                                    $t('label.date')
                                }}</label>
                            <DatePickerComponent @update:modelValue="handleDate" inputStyle="filter" :range="true"
                                                 hideInputIcon v-model="modelValue"/>
                        </div>
                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-line-cross lab-font-size-22"></i>
                                    <span>{{ $t('button.clear') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th v-if="defaultRestaurant === 0 && roleId !== enums.roleEnum.DELIVERY_BOY" class="db-table-head-th">
                                {{ $t("label.name") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.rating") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.review") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.date") }}
                            </th>
                            <th class="db-table-head-th hidden-print"
                                v-if="permissionChecker('reviews_show') || permissionChecker('reviews_edit') || permissionChecker('reviews_delete')">
                                {{ $t("label.action") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="reviews.length > 0">
                        <tr class="db-table-body-tr" v-for="review in reviews" :key="review">
                            <td v-if="defaultRestaurant === 0 && roleId !== enums.roleEnum.DELIVERY_BOY" class="db-table-body-td">
                                <span class="flex items-center">
                                    <img class="w-10 h-10 rounded-full object-cover me-2" :src="review.profile" alt="avatar">
                                    {{ textShortener(review.name, 15) }}
                                </span>
                            </td>
                            <td class="db-table-body-td">
                                <div class="flex items-center">
                                    <span class="text-lg" v-for="starIndex in 5" :key="starIndex">
                                        <span v-if="starIndex <= review.star" class="text-[18px] text-[#facc15]">★</span>
                                        <span v-else class="text-[18px] text-[#6e7190]">★</span>
                                    </span>
                                </div>
                            </td>
                            <td class="db-table-body-td">
                                {{ textShortener(review.review, 20) }}
                            </td>
                            <td class="db-table-body-td">
                                {{ review.date }}
                            </td>
                            <td class="db-table-body-td hidden-print" v-if="permissionChecker('reviews_show') || permissionChecker('reviews_edit') || permissionChecker('reviews_delete')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <button class="db-table-action view" @click="showModal(review)" v-if="permissionChecker('reviews_show')">
                                        <i class="lab lab-line-eye"></i>
                                        <span class="db-tooltip">{{ $t('button.view') }}</span>
                                    </button>
                                    <SmIconSidebarModalEditComponent @click="edit(review)" v-if="permissionChecker('reviews_edit')"/>
                                    <SmIconDeleteComponent @click="destroy(review.id)" v-if="permissionChecker('reviews_delete')"/>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td" :colspan="defaultRestaurant === 0 && roleId !== enums.roleEnum.DELIVERY_BOY ? 5 : 4">
                                <div class="p-4">
                                    <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
                                    <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list"/>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }"/>
                    <PaginationBox :pagination="pagination" :method="list"/>
                </div>
            </div>
        </div>
    </div>

    <div v-if="permissionChecker('reviews_show')" id="reviewModal" class="modal">
        <div class="modal-dialog max-w-lg">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.review") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click="hideModal"></button>
            </div>
            <div class="modal-body">
                <div class="row py-2 flex flex-col gap-6" v-if="modalData !== null">
                    <div class="col-12 !py-1.5 flex items-center gap-4">
                        <img :src="modalData.profile" v-if="modalData.profile" class="w-11 h-11 object-fill" alt="review-image"/>
                        <div class="p-0" v-if="modalData.type === enums.reviewTypeEnum.RESTAURANT">
                            <span class="db-list-item-text after:content-none block text-[#6E7191]"> {{ $t("label.restaurant") }} </span>
                            <span class="db-list-item-title after:content-none block"> {{ modalData.name }} </span>
                        </div>
                        <div class="p-0" v-if="modalData.type === enums.reviewTypeEnum.DELIVERY_BOY">
                            <span class="db-list-item-text after:content-none block text-[#6E7191]"> {{ $t("label.delivery_boy") }} </span>
                            <span class="db-list-item-title after:content-none block"> {{ modalData.name }}  </span>
                        </div>
                    </div>
                    <div class="col-12 py-0 flex flex-col">
                        <span class="db-list-item-title after:content-none mr-2 ">{{ $t('label.your_rating') }} :</span>
                        <span class="db-list-item-text">
                            <span v-for="starIndex in 5" :key="starIndex">
                                <i class="lab-fill-star-round mr-1 text-xl" :class="starIndex <= modalData.star ? 'text-yellow-400' : 'text-[#D9DBE9]'"></i>
                            </span>
                        </span>
                    </div>
                    <div class="col-12 py-0 flex flex-col">
                        <span class="db-list-item-title after:content-none mr-2">{{ $t('label.rating_details') }}:</span>
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
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import ReviewCreateComponent from "./ReviewCreateComponent.vue";
import alertService from "../../../services/alertService.js";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../components/pagination/PaginationSMBox.vue";
import appService from "../../../services/appService.js";
import TableLimitComponent from "../components/TableLimitComponent.vue";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent.vue";
import FilterComponent from "../components/buttons/collapse/FilterComponent.vue";
import ExportComponent from "../components/buttons/export/ExportComponent.vue";
import print from 'vue3-print-nb';
import PrintComponent from "../components/buttons/export/PrintComponent.vue";
import ExcelComponent from "../components/buttons/export/ExcelComponent.vue";
import {usePaper} from "../../../composables/paper.js";
import {useSlide} from "../../../composables/slide.js";
import {useReviewStore} from "../../../stores/review.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";
import DatePickerComponent from "../components/DatePickerComponent.vue";
import modelTypeEnum from "../../../enums/modules/modelTypeEnum.js";
import {useModal} from "../../../composables/modal.js";
import reviewTypeEnum from "../../../enums/modules/reviewTypeEnum.js";
import {useAuthStore} from "../../../stores/auth.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";

export default {
    name: "ReviewListComponent",
    components: {
        SmIconSidebarModalEditComponent,
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        ReviewCreateComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        FilterComponent,
        ExportComponent,
        DatePickerComponent,
        print,
        PrintComponent,
        ExcelComponent
    },
    setup() {
        const {openModal, closeModal} = useModal();
        const authStore               = useAuthStore();
        const defaultAccessStore      = useDefaultAccessStore();
        const reviewStore             = useReviewStore();
        const frontendSettingStore    = useFrontendSettingStore();
        return {
            authStore,
            reviewStore,
            defaultAccessStore,
            frontendSettingStore,
            openModal,
            closeModal
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.reviews')
            },
            enums: {
                roleEnum: roleEnum,
                modelTypeEnum: modelTypeEnum,
                reviewTypeEnum: reviewTypeEnum,

            },
            props: {
                form: {
                    order_id: "",
                    type: null,
                    star: "",
                    review: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    star: "",
                    from_date: "",
                    to_date: "",
                    type: null,
                    restaurant_id: null,
                    delivery_boy_id: null
                }
            },
            modalData: null,
            modelValue: null,
            handlePaper: usePaper().handlePaper,
            handleSlide: useSlide().handleSlide
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        defaultRestaurant: function () {
            return this.defaultAccessStore.lists?.restaurant_id;
        },
        roleId: function () {
            return this.authStore.info?.role_id;
        },
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
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date   = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date   = null;
            }
        },
        clear: function () {
            this.props.search.paginate        = 1;
            this.props.search.page            = 1;
            this.props.search.type            = null;
            this.props.search.restaurant_id   = null;
            this.props.search.delivery_boy_id = null;
            this.props.search.star            = "";
            this.props.search.from_date       = "";
            this.props.search.to_date         = "";
            this.modelValue                   = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.reviewStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (review) {
            this.loading.isActive = true;
            this.reviewStore.edit(review.id);
            this.loading.isActive = false;
            this.props.errors     = {};
            this.props.form       = {
                star: review.star,
                review: review.review,
                type: modelTypeEnum.RESTAURANT
            };
        },
        destroy: function (id) {
            return new VueSimpleAlert.confirm(
                this.$t("message.delete_record"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.yes_delete"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.reviewStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.reviews"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        xls: function () {
            this.loading.isActive = true;
            this.reviewStore.export(this.props.search).then(res => {
                this.loading.isActive = false;
                const blob            = new Blob([res.data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const link            = document.createElement('a');
                link.href             = URL.createObjectURL(blob);
                link.download         = this.$t("menu.reviews");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
    }
};
</script>
