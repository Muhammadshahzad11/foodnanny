<template>
    <section class="relative overflow-hidden pb-16 pt-4 min-h-[70vh]">
        <!-- Joyful success backdrop -->
        <div class="pointer-events-none absolute inset-0 thank-you-joy-bg" aria-hidden="true">
            <span
                v-for="(piece, index) in confetti"
                :key="index"
                class="thank-you-confetti"
                :style="piece.style"
            ></span>
        </div>

        <div class="relative z-10 w-full max-w-[520px] mx-auto mt-8 mb-12 p-6 sm:p-8 rounded-2xl bg-white shadow-xs text-center">
            <RestaurantSignupStepsComponent current="done"/>
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-primary text-white shadow-sm thank-you-icon-pop">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-8 w-8" aria-hidden="true">
                    <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="mb-3 text-2xl font-semibold text-heading capitalize">
                {{ $t('label.restaurant_registration_received') }}
            </h1>

            <p class="mb-5 text-sm leading-6 text-paragraph">
                {{ $t('message.restaurant_thank_you_intro') }}
            </p>

            <!-- Success / joyfulness bar (fills to 100%) -->
            <div class="mb-6 text-left">
                <div class="mb-1.5 flex items-center justify-between gap-2">
                    <span class="text-xs font-medium text-heading">{{ $t('label.registration_complete') }}</span>
                    <span class="text-xs font-semibold text-primary">{{ progress }}%</span>
                </div>
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-primary/10">
                    <div
                        class="h-full rounded-full bg-primary thank-you-progress-fill"
                        :style="{ width: progress + '%' }"
                    ></div>
                </div>
            </div>

            <div class="mb-6 rounded-xl border border-primary/20 bg-primary/5 px-4 py-4 text-left">
                <p class="mb-2 text-sm font-semibold text-heading">
                    {{ $t('message.restaurant_thank_you_approval_title') }}
                </p>
                <p class="text-sm leading-6 text-paragraph">
                    {{ $t('message.restaurant_thank_you_approval_body') }}
                </p>
            </div>

            <p class="mb-6 text-sm leading-6 text-paragraph">
                {{ $t('message.restaurant_thank_you_login_note') }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <router-link :to="{ name: 'auth.login' }" class="field-button">
                    {{ $t('label.go_to_login') }}
                </router-link>
                <router-link :to="{ name: 'frontend.home' }"
                             class="field-button border border-primary text-primary bg-white">
                    {{ $t('label.back_to_home') }}
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
import {useAuthStore} from "../../../stores/auth.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendRestaurantSignupStore} from "../../../stores/frontendRestaurantSignup.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import RestaurantSignupStepsComponent from "./RestaurantSignupStepsComponent.vue";

export default {
    name: "RestaurantThankYouComponent",
    components: {RestaurantSignupStepsComponent},
    setup() {
        const authStore                     = useAuthStore();
        const commonStore                   = useCommonStore();
        const frontendSettingStore          = useFrontendSettingStore();
        const frontendRestaurantSignupStore = useFrontendRestaurantSignupStore();

        return {
            authStore,
            commonStore,
            frontendSettingStore,
            frontendRestaurantSignupStore
        }
    },
    data() {
        return {
            progress: 0,
            confetti: [],
            progressTimer: null
        }
    },
    async mounted() {
        this.buildConfetti();
        this.animateProgress();

        // Clear wizard state so back navigation cannot resubmit
        this.frontendRestaurantSignupStore.$patch({
            code: "",
            phone: "",
            token: "",
            verify: false,
            ownerName: "",
            ownerEmail: "",
            ownerPassword: "",
            ownerVerify: false
        });

        if (this.authStore.status) {
            if (this.commonStore.location) {
                await this.$router.push({name: "frontend.restaurant"});
            } else {
                await this.$router.push({name: "frontend.home"});
            }
        }
    },
    beforeUnmount() {
        if (this.progressTimer) {
            clearInterval(this.progressTimer);
        }
    },
    methods: {
        buildConfetti() {
            const colors = ['#1AB759', '#86EFAC', '#FACC15', '#FB7185', '#60A5FA', '#A78BFA'];
            this.confetti = Array.from({length: 28}, (_, index) => {
                const size = 6 + Math.random() * 8;
                return {
                    style: {
                        left: `${Math.random() * 100}%`,
                        top: `${Math.random() * 100}%`,
                        width: `${size}px`,
                        height: `${size * (0.4 + Math.random())}px`,
                        backgroundColor: colors[index % colors.length],
                        animationDelay: `${Math.random() * 2.5}s`,
                        animationDuration: `${3.5 + Math.random() * 3}s`,
                        transform: `rotate(${Math.random() * 360}deg)`
                    }
                };
            });
        },
        animateProgress() {
            this.progress = 0;
            this.progressTimer = setInterval(() => {
                if (this.progress >= 100) {
                    this.progress = 100;
                    clearInterval(this.progressTimer);
                    this.progressTimer = null;
                    return;
                }
                this.progress = Math.min(100, this.progress + 4);
            }, 28);
        }
    }
}
</script>

<style scoped>
.thank-you-joy-bg {
    background:
        radial-gradient(circle at 15% 20%, rgba(26, 183, 89, 0.12), transparent 42%),
        radial-gradient(circle at 85% 15%, rgba(250, 204, 21, 0.12), transparent 38%),
        radial-gradient(circle at 70% 80%, rgba(96, 165, 250, 0.12), transparent 40%),
        radial-gradient(circle at 20% 85%, rgba(251, 113, 133, 0.1), transparent 36%);
}

.thank-you-confetti {
    position: absolute;
    border-radius: 2px;
    opacity: 0.75;
    animation: thank-you-float ease-in-out infinite;
}

.thank-you-icon-pop {
    animation: thank-you-pop 0.55s ease-out;
}

.thank-you-progress-fill {
    transition: width 0.05s linear;
}

@keyframes thank-you-float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
        opacity: 0.35;
    }
    50% {
        transform: translateY(-18px) rotate(18deg);
        opacity: 0.9;
    }
}

@keyframes thank-you-pop {
    0% {
        transform: scale(0.6);
        opacity: 0;
    }
    70% {
        transform: scale(1.08);
        opacity: 1;
    }
    100% {
        transform: scale(1);
    }
}
</style>
