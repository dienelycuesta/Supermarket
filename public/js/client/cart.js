/**
 * Cart page - quantity updates, remove items, recalculate totals
 */
(function(){
    'use strict';

    // Quantity +/- buttons
    document.querySelectorAll('.cart-qty-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.dataset.id;
            var action = this.dataset.action;
            var row = document.querySelector('tr[data-id="'+id+'"]');
            var input = row.querySelector('.cart-qty');
            var qty = parseInt(input.value) || 1;
            var max = parseInt(input.max) || 999;

            if(action === 'increase' && qty < max) qty++;
            else if(action === 'decrease' && qty > 1) qty--;
            else return;

            input.value = qty;
            updateCartItem(id, qty);
        });
    });

    // Manual quantity input
    document.querySelectorAll('.cart-qty').forEach(function(input){
        var timer;
        input.addEventListener('change', function(){
            clearTimeout(timer);
            var id = this.dataset.id;
            var qty = parseInt(this.value) || 1;
            var max = parseInt(this.max) || 999;
            if(qty < 1) qty = 1;
            if(qty > max) qty = max;
            this.value = qty;
            timer = setTimeout(function(){ updateCartItem(id, qty); }, 300);
        });
    });

    // Remove item
    document.querySelectorAll('.cart-remove').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.dataset.id;
            removeCartItem(id);
        });
    });

    function updateCartItem(id, quantity){
        $.ajax({
            url: BASE_URL + '/api/v1/cart.php',
            method: 'POST',
            data: {action:'update', product_id: id, quantity: quantity},
            dataType: 'json',
            success: function(res){
                if(res.success){
                    var row = document.querySelector('tr[data-id="'+id+'"]');
                    if(row) row.querySelector('.cart-line-total').textContent = formatMoney(res.line_total);
                    updateSummary(res);
                } else {
                    showToast(res.message || 'Error', 'error');
                }
            }
        });
    }

    function removeCartItem(id){
        $.ajax({
            url: BASE_URL + '/api/v1/cart.php',
            method: 'POST',
            data: {action:'remove', product_id: id},
            dataType: 'json',
            success: function(res){
                if(res.success){
                    var row = document.querySelector('tr[data-id="'+id+'"]');
                    if(row) row.remove();
                    updateSummary(res);
                    if(res.cart_count === 0) location.reload();
                    showToast('Eliminado', 'success');
                }
            }
        });
    }

    function updateSummary(res){
        var sub = document.getElementById('cartSubtotal');
        var tax = document.getElementById('cartTax');
        var total = document.getElementById('cartTotal');
        if(sub) sub.textContent = formatMoney(res.subtotal);
        if(tax) tax.textContent = formatMoney(res.tax);
        if(total) total.textContent = formatMoney(res.total);
        var badges = document.querySelectorAll('#cart-badge, #cart-badge-mobile, #cartBadge');
        badges.forEach(function(b){ b.textContent = res.cart_count > 0 ? res.cart_count : ''; });
    }
})();
