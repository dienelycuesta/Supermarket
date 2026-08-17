<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>Nueva Orden de Compra</h4><a href="<?= url('admin/inventory/purchase-orders') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<form method="POST" action="<?= url('admin/inventory/purchase-orders/store') ?>">
<?= csrf_field() ?>
<div class="card mb-3"><div class="card-body">
    <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">Proveedor *</label><select name="supplier_id" class="form-select" required><option value="">Seleccionar...</option><?php foreach($suppliers as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4 mb-3"><label class="form-label">Fecha Esperada</label><input type="date" name="expected_date" class="form-control"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Notas</label><input type="text" name="notes" class="form-control"></div>
    </div>
</div></div>
<div class="card mb-3"><div class="card-header d-flex justify-content-between"><span>Artículos</span><button type="button" class="btn btn-sm btn-success" onclick="addRow()"><i class="fas fa-plus me-1"></i>Agregar Artículo</button></div>
<div class="card-body p-0"><div class="table-responsive"><table class="table mb-0" id="itemsTable">
<thead><tr><th>Producto</th><th width="120">Cantidad</th><th width="140">Costo Unitario</th><th width="140">Total</th><th width="50"></th></tr></thead>
<tbody id="itemsBody">
<tr>
    <td><input type="text" class="form-control product-search" placeholder="Buscar producto..." autocomplete="off"><input type="hidden" name="products[]" class="product-id"></td>
    <td><input type="number" name="quantities[]" class="form-control qty" min="1" value="1"></td>
    <td><input type="number" name="unit_costs[]" class="form-control cost" step="0.01" min="0" value="0"></td>
    <td class="line-total pt-3">RD$ 0.00</td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();calcTotal()"><i class="fas fa-times"></i></button></td>
</tr>
</tbody></table></div></div></div>
<div class="text-end mb-3"><strong>Total: <span id="poTotal">RD$ 0.00</span></strong></div>
<button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-1"></i>Crear Orden de Compra</button>
</form>
<script>
function addRow(){
    const row = document.querySelector('#itemsBody tr').cloneNode(true);
    row.querySelectorAll('input').forEach(i => { if(i.type!=='hidden') i.value = i.type==='number'?(i.classList.contains('qty')?1:0):''; else i.value=''; });
    row.querySelector('.line-total').textContent = 'RD$ 0.00';
    document.getElementById('itemsBody').appendChild(row);
    bindSearch(row.querySelector('.product-search'));
}
function calcTotal(){
    let total = 0;
    document.querySelectorAll('#itemsBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value)||0;
        const cost = parseFloat(row.querySelector('.cost').value)||0;
        const lt = qty * cost;
        row.querySelector('.line-total').textContent = 'RD$ ' + lt.toFixed(2);
        total += lt;
    });
    document.getElementById('poTotal').textContent = 'RD$ ' + total.toFixed(2);
}
document.addEventListener('input', e => { if(e.target.matches('.qty,.cost')) calcTotal(); });
function bindSearch(el){
    let to;
    el.addEventListener('input', function(){
        clearTimeout(to);
        const q = this.value, tr = this.closest('tr');
        if(q.length<2) return;
        to = setTimeout(()=>{
            fetch('<?= url('api/v1/products.php') ?>?action=search&q='+encodeURIComponent(q))
            .then(r=>r.json()).then(products=>{
                const existing = document.querySelector('.search-dropdown');
                if(existing) existing.remove();
                const div = document.createElement('div');
                div.className = 'search-dropdown list-group position-absolute';
                div.style.zIndex='1000';
                products.forEach(p=>{
                    const a = document.createElement('a');
                    a.className='list-group-item list-group-item-action small';
                    a.href='#';
                    a.textContent=`${p.name} (${p.sku})`;
                    a.onclick=function(e){e.preventDefault();tr.querySelector('.product-id').value=p.id;el.value=p.name;tr.querySelector('.cost').value=p.cost_price||0;calcTotal();div.remove();};
                    div.appendChild(a);
                });
                el.parentNode.appendChild(div);
            });
        },300);
    });
}
document.querySelectorAll('.product-search').forEach(bindSearch);
document.addEventListener('click', e => { if(!e.target.matches('.product-search')) document.querySelectorAll('.search-dropdown').forEach(d=>d.remove()); });
</script>
