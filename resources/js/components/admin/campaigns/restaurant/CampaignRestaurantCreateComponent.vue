<template>
    <LoadingComponent :props="loading" />

    <button type="button" @click="add" class="h-9 px-3 py-4 font-medium border border-primary flex items-center gap-1 text-sm tracking-wide capitalize rounded-md shadow text-white bg-primary">
        <i class="lab lab-line-add-circle"></i>
        <span>{{ addButton.title }}</span>
    </button>

    <div id="campaignRestaurant" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.restaurants") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <div class="form-row" v-if="message">
                    <div class="form-col-12 db-field-alert">
                        {{ message }}
                    </div>
                </div>
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-12">
                            <label for="restaurant_id" class="db-field-title required">
                                {{ $t("label.restaurant") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="restaurant_id"
                                v-bind:class="errors.restaurant_id ? 'invalid' : ''" v-model="props.form.restaurant_id"
                                :options="restaurants" label-by="name" value-by="id" :closeOnSelect="true"
                                :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.restaurant_id">
                                {{ errors.restaurant_id[0] }}
                            </small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle text-base"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save text-base"></i>
                                    <span>{{ $t("button.save") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import { useModal } from "../../../../composables/modal.js";
import { useCampaignRestaurantStore } from "../../../../stores/campaignRestaurant.js";
import { useRestaurantStore } from "../../../../stores/restaurant.js";

export default {
    name: "CampaignRestaurantCreateComponent",
    components: { LoadingComponent },
    props: ["props"],
    setup() {
        const campaignRestaurantStore = useCampaignRestaurantStore();
        const restaurantStore = useRestaurantStore();
        return {
            campaignRestaurantStore,
            restaurantStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            addButton: {
                title: this.$t("button.add_restaurant")
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            errors: {},
            message: null
        };
    },
    computed: {
        restaurants: function () {
            return this.restaurantStore.lists;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.restaurantStore.fetch({
            paginate: 0,
            order_column: 'id',
            order_type: 'asc',
            status: statusEnum.ACTIVE
        });
        this.loading.isActive = false;
    },
    methods: {
        add: function () {
            useModal().openModal('campaignRestaurant');
        },
        reset: function () {
            useModal().closeModal('campaignRestaurant');
            this.campaignRestaurantStore.reset();
            this.errors = {};
            this.$props.props.form = {
                restaurant_id: null
            };
            this.message = null;
        },
        save: function () {
            try {
                const tempId = this.campaignRestaurantStore.temp.temp_id;
                this.loading.isActive = true;
                this.campaignRestaurantStore.save(this.props).then((res) => {
                    useModal().closeModal('campaignRestaurant');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.restaurant"));
                    this.props.form = {
                        restaurant_id: null
                    };
                    this.errors = {};
                    this.message = null;
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err.response.data.errors === undefined) {
                        if (err.response.data.message) {
                            this.errors = {};
                            this.message = err.response.data.message;
                        } else {
                            this.message = null;
                        }
                    } else {
                        this.message = null;
                        this.errors = err.response.data.errors;
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
