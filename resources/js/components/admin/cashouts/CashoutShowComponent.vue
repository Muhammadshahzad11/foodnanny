<template>
    <LoadingComponent :props="loading" />

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('label.cashout') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row">
                     <div class="col-12">
                        <ul class="db-list single">
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.user') }}</span>
                                <span class="db-list-item-text"> {{ cashout.user_name }} </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.email') }}</span>
                                <span class="db-list-item-text"> {{ cashout.user_email }} </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.amount') }}</span>
                                <span class="db-list-item-text"> {{ cashout.flat_amount }} </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.date') }}</span>
                                <span class="db-list-item-text"> {{ cashout.convert_date }} </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.transaction_id') }}</span>
                                <span class="db-list-item-text"> {{ cashout.transaction_id }} </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.attachment') }}</span>
                                <span class="db-list-item-text">
                                    <a v-if="cashout.file" :href="cashout.file" download class="db-btn-outline sm info modal-btn m-0.5">
                                        <i class="lab lab-line-download"></i>
                                        <span>{{ $t('label.download') }}</span>
                                    </a>
                                    <span v-else>{{ $t('message.attachment_not_found') }}</span>
                                </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.remarks') }}</span>
                                <span class="db-list-item-text" v-html="cashout.remarks"> </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import { useCashoutStore } from "../../../stores/cashout.js";

export default {
    name: "CashoutShowComponent",
    components: {
        LoadingComponent
    },
    setup() {
        const cashoutStore = useCashoutStore();
        return {
            cashoutStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        cashout: function () {
            return this.cashoutStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.cashoutStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    }
}
</script>
