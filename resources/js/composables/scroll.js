import { ref, onMounted, onBeforeUnmount } from "vue";

export function useScroll() {

    const lastScrollTop = ref(0);
    const isScrollingUp = ref(true);

    function handleScroll() {
        const st = window.scrollY || document.documentElement.scrollTop;
        isScrollingUp.value = st <= lastScrollTop.value;
        lastScrollTop.value = st <= 0 ? 0 : st;
    }

    onMounted(() => window.addEventListener('scroll', handleScroll));
    onBeforeUnmount(() => window.removeEventListener('scroll', handleScroll));

    return {
        isScrollingUp
    }
}
