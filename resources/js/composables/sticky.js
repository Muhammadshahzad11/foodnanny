import { ref, onMounted } from "vue";

export function useSticky() {

    const isSticky = ref(false)

    onMounted(() => {
        window.addEventListener('scroll', function() {
            isSticky.value = window.scrollY > 0;
        })
    })

    return {
        isSticky
    }
}
