<template>
    <div class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 p-4" v-if="visible">
        <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h3 class="text-lg font-semibold">{{ $t('label.table_qr') }}</h3>
                <button type="button" class="text-slate-400 hover:text-red-500" @click="$emit('close')">
                    <i class="lab lab-line-close"></i>
                </button>
            </div>
            <div class="p-5 text-center" v-if="qr">
                <p class="mb-1 font-medium">{{ qr.restaurant?.name }}</p>
                <p class="mb-4 text-sm text-slate-500">{{ qr.name }} ({{ qr.table_number }})</p>
                <img
                    v-if="qr.qr_image_url"
                    :src="qr.qr_image_url"
                    alt="QR"
                    class="mx-auto mb-4 h-56 w-56 rounded border object-contain"
                />
                <p class="mb-4 break-all text-xs text-slate-500">{{ qr.qr_url }}</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <button
                        v-if="permissionChecker('table_qr_download')"
                        type="button"
                        class="db-btn py-2 text-white bg-primary"
                        @click="$emit('download', 'png')"
                    >
                        {{ $t('button.download_png') }}
                    </button>
                    <button
                        v-if="permissionChecker('table_qr_download')"
                        type="button"
                        class="db-btn py-2 text-white bg-sky-600"
                        @click="$emit('download', 'svg')"
                    >
                        {{ $t('button.download_svg') }}
                    </button>
                    <button
                        v-if="permissionChecker('table_qr_view')"
                        type="button"
                        class="db-btn py-2 text-white bg-slate-600"
                        @click="$emit('print')"
                    >
                        {{ $t('button.print') }}
                    </button>
                    <button
                        v-if="permissionChecker('table_qr_regenerate')"
                        type="button"
                        class="db-btn py-2 text-white bg-amber-600"
                        @click="$emit('regenerate')"
                    >
                        {{ $t('button.regenerate_qr') }}
                    </button>
                </div>
            </div>
            <div class="p-5 text-center text-slate-500" v-else>
                {{ $t('message.no_qr_yet') }}
                <div class="mt-4" v-if="permissionChecker('table_qr_generate')">
                    <button type="button" class="db-btn py-2 text-white bg-primary" @click="$emit('generate')">
                        {{ $t('button.generate_qr') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import appService from "../../../services/appService.js";

export default {
    name: "TableQrModalComponent",
    props: {
        visible: {type: Boolean, default: false},
        qr: {type: Object, default: null},
    },
    emits: ['close', 'generate', 'regenerate', 'download', 'print'],
    methods: {
        permissionChecker(permission) {
            return appService.permissionChecker(permission);
        }
    }
};
</script>
