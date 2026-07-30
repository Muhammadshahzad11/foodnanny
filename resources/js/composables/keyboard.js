

export function useKeyboard() {

    const changeSheet = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", '⇧', "a", "s", "d", "f", "g", "h", "j", "k", '←', "l", "z", "x", "c", "v", "b", "n", "m"];
    const originalSheet = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, "Q", "W", "E", "R", "T", "Y", "U", "I", "O", "P", '⇧', "A", "S", "D", "F", "G", "H", "J", "K", '←', "L", "Z", "X", "C", "V", "B", "N", "M"];
    function backspace(id) {
        let textBoard = document.getElementById(id)
        textBoard.value = textBoard.value.slice(0, textBoard.value.length - 1)
    }

    const evaluateClick = (e) => {
        let btnClicked = e.target.classList[0]
        if (btnClicked !== "board" && btnClicked !== "rows") {
            let btnText = e.target.innerText
            let btnId = e.target.parentElement.parentElement.querySelector('input').getAttribute('id')
            action(btnText, btnId)
        }
    }

    function shift(btnId) {
        let btn = document.getElementById(btnId).parentElement.parentElement.querySelector('.shifter')
        if (btn?.classList.contains("no-shift")) {
            shiftOn(changeSheet, btnId)
        } else {
            shiftOn(originalSheet, btnId)
        }
    }

    function shiftOn(change, btnId) {
        let shift = document.getElementById(btnId).parentElement.parentElement.querySelector('.shifter');
        shift?.classList.toggle("no-shift");
        let btnChange = document.getElementById(btnId).parentElement.parentElement.querySelectorAll(".cng");
        Array.from(btnChange).forEach((value, index) => {
            value.innerText = change[index]
        });
    }

    const action = (btnText, btnId) => {
        switch (btnText) {
            case '←':
                backspace(btnId)
                break
            case "⇧":
                shift(btnId)
                break
            default:
                setText(btnText, btnId)
        }
    }

    const setText = (text, id) => {
        const element = document.getElementById(id);
        const cursorPosition = element.selectionStart;
        const currentText = element.value;
        element.value = currentText.substring(0, cursorPosition) + text + currentText.substring(cursorPosition);
        element.selectionStart = element.selectionEnd = cursorPosition + text.length;
        element.focus();
    };

    let boards = document.querySelectorAll(".board");
    const createKeyboard = (mfs) => {
        const element = document.getElementById(mfs)
        const divBoard = element.lastElementChild
        divBoard.addEventListener("click", evaluateClick)
        if (divBoard.childElementCount === 0) {
            originalSheet.map(sht => {
                const div = document.createElement('div')
                div.classList.add('cal-btn', 'cng')
                if (sht === '⇧') {
                    div.classList.add('no-shift', 'row-span-2', 'col-span-1', 'shifter')
                    div.textContent = sht
                }
                else if (sht === '←') {
                    div.classList.add('row-span-2', 'col-span-1')
                    div.textContent = sht
                }
                else {
                    div.textContent = sht
                }
                divBoard.appendChild(div)
            })
        }

    }

    return { createKeyboard }
}
