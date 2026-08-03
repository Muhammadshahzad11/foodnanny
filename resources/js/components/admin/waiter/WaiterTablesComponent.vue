<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('label.tables') }}</h3>
                <form class="flex gap-2" @submit.prevent="search">
                    <input v-model="props.search.search" type="text" class="db-field-control"
                           :placeholder="$t('button.search')"/>
                    <button type="submit" class="db-btn py-2 text-white bg-primary">{{ $t('button.search') }}</button>
                </form>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 p-4">
                <button
                    v-for="table in tables"
                    :key="table.id"
                    type="button"
                    class="text-left rounded-xl p-4 border transition hover:border-primary"
                    :class="statusClass(table.status)"
                    @click="openTable(table)"
                >
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <p class="text-lg font-semibold text-heading">{{ table.table_number }}</p>
                            <p class="text-sm text-[#6E7191]">{{ table.name }}</p>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded bg-white/80">{{ statusLabel(table.status) }}</span>
                    </div>
                    <p class="text-xs text-[#6E7191] mb-1" v-if="table.zone">{{ table.zone }} · {{ table.capacity }} {{ $t('label.seats') }}</p>
                    <p class="text-sm font-medium text-heading" v-if="table.open_order">
                        {{ table.open_order.order_serial_no }} · {{ table.open_order_total_currency }}
                    </p>
                    <p class="text-sm text-[#6E7191]" v-else>{{ $t('label.no_open_order') }}</p>
                </button>
            </div>

            <div v-if="!tables.length" class="p-8 text-center text-[#6E7191]">
                {{ $t('message.no_data_found') }}
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useWaiterTableStore} from "../../../stores/waiterTable.js";
import tableStatusEnum from "../../../enums/modules/tableStatusEnum.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "WaiterTablesComponent",
    components: {LoadingComponent},
    setup() {
        const waiterTableStore = useWaiterTableStore();
        return {waiterTableStore, tableStatusEnum};
    },
    data() {
        return {
            loading: {isActive: false},
            props: {
                search: {
                    paginate: 0,
                    order_column: 'table_number',
                    order_type: 'asc',
                    search: '',
                }
            }
        };
    },
    computed: {
        tables() {
            return this.waiterTableStore.lists || [];
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        list() {
            this.loading.isActive = true;
            this.waiterTableStore.fetch(this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t('message.something_wrong'));
            });
        },
        search() {
            this.list();
        },
        openTable(table) {
            this.$router.push({name: 'admin.waiter.table.order', params: {id: table.id}});
        },
        statusLabel(status) {
            const map = {
                [tableStatusEnum.AVAILABLE]: this.$t('label.available'),
                [tableStatusEnum.OCCUPIED]: this.$t('label.occupied'),
                [tableStatusEnum.RESERVED]: this.$t('label.reserved'),
                [tableStatusEnum.CLEANING]: this.$t('label.cleaning'),
                [tableStatusEnum.OUT_OF_SERVICE]: this.$t('label.out_of_service'),
                [tableStatusEnum.INACTIVE]: this.$t('label.inactive'),
            };
            return map[status] || status;
        },
        statusClass(status) {
            if (status === tableStatusEnum.OCCUPIED) return 'border-amber-400 bg-amber-50';
            if (status === tableStatusEnum.AVAILABLE) return 'border-emerald-300 bg-emerald-50';
            if (status === tableStatusEnum.RESERVED) return 'border-sky-300 bg-sky-50';
            if (status === tableStatusEnum.CLEANING) return 'border-slate-300 bg-slate-50';
            return 'border-[#EFF0F6] bg-white';
        }
    }
}
</script>
