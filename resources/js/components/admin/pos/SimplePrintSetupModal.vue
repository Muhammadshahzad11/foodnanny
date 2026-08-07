<template>
    <teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-[95] flex items-center justify-center p-4 bg-black/50"
            @click.self="close"
        >
            <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden" @click.stop>
                <div class="p-6 text-center border-b border-[#EFF0F6]">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-2xl">
                        !
                    </div>
                    <h3 class="text-xl font-semibold text-heading mb-1">
                        {{ $t('message.simple_print_title') }}
                    </h3>
                    <p class="text-sm text-[#6E7191] leading-relaxed">
                        {{ $t('message.simple_print_body') }}
                    </p>
                    <p class="mt-2 text-xs font-semibold text-heading">
                        {{ $t('message.simple_print_detected_os', { os: osLabel }) }}
                    </p>
                </div>

                <div class="px-6 py-5 text-left space-y-3">
                    <ol class="list-decimal pl-5 text-sm text-heading space-y-2 leading-relaxed">
                        <li>{{ printerStep }}</li>
                        <li>{{ $t('message.simple_print_step_download') }}</li>
                        <li>{{ runStep }}</li>
                    </ol>

                    <p v-if="statusText" class="text-xs rounded-lg px-3 py-2" :class="statusOk ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800'">
                        {{ statusText }}
                    </p>
                </div>

                <div class="p-6 pt-0 flex flex-col gap-2">
                    <button
                        type="button"
                        class="w-full rounded-3xl py-3 text-sm font-semibold text-white bg-primary"
                        @click.prevent="enablePrinting"
                    >
                        {{ downloading ? $t('label.downloading') : $t('button.enable_printing') }}
                    </button>
                    <button
                        type="button"
                        class="w-full rounded-3xl py-2.5 text-sm font-medium border border-[#EFF0F6] text-heading"
                        @click.prevent="checkAgain"
                    >
                        {{ checking ? $t('label.checking') : $t('button.i_ran_it_check_again') }}
                    </button>
                    <button
                        type="button"
                        class="w-full rounded-3xl py-2 text-xs font-medium text-[#6E7191]"
                        @click.prevent="close"
                    >
                        {{ $t('button.close') }}
                    </button>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script>
import {
    clientOsLabel,
    detectClientOs,
    downloadSimplePrintHelper,
    isClientPrintReady,
    markSimplePrintReady,
} from '../../../services/simplePrintSetup.js';
import alertService from '../../../services/alertService.js';

export default {
    name: 'SimplePrintSetupModal',
    props: {
        modelValue: { type: Boolean, default: false },
    },
    emits: ['update:modelValue', 'ready'],
    data() {
        return {
            downloading: false,
            checking: false,
            statusText: '',
            statusOk: false,
            pollTimer: null,
            os: detectClientOs(),
            lastFilename: '',
        };
    },
    computed: {
        open: {
            get() {
                return this.modelValue;
            },
            set(v) {
                this.$emit('update:modelValue', v);
            },
        },
        osLabel() {
            return clientOsLabel(this.os);
        },
        printerStep() {
            if (this.os === 'mac') return this.$t('message.simple_print_step_printer_mac');
            if (this.os === 'linux') return this.$t('message.simple_print_step_printer_linux');
            return this.$t('message.simple_print_step_printer');
        },
        runStep() {
            if (this.os === 'mac') return this.$t('message.simple_print_step_run_mac');
            if (this.os === 'linux') return this.$t('message.simple_print_step_run_linux');
            return this.$t('message.simple_print_step_run');
        },
    },
    watch: {
        open(val) {
            if (val) {
                this.os = detectClientOs();
                this.statusText = '';
                this.statusOk = false;
                this.startPolling();
            } else {
                this.stopPolling();
            }
        },
    },
    beforeUnmount() {
        this.stopPolling();
    },
    methods: {
        close() {
            this.open = false;
        },
        enablePrinting() {
            this.downloading = true;
            try {
                const { filename } = downloadSimplePrintHelper();
                this.lastFilename = filename;
                this.statusText = this.$t('message.simple_print_downloaded_file', { file: filename });
                this.statusOk = false;
                alertService.success(this.$t('message.simple_print_downloaded_file', { file: filename }));
            } finally {
                this.downloading = false;
            }
        },
        async checkAgain() {
            this.checking = true;
            try {
                const ready = await isClientPrintReady();
                if (ready) {
                    markSimplePrintReady();
                    this.statusOk = true;
                    this.statusText = this.$t('message.simple_print_ready');
                    alertService.success(this.$t('message.simple_print_ready'));
                    this.$emit('ready');
                    setTimeout(() => this.close(), 600);
                } else {
                    this.statusOk = false;
                    this.statusText = this.$t('message.simple_print_not_ready_yet');
                }
            } finally {
                this.checking = false;
            }
        },
        startPolling() {
            this.stopPolling();
            this.pollTimer = setInterval(async () => {
                if (!this.open) return;
                const ready = await isClientPrintReady();
                if (ready) {
                    markSimplePrintReady();
                    this.statusOk = true;
                    this.statusText = this.$t('message.simple_print_ready');
                    this.$emit('ready');
                    this.stopPolling();
                    setTimeout(() => this.close(), 500);
                }
            }, 4000);
        },
        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },
    },
};
</script>
