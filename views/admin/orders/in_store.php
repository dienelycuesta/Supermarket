<?php $layout = 'admin/layouts/main'; ?>
<h4 class="mb-4">Nueva Venta en Tienda</h4>
<form method="POST" action="<?= url('admin/orders/in-store') ?>" id="posForm">
<?= csrf_field() ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6"><label class="form-label">Nombre del Cliente</label><input type="text" name="customer_name" class="form-control" value="Sin nombre"></div>
                <div class="col-md-6"><label class="form-label">Buscar Producto</label>
                    <input type="text" id="posSearch" class="form-control" placeholder="Escanear c&oacute;digo de barras o buscar..." autocomplete="off">
                    <div id="posResults" class="list-group mt-1" style="position:absolute;z-index:100"></div>
                </div>
            </div>
            <div class="table-responsive">
            <table class="table" id="posTable"><thead><tr><th>Producto</th><th width="100">Cant</th><th width="120">Precio</th><th width="120">Subtotal</th><th width="50"></th></tr></thead>
            <tbody id="posItems"></tbody>
            <tfoot>
                <tr><td colspan="3" class="text-end"><strong>Subtotal</strong></td><td id="posSubtotal">RD$ 0.00</td><td></td></tr>
                <tr><td colspan="3" class="text-end"><strong>ITBIS (18%)</strong></td><td id="posTax">RD$ 0.00</td><td></td></tr>
                <tr><td colspan="3" class="text-end"><strong class="fs-5">Total</strong></td><td id="posTotal" class="fs-5 fw-bold">RD$ 0.00</td><td></td></tr>
            </tfoot></table>
            </div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-header">Pago</div><div class="card-body">
            <div class="mb-3"><label class="form-label">M&eacute;todo</label><select name="payment_method" class="form-select"><option value="cash">Efectivo</option><option value="card_pos">Tarjeta (POS)</option></select></div>
            <div class="mb-3"><label class="form-label">Referencia</label><input type="text" name="reference" class="form-control" placeholder="Opcional"></div>
            <button type="submit" class="btn btn-success btn-lg w-100" id="completeSale" disabled><i class="fas fa-check me-1"></i>Completar Venta</button>
        </div></div>
    </div>
</div>
</form>
<script>
let posItems = [];
let searchTo;
document.getElementById('posSearch').addEventListener('input', function(){
    clearTimeout(searchTo);
    const q = this.value;
    if(q.length < 2){document.getElementById('posResults').innerHTML='';return;}
    searchTo = setTimeout(()=>{
        fetch('<?= url('api/v1/products.php') ?>?action=search&q='+encodeURIComponent(q))
        .then(r=>r.json()).then(products=>{
            document.getElementById('posResults').innerHTML = products.map(p=>
                `<a href="#" class="list-group-item list-group-item-action" data-id="${p.id}" data-name="${p.name}" data-price="${p.sale_price}" data-sku="${p.sku}">${p.name} <small>(${p.sku}) - ${p.sale_price}</small></a>`
            ).join('');
            document.querySelectorAll('#posResults a').forEach(a=>a.addEventListener('click',function(e){
                e.preventDefault();
                addPosItem({id:this.dataset.id,name:this.dataset.name,price:parseFloat(this.dataset.price),sku:this.dataset.sku});
                document.getElementById('posResults').innerHTML='';
                document.getElementById('posSearch').value='';
            }));
        });
    },300);
});
function addPosItem(product){
    const existing = posItems.find(i=>i.id==product.id);
    if(existing){existing.qty++;} else {posItems.push({...product,qty:1});}
    renderPos();
}
function renderPos(){
    let html='',sub=0;
    posItems.forEach((item,i)=>{
        const lt = item.qty * item.price;
        sub += lt;
        html += `<tr>
            <td>${item.name}<input type="hidden" name="products[]" value="${item.id}"></td>
            <td><input type="number" name="quantities[]" class="form-control form-control-sm" value="${item.qty}" min="1" onchange="posItems[${i}].qty=parseInt(this.value);renderPos()"></td>
            <td>RD$ ${item.price.toFixed(2)}</td>
            <td>RD$ ${lt.toFixed(2)}</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="posItems.splice(${i},1);renderPos()"><i class="fas fa-times"></i></button></td>
        </tr>`;
    });
    document.getElementById('posItems').innerHTML = html;
    const tax = sub * 0.18;
    document.getElementById('posSubtotal').textContent = 'RD$ ' + sub.toFixed(2);
    document.getElementById('posTax').textContent = 'RD$ ' + tax.toFixed(2);
    document.getElementById('posTotal').textContent = 'RD$ ' + (sub+tax).toFixed(2);
    document.getElementById('completeSale').disabled = posItems.length === 0;
}
</script>
