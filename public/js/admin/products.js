/**
 * Admin Products - DataTable init, image preview, barcode input
 */
(function(){
    'use strict';

    // Init DataTable
    if($.fn.DataTable && document.getElementById('productsTable')){
        $('#productsTable').DataTable({
            pageLength: 25,
            order: [[0,'desc']],
            language: {search:'Buscar:', lengthMenu:'Mostrar _MENU_ registros', info:'Mostrando _START_ a _END_ de _TOTAL_ registros', paginate:{previous:'Anterior', next:'Siguiente'}, zeroRecords:'Sin resultados', infoEmpty:'Sin registros', infoFiltered:'(filtrado de _MAX_ registros)'}
        });
    }

    // Image preview on file input change
    const imageInput = document.getElementById('productImage');
    const imagePreview = document.getElementById('imagePreview');
    if(imageInput && imagePreview){
        imageInput.addEventListener('change', function(){
            const file = this.files[0];
            if(file){
                const reader = new FileReader();
                reader.onload = function(e){ imagePreview.src = e.target.result; imagePreview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }

    // Auto-generate slug from name
    const nameInput = document.getElementById('productName');
    const slugInput = document.getElementById('productSlug');
    if(nameInput && slugInput && !slugInput.value){
        nameInput.addEventListener('input', function(){
            let slug = this.value.toLowerCase()
                .replace(/[áàäâ]/g,'a').replace(/[éèëê]/g,'e').replace(/[íìïî]/g,'i')
                .replace(/[óòöô]/g,'o').replace(/[úùüû]/g,'u').replace(/ñ/g,'n')
                .replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
            slugInput.value = slug;
        });
    }

    // Barcode EAN-13 validation
    const barcodeInput = document.getElementById('productBarcode');
    if(barcodeInput){
        barcodeInput.addEventListener('blur', function(){
            const val = this.value.trim();
            if(val && val.length === 13){
                let sum = 0;
                for(let i = 0; i < 12; i++) sum += parseInt(val[i]) * (i % 2 === 0 ? 1 : 3);
                const check = (10 - (sum % 10)) % 10;
                if(check !== parseInt(val[12])){
                    this.classList.add('is-invalid');
                    showToast('Dígito verificador EAN-13 inválido','warning');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            } else if(val && val.length !== 13){
                this.classList.add('is-invalid');
            }
        });
    }

    // Product search autocomplete (for inventory adjustments etc.)
    const prodSearch = document.getElementById('productSearch');
    if(prodSearch){
        let timer;
        prodSearch.addEventListener('input', function(){
            clearTimeout(timer);
            const q = this.value.trim();
            if(q.length < 2){ document.getElementById('productResults')?.remove(); return; }
            timer = setTimeout(function(){
                $.getJSON(BASE_URL + '/api/v1/products.php', {search:q}, function(res){
                    let container = document.getElementById('productResults');
                    if(!container){
                        container = document.createElement('div');
                        container.id = 'productResults';
                        container.className = 'list-group position-absolute w-100';
                        container.style.zIndex = '1050';
                        prodSearch.parentNode.style.position = 'relative';
                        prodSearch.parentNode.appendChild(container);
                    }
                    container.innerHTML = '';
                    if(res.products && res.products.length){
                        res.products.forEach(function(p){
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action small';
                            item.innerHTML = '<strong>'+p.name+'</strong> <span class="text-muted">('+p.sku+')</span> - Stock: '+p.stock;
                            item.addEventListener('click', function(e){
                                e.preventDefault();
                                document.getElementById('productId').value = p.id;
                                prodSearch.value = p.name + ' (' + p.sku + ')';
                                container.remove();
                            });
                            container.appendChild(item);
                        });
                    } else {
                        container.innerHTML = '<div class="list-group-item text-muted small">No se encontraron productos</div>';
                    }
                });
            }, 300);
        });
        document.addEventListener('click', function(e){
            if(!e.target.closest('#productSearch')) document.getElementById('productResults')?.remove();
        });
    }
})();
