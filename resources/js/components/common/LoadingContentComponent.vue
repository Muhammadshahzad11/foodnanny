<template>
    <Transition name="app-inline-loader-fade">
        <div
            v-if="props?.isActive"
            class="app-inline-loader"
            role="status"
            aria-live="polite"
            aria-busy="true"
        >
            <div class="app-inline-loader__backdrop"></div>
            <div class="app-inline-loader__pulse" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </Transition>
</template>

<script>
export default {
    name: "LoadingContentComponent",
    props: {
        props: {
            type: Object,
            default: () => ({ isActive: false }),
        },
    },
};
</script>

<style scoped>
.app-inline-loader {
    position: absolute;
    inset: 0;
    z-index: 40;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: inherit;
    overflow: hidden;
}
.app-inline-loader__backdrop {
    position: absolute;
    inset: 0;
    background: rgb(255 255 255 / 0.55);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}
.app-inline-loader__pulse {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.app-inline-loader__pulse span {
    width: 0.55rem;
    height: 0.55rem;
    border-radius: 999px;
    background: rgb(20 138 60);
    animation: app-inline-bounce 0.9s ease-in-out infinite;
}
.app-inline-loader__pulse span:nth-child(2) {
    animation-delay: 0.15s;
    background: rgb(10 61 40);
}
.app-inline-loader__pulse span:nth-child(3) {
    animation-delay: 0.3s;
    background: rgb(74 222 128);
}
.app-inline-loader-fade-enter-active,
.app-inline-loader-fade-leave-active {
    transition: opacity 0.18s ease;
}
.app-inline-loader-fade-enter-from,
.app-inline-loader-fade-leave-to {
    opacity: 0;
}
@keyframes app-inline-bounce {
    0%,
    80%,
    100% {
        transform: translateY(0);
        opacity: 0.55;
    }
    40% {
        transform: translateY(-6px);
        opacity: 1;
    }
}
</style>
