export function useModal() {

    function openModal(targetID) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement?.classList.add('modal-active');
        document.body?.classList.add('overflow-hidden');
    }

    function closeModal(targetID) {
        const targetElement = document.querySelector(`#${targetID}`);
        targetElement?.classList.remove('modal-active');
        document.body?.classList.remove('overflow-hidden');
    }

    return { openModal, closeModal }
}
