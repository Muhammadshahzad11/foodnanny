if (stripeKey) {
    "use strict";

    const stripe = Stripe(stripeKey);
    const elements = stripe.elements();

    const style = {
        base: {
            fontSize: '16px',
            color: '#32325d',
            border: '1px solid red',
        },
    };

    const card = elements.create('card', { style });
    card.mount('#card-element');

    // Define globally if needed
    window.stripe_payment = function () {
        const paymentMethodInput = document.getElementById('payment_method');
        if (paymentMethodInput) {
            const parent = paymentMethodInput.parentElement;
            if (parent && parent.classList.contains('has-error')) {
                parent.classList.remove('has-error');
            }
        }

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                const errorElement = document.getElementById('card-errors');
                if (errorElement) {
                    errorElement.textContent = result.error.message;
                }
            } else {
                stripeTokenHandler(result.token);
            }
        });
    };

    function stripeTokenHandler(token) {
        const form = document.getElementById('paymentForm');
        if (!form) return;

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'stripeToken';
        hiddenInput.value = token.id;
        form.appendChild(hiddenInput);

        form.submit();
    }
}
