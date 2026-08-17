/**
 * Catalog & Cart - Add to Cart with live mini-cart sidebar
 */
(function(){
    'use strict';

    function getOffcanvas(){
        var el = document.getElementById('cartOffcanvas');
        if(!el) return null;
        return bootstrap.Offcanvas.getOrCreateInstance(el);
    }

    // Add to Cart buttons
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.add-to-cart');
        if(!btn) return;
        e.preventDefault();

        var productId = btn.dataset.id;
        var qtyInput = btn.dataset.qtyInput ? document.getElementById(btn.dataset.qtyInput) : null;
        var quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

        btn.disabled = true;
        var origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';

        $.ajax({
            url: BASE_URL + '/api/v1/cart.php',
            method: 'POST',
            data: {action:'add', product_id: productId, quantity: quantity},
            dataType: 'json',
            success: function(res){
                if(res.success){
                    updateCartBadge(res.cart_count);
                    btn.innerHTML = '<i class="fas fa-check me-1"></i>';
                    setTimeout(function(){ btn.innerHTML = origHtml; btn.disabled = false; }, 1000);
                    loadMiniCart(true);
                } else {
                    showToast(res.message || 'Error', 'error');
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                }
            },
            error: function(){
                showToast('Error al agregar', 'error');
                btn.innerHTML = origHtml;
                btn.disabled = false;
            }
        });
    });

    // Mini cart quantity buttons (delegated)
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.mini-qty-btn');
        if(!btn) return;
        e.preventDefault();
        var id = btn.dataset.id;
        var action = btn.dataset.action;

        if(action === 'remove'){
            var item = document.querySelector('.mini-cart-item[data-id="'+id+'"]');
            if(item) item.style.opacity = '0.4';
            $.ajax({
                url: BASE_URL + '/api/v1/cart.php',
                method: 'POST',
                data: {action:'remove', product_id: id},
                dataType: 'json',
                success: function(res){
                    if(res.success){
                        updateCartBadge(res.cart_count);
                        loadMiniCart(false);
                    }
                }
            });
            return;
        }

        var row = document.querySelector('.mini-cart-item[data-id="'+id+'"]');
        if(!row) return;
        var qtyEl = row.querySelector('.mini-qty-val');
        var qty = parseInt(qtyEl.textContent) || 1;
        if(action === 'plus') qty++;
        else if(action === 'minus' && qty > 1) qty--;
        else return;

        qtyEl.textContent = qty;
        $.ajax({
            url: BASE_URL + '/api/v1/cart.php',
            method: 'POST',
            data: {action:'update', product_id: id, quantity: qty},
            dataType: 'json',
            success: function(res){
                if(res.success){
                    var lineEl = row.querySelector('.mini-line-total');
                    if(lineEl) lineEl.textContent = formatMoney(res.line_total);
                    updateMiniTotals(res);
                    updateCartBadge(res.cart_count);
                }
            }
        });
    });

    function loadMiniCart(openPanel){
        var loading = document.getElementById('miniCartLoading');
        var container = document.getElementById('miniCartItems');
        var empty = document.getElementById('miniCartEmpty');
        var footer = document.getElementById('miniCartFooter');

        // Show loading + open panel immediately so user sees feedback
        if(openPanel){
            if(loading) loading.style.display = 'block';
            if(container) container.innerHTML = '';
            if(empty) empty.style.display = 'none';
            if(footer) footer.style.display = 'none';
            var oc = getOffcanvas();
            if(oc) oc.show();
        }

        $.ajax({
            url: BASE_URL + '/api/v1/cart.php?action=get',
            method: 'GET',
            dataType: 'json',
            success: function(res){
                if(loading) loading.style.display = 'none';
                if(!res.success) return;

                if(!res.items || res.items.length === 0){
                    container.innerHTML = '';
                    empty.style.display = 'block';
                    footer.style.display = 'none';
                } else {
                    empty.style.display = 'none';
                    footer.style.display = 'block';
                    var html = '';
                    for(var i = 0; i < res.items.length; i++){
                        var item = res.items[i];
                        var img = item.image
                            ? '<img src="'+BASE_URL+'/public/uploads/products/'+item.image+'" class="rounded" style="width:45px;height:45px;object-fit:cover">'
                            : '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:45px;height:45px"><i class="fas fa-box text-muted small"></i></div>';
                        html += '<div class="mini-cart-item d-flex align-items-center p-2 border-bottom" data-id="'+item.id+'">'
                            + '<div class="me-2 flex-shrink-0">'+img+'</div>'
                            + '<div class="flex-grow-1 small overflow-hidden">'
                            + '<div class="fw-bold text-truncate">'+item.name+'</div>'
                            + '<div class="d-flex align-items-center gap-1 mt-1">'
                            + '<button class="btn btn-outline-secondary btn-sm px-1 py-0 mini-qty-btn" data-id="'+item.id+'" data-action="minus" style="font-size:.7rem;line-height:1.2">-</button>'
                            + '<span class="mini-qty-val px-1">'+item.quantity+'</span>'
                            + '<button class="btn btn-outline-secondary btn-sm px-1 py-0 mini-qty-btn" data-id="'+item.id+'" data-action="plus" style="font-size:.7rem;line-height:1.2">+</button>'
                            + '</div>'
                            + '</div>'
                            + '<div class="text-end ms-2 small flex-shrink-0">'
                            + '<div class="fw-bold mini-line-total">'+formatMoney(item.line_total)+'</div>'
                            + '<button class="btn btn-link btn-sm text-danger p-0 mini-qty-btn" data-id="'+item.id+'" data-action="remove" style="font-size:.65rem"><i class="fas fa-trash"></i></button>'
                            + '</div>'
                            + '</div>';
                    }
                    container.innerHTML = html;
                    updateMiniTotals(res);
                }
            },
            error: function(){
                if(loading) loading.style.display = 'none';
                if(empty) empty.style.display = 'block';
            }
        });
    }

    function updateMiniTotals(res){
        var s = document.getElementById('miniSubtotal');
        var t = document.getElementById('miniTax');
        var tt = document.getElementById('miniTotal');
        if(s) s.textContent = formatMoney(res.subtotal);
        if(t) t.textContent = formatMoney(res.tax);
        if(tt) tt.textContent = formatMoney(res.total);
    }

    function updateCartBadge(count){
        var badges = document.querySelectorAll('#cart-badge, #cart-badge-mobile, #cartBadge');
        badges.forEach(function(badge){
            badge.textContent = count > 0 ? count : '';
            if(badge.style) badge.style.display = count > 0 ? 'inline-block' : 'none';
        });
    }

    // Navbar cart icon -> open mini cart
    var cartLink = document.getElementById('openMiniCart');
    if(cartLink){
        cartLink.addEventListener('click', function(e){
            e.preventDefault();
            loadMiniCart(true);
        });
    }
})();
