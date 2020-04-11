(function ($) {
    'use strict';

    var stripe = Stripe(hh_stripe.publish_key);

    var elements = stripe.elements();

    var style = {
        base: {
            color: '#32325d',
            fontFamily: 'Poppins, Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    var card = elements.create('card', {style: style});

    card.mount('#card-element');

    card.addEventListener('change', function (event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
            displayError.style.display = 'block';
        } else {
            displayError.textContent = '';
            displayError.style.display = 'none';
        }
    });

    var stripe_token = $('.checkout-form input[name="stripe_token"]');

    var tokenRequest = function () {
        stripe.createToken(card).then(function (result) {
            if (result.error) {
                console.log('error')
            } else {
                stripe_token.val(result.token.id);
                $('.checkout-form-payment').submit();
            }
        });
    };

    $('.checkout-form .btn-complete-payment').click(function (e) {
        var t = $(this);
        var payment = $('.payment-item .payment-method:checked', t.closest('.checkout-form')).val();
        if (payment === 'stripe') {
            e.preventDefault();
            tokenRequest();
        }
    });
})(jQuery);