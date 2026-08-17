/**
 * Admin Inventory - Stock management, purchase order items
 */
(function(){
    'use strict';

    // Init DataTables for inventory tables
    if($.fn.DataTable){
        ['inventoryTable','movementsTable','alertsTable','poTable'].forEach(function(id){
            const el = document.getElementById(id);
            if(el) $(el).DataTable({pageLength:25, order:[[0,'desc']]});
        });
    }

    // Purchase Order - Add item row
    const addItemBtn = document.getElementById('addPOItem');
    if(addItemBtn){
        let itemIndex = document.querySelectorAll('.po-item-row').length;
        addItemBtn.addEventListener('click', function(){
            const tbody = document.getElementById('poItemsBody');
            const row = document.createElement('tr');
            row.className = 'po-item-row';
            row.innerHTML = `
                <td>
                    <input type="text" class="form-control form-control-sm po-product-search" placeholder="Buscar producto..." data-index="${itemIndex}">
                    <input type="hidden" name="items[${itemIndex}][product_id]" class="po-product-id">
                </td>
                <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm po-qty" min="1" value="1" required></td>
                <td><input type="number" name="items[${itemIndex}][unit_cost]" class="form-control form-control-sm po-cost" step="0.01" min="0" required></td>
                <td class="po-line-total fw-bold">RD$0.00</td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-po-item"><i class="fas fa-times"></i></button></td>
            `;
            tbody.appendChild(row);
            itemIndex++;
            bindPOEvents(row);
        });
    }

    // Remove PO item row
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.remove-po-item');
        if(btn){
            btn.closest('tr').remove();
            recalcPOTotal();
        }
    });

    // Bind PO row events (product search, qty/cost change)
    function bindPOEvents(row){
        const searchInput = row.querySelector('.po-product-search');
        if(searchInput){
            let timer;
            searchInput.addEventListener('input', function(){
                clearTimeout(timer);
                const q = this.value.trim();
                const input = this;
                if(q.length < 2) return;
                timer = setTimeout(function(){
                    $.getJSON(BASE_URL + '/api/v1/products.php', {search:q}, function(res){
                        let dd = input.nextElementSibling?.classList?.contains('po-dropdown') ? input.nextElementSibling : null;
                        if(!dd){
                            dd = document.createElement('div');
                            dd.className = 'po-dropdown list-group position-absolute';
                            dd.style.zIndex = '1050';
                            dd.style.maxHeight = '200px';
                            dd.style.overflowY = 'auto';
                            input.parentNode.style.position = 'relative';
                            input.after(dd);
                        }
                        dd.innerHTML = '';
                        (res.products||[]).forEach(function(p){
                            const a = document.createElement('a');
                            a.href = '#';
                            a.className = 'list-group-item list-group-item-action small py-1';
                            a.textContent = p.name + ' (' + p.sku + ')';
                            a.addEventListener('click', function(e2){
                                e2.preventDefault();
                                input.value = p.name;
                                row.querySelector('.po-product-id').value = p.id;
                                dd.remove();
                            });
                            dd.appendChild(a);
                        });
                    });
                }, 300);
            });
        }

        // Recalculate on qty/cost change
        row.querySelectorAll('.po-qty, .po-cost').forEach(function(inp){
            inp.addEventListener('input', function(){
                const qty = parseFloat(row.querySelector('.po-qty').value) || 0;
                const cost = parseFloat(row.querySelector('.po-cost').value) || 0;
                row.querySelector('.po-line-total').textContent = formatMoney(qty * cost);
                recalcPOTotal();
            });
        });
    }

    // Init existing PO rows
    document.querySelectorAll('.po-item-row').forEach(bindPOEvents);

    function recalcPOTotal(){
        let total = 0;
        document.querySelectorAll('.po-item-row').forEach(function(row){
            const qty = parseFloat(row.querySelector('.po-qty')?.value) || 0;
            const cost = parseFloat(row.querySelector('.po-cost')?.value) || 0;
            total += qty * cost;
        });
        const totalEl = document.getElementById('poTotal');
        if(totalEl) totalEl.textContent = formatMoney(total);
    }
})();
