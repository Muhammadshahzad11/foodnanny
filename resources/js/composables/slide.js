import { ref } from "vue";

export function useSlide(param) {

    const toggleSlide = ref(param || false);

    function handleSlide(id) {
        const targetElement = document.querySelector(`#${id}`);
        toggleSlide.value = !toggleSlide.value

        targetElement.classList.add("transition-all", "duration-300", "ease-in-out");
        if (!toggleSlide.value) {
            targetElement.style.height = targetElement.scrollHeight + 'px';
            targetElement.style.overflow = 'hidden';
            void targetElement.offsetHeight;
            targetElement.style.height = '0px';
            targetElement.style.opacity = '0';
            targetElement.style.visibility = 'hidden';
        } else {
            targetElement.style.height = targetElement.scrollHeight + 'px';
            targetElement.style.opacity = '1';
            targetElement.style.visibility = 'visible';
            targetElement.style.overflow = 'hidden';
            targetElement.addEventListener('transitionend', function handler(e) {
                if (e.propertyName === 'height') {
                    targetElement.style.height = 'auto';
                    targetElement.style.overflow = 'visible';
                    targetElement.removeEventListener('transitionend', handler);
                }
            });
        }
    }

    return {
        handleSlide,
        toggleSlide
    }
}
