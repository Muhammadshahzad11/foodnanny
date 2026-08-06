<template>
    <Teleport to="body">
        <Transition name="app-loader-fade">
            <div
                v-if="props?.isActive"
                class="app-loader"
                role="status"
                aria-live="polite"
                aria-busy="true"
            >
                <div class="app-loader__backdrop"></div>
                <div class="app-loader__card">
                    <div class="app-loader__ring" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <p class="app-loader__title">{{ label }}</p>
                    <p class="app-loader__hint">{{ hint }}</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script>
export default {
    name: "LoadingComponent",
    props: {
        props: {
            type: Object,
            default: () => ({ isActive: false }),
        },
        label: {
            type: String,
            default: "Please wait…",
        },
        hint: {
            type: String,
            default: "Loading your experience",
        },
    },
};
</script>

<style scoped>
.app-loader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    pointer-events: all;
}
.app-loader__backdrop {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 70% 50% at 50% 40%, rgb(20 138 60 / 0.22), transparent 70%),
        rgb(10 61 40 / 0.42);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
}
.app-loader__card {
    position: relative;
    z-index: 1;
    min-width: 11.5rem;
    max-width: 18rem;
    padding: 1.5rem 1.75rem;
    text-align: center;
    border-radius: 1.25rem;
    background: rgb(255 255 255 / 0.94);
    border: 1px solid rgb(20 138 60 / 0.18);
    box-shadow:
        0 18px 48px rgb(10 61 40 / 0.28),
        0 0 0 1px rgb(255 255 255 / 0.4) inset;
}
.app-loader__ring {
    position: relative;
    width: 3.25rem;
    height: 3.25rem;
    margin: 0 auto 1rem;
}
.app-loader__ring span {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    border: 3px solid transparent;
    animation: app-loader-spin 1s linear infinite;
}
.app-loader__ring span:nth-child(1) {
    border-top-color: rgb(20 138 60);
    animation-duration: 0.9s;
}
.app-loader__ring span:nth-child(2) {
    inset: 5px;
    border-right-color: rgb(10 61 40);
    animation-direction: reverse;
    animation-duration: 1.15s;
}
.app-loader__ring span:nth-child(3) {
    inset: 10px;
    border-bottom-color: rgb(74 222 128);
    animation-duration: 0.75s;
}
.app-loader__title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgb(10 61 40);
    letter-spacing: 0.01em;
}
.app-loader__hint {
    margin: 0.35rem 0 0;
    font-size: 0.75rem;
    color: rgb(110 113 145);
}
.app-loader-fade-enter-active,
.app-loader-fade-leave-active {
    transition: opacity 0.22s ease;
}
.app-loader-fade-enter-active .app-loader__card,
.app-loader-fade-leave-active .app-loader__card {
    transition: transform 0.22s ease, opacity 0.22s ease;
}
.app-loader-fade-enter-from,
.app-loader-fade-leave-to {
    opacity: 0;
}
.app-loader-fade-enter-from .app-loader__card,
.app-loader-fade-leave-to .app-loader__card {
    opacity: 0;
    transform: translateY(8px) scale(0.96);
}
@keyframes app-loader-spin {
    to {
        transform: rotate(360deg);
    }
}
@media (prefers-reduced-motion: reduce) {
    .app-loader__ring span {
        animation-duration: 1.6s;
    }
}
</style>
