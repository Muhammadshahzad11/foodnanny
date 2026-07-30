import { onMounted } from "vue";

export function usePaper() {

    function handlePaper(event) {
        const currGroup = event.currentTarget.parentElement
        const isActivate = currGroup.className.includes('active')

        if(!isActivate) currGroup.classList.add('active')
        else currGroup.classList.remove('active')
    }

    onMounted(() => {
        window.addEventListener('click', function(event) {
            const paperGroups = document.querySelectorAll('.paper-group')

            paperGroups.forEach((paperGroup) => {
                const paperButton = paperGroup.querySelector('.paper-button')

                if(!paperButton.contains(event.target)) {
                    paperGroup.classList.remove('active')
                }
            })
        })
    })

    return {
        handlePaper
    }
}
