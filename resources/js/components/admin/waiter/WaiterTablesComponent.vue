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
                    class="text-left rounded-xl p-4 border-2 transition hover:shadow-md"
                    :class="statusClass(table.status)"
                    @click="openTable(table)"
                >
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="min-w-0">
                            <p class="text-lg font-semibold text-heading">{{ table.table_number }}</p>
                            <p class="text-sm text-[#6E7191] truncate">{{ table.name }}</p>
                        </div>
                        <span
                            class="flex-shrink-0 inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full shadow-sm"
                            :class="statusBadgeClass(table.status)"
                        >
                            <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse" v-if="isLiveStatus(table.status)"></span>
                            {{ statusLabel(table.status) }}
                        </span>
                    </div>
                    <p class="text-xs text-[#6E7191] mb-2" v-if="table.zone">{{ table.zone }} · {{ table.capacity }} {{ $t('label.seats') }}</p>
                    <p class="text-sm font-semibold text-heading" v-if="table.open_order">
                        <a
                            href="#"
                            class="text-primary underline underline-offset-2 decoration-2 hover:text-secondary"
                            @click.stop.prevent="openOrder(table.open_order)"
                        >#{{ table.open_order.order_serial_no }}</a>
                        <span class="text-heading"> · {{ table.open_order_total_currency }}</span>
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
import {useAuthStore} from "../../../stores/auth.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import tableStatusEnum from "../../../enums/modules/tableStatusEnum.js";
import alertService from "../../../services/alertService.js";
import {apiErrorMessage} from "../../../services/apiError.js";
import {getAuthRestaurantId, subscribeRestaurantRealtime} from "../../../composables/useRealtime.js";
import roleEnum from "../../../enums/modules/roleEnum.js";

export default {
    name: "WaiterTablesComponent",
    components: {LoadingComponent},
    setup() {
        return {
            waiterTableStore: useWaiterTableStore(),
            authStore: useAuthStore(),
            defaultAccessStore: useDefaultAccessStore(),
            tableStatusEnum,
        };
    },
    data() {
        return {
            loading: {isActive: false},
            unsubscribeRealtime: null,
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
        const restaurantId = getAuthRestaurantId(this.authStore.info, this.defaultAccessStore.lists);
        const isAdmin = Number(this.authStore.info?.role_id) === roleEnum.ADMIN;
        this.unsubscribeRealtime = subscribeRestaurantRealtime({
            restaurantId: restaurantId || 1,
            roles: isAdmin ? ['admin'] : [],
            onKitchenOrder: () => this.listQuiet(),
            onTable: () => this.listQuiet(),
        });
    },
    beforeUnmount() {
        if (typeof this.unsubscribeRealtime === 'function') {
            this.unsubscribeRealtime();
        }
    },
    methods: {
        list() {
            this.loading.isActive = true;
            this.waiterTableStore.fetch(this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(apiErrorMessage(err, this.$t('message.failed_to_load_tables')));
            });
        },
        listQuiet() {
            this.waiterTableStore.fetch(this.props.search).catch(() => {});
        },
        search() {
            this.list();
        },
        openTable(table) {
            this.$router.push({name: 'admin.waiter.table.order', params: {id: table.id}});
        },
        openOrder(order) {
            if (!order?.id) return;
            this.$router.push({name: 'admin.waiter.orders.show', params: {id: order.id}});
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
        isLiveStatus(status) {
            return status === tableStatusEnum.OCCUPIED || status === tableStatusEnum.RESERVED;
        },
        statusBadgeClass(status) {
            if (status === tableStatusEnum.OCCUPIED) {
                return 'text-white bg-amber-500 ring-2 ring-amber-300/80 shadow-amber-200/60';
            }
            if (status === tableStatusEnum.AVAILABLE) {
                return 'text-white bg-emerald-500 ring-2 ring-emerald-300/70';
            }
            if (status === tableStatusEnum.RESERVED) {
                return 'text-white bg-sky-500 ring-2 ring-sky-300/70';
            }
            if (status === tableStatusEnum.CLEANING) {
                return 'text-slate-800 bg-slate-200 ring-1 ring-slate-300';
            }
            if (status === tableStatusEnum.OUT_OF_SERVICE) {
                return 'text-white bg-rose-500 ring-2 ring-rose-300/70';
            }
            return 'text-slate-700 bg-slate-100 ring-1 ring-slate-200';
        },
        statusClass(status) {
            if (status === tableStatusEnum.OCCUPIED) return 'border-amber-400 bg-amber-50';
            if (status === tableStatusEnum.AVAILABLE) return 'border-emerald-300 bg-emerald-50';
            if (status === tableStatusEnum.RESERVED) return 'border-sky-300 bg-sky-50';
            if (status === tableStatusEnum.CLEANING) return 'border-slate-300 bg-slate-50';
            if (status === tableStatusEnum.OUT_OF_SERVICE) return 'border-rose-200 bg-rose-50/40';
            return 'border-[#EFF0F6] bg-white';
        }
    }
}
</script>
