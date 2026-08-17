/**
 * Checkout page - payment method toggle, Stripe Elements integration
 */
(function(){
    'use strict';

    // Payment method toggle
    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio){
        radio.addEventListener('change', function(){
            // Bank transfer details
            const bankDetails = document.getElementById('bankDetails');
            if(bankDetails) bankDetails.classList.toggle('d-none', this.value !== 'bank_transfer');

            // Stripe card element
            const stripeSection = document.getElementById('stripeCardSection');
            if(stripeSection) stripeSection.classList.toggle('d-none', this.value !== 'stripe');
        });
    });

    // Stripe Elements (only if Stripe key is available)
    if(typeof STRIPE_KEY !== 'undefined' && STRIPE_KEY){
        const stripe = Stripe(STRIPE_KEY);
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style:{
                base:{fontSize:'16px',color:'#333','::placeholder':{color:'#aab7c4'}},
                invalid:{color:'#dc3545'}
            }
        });

        const cardMount = document.getElementById('stripeCardElement');
        if(cardMount) cardElement.mount('#stripeCardElement');

        const form = document.getElementById('checkoutForm');
        if(form){
            form.addEventListener('submit', function(e){
                const method = document.querySelector('input[name="payment_method"]:checked')?.value;
                if(method !== 'stripe') return; // Non-Stripe, let form submit normally

                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

                // Create PaymentIntent via API
                $.ajax({
                    url: BASE_URL + '/api/v1/checkout.php',
                    method: 'POST',
                    data: {action:'create_intent'},
                    dataType: 'json',
                    success: function(res){
                        if(!res.success){
                            showToast(res.message || 'Error al crear el pago', 'error');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Realizar Pedido';
                            return;
                        }
                        // Confirm payment with Stripe
                        stripe.confirmCardPayment(res.client_secret, {
                            payment_method: {card: cardElement}
                        }).then(function(result){
                            if(result.error){
                                showToast(result.error.message, 'error');
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Realizar Pedido';
                            } else if(result.paymentIntent.status === 'succeeded'){
                                // Add payment intent ID and submit form
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'stripe_payment_intent';
                                input.value = result.paymentIntent.id;
                                form.appendChild(input);
                                form.submit();
                            }
                        });
                    },
                    error: function(){
                        showToast('Error al procesar el pago', 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Realizar Pedido';
                    }
                });
            });
        }
    }
})();
