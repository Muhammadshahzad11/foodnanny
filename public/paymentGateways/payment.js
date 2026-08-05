"use strict";

// Get selected payment method from form
let paymentMethod = document.querySelector('#paymentForm input[name="paymentMethod"]:checked')?.value;
let showStatus = false;

// Show/hide gateway elements
for (let item in gateway) {
    const element = document.getElementById(item + '_div');
    if (item === paymentMethod && gateway[item]) {
        showStatus = true;
        if (element) element.style.display = 'block';
    } else {
        if (element) element.style.display = 'none';
    }
}

let clickGateway = false;
for (let item in onClickGateway) {
    if (item === paymentMethod) {
        showStatus = true;
        clickGateway = true;
        break;
    }
}

let form = document.getElementById('paymentForm');

if (showStatus) {
    // Show/Hide button groups
    document.getElementById('loading-show')?.classList.add('hidden');
    document.getElementById('confirmBtn')?.classList.remove('hidden');
    document.getElementById('backBtn')?.classList.remove('hidden');

    if (clickGateway) {
        document.getElementById('confirmBtn')?.classList.add('hidden');
        document.getElementById('backBtn')?.classList.add('hidden');
    }

    // Form submit handler
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        let submit = false;
        for (let item in submitGateway) {
            if (item === paymentMethod) {
                submit = true;
                if (typeof window[paymentMethod + '_payment'] === 'function') {
                    window[paymentMethod + '_payment']();
                }
                break;
            }
        }

        if (!submit) {
            form.submit();
        }
    });

} else {
    form.submit();
}
