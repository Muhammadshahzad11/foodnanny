import { ref } from "vue";

export function useTab() {

    const tabButton = ref('tab-button')
    const tabContent = ref('tab-content')
    const tabActive = ref('tab-active')

    function handleTab(event, targetID) {
        const targetBtns = document.querySelectorAll(`.${ tabButton.value }`);
        const targetDivs = document.querySelectorAll(`.${ tabContent.value }`);
        const currentBtn = event.currentTarget;
        const currentDiv = document.querySelector(`#${ targetID }`);


        targetBtns.forEach(item => item.classList.remove(tabActive.value));
        targetDivs.forEach(item => item.classList.remove(tabActive.value));


        currentBtn.classList.add(tabActive.value);
        currentDiv.classList.add(tabActive.value);
    }

    return { handleTab }
}
