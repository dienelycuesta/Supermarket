<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>Ajuste de Stock</h4><a href="<?= url('admin/inventory') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<div class="row"><div class="col-lg-6">
<div class="card"><div class="card-body">
<form method="POST" action="<?= url('admin/inventory/adjust') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Buscar Producto</label>
        <input type="text" id="productSearch" class="form-control" placeholder="Escriba nombre, SKU o código de barras..." autocomplete="off">
        <input type="hidden" name="product_id" id="selectedProductId">
        <div id="searchResults" class="list-group mt-1" style="position:absolute;z-index:100;width:calc(100% - 2rem)"></div>
    </div>
    <div id="selectedProduct" class="alert alert-info d-none mb-3"></div>
    <div class="mb-3"><label class="form-label">Tipo</label><select name="type" class="form-select" required><option value="entry">Entrada (Agregar Stock)</option><option value="exit">Salida (Retirar Stock)</option><option value="adjustment">Ajuste</option></select></div>
    <div class="mb-3"><label class="form-label">Cantidad</label><input type="number" name="quantity" class="form-control" required min="1"></div>
    <div class="mb-3"><label class="form-label">Notas</label><textarea name="notes" class="form-control" rows="2" required></textarea></div>
    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Aplicar Ajuste</button>
</form>
</div></div></div></div>
<script>
let searchTimeout;
document.getElementById('productSearch').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value;
    if (q.length < 2) { document.getElementById('searchResults').innerHTML = ''; return; }
    searchTimeout = setTimeout(() => {
        fetch('<?= url('api/v1/products.php') ?>?action=search&q=' + encodeURIComponent(q))
        .then(r => r.json()).then(products => {
            const div = document.getElementById('searchResults');
            div.innerHTML = products.map(p =>
                `<a href="#" class="list-group-item list-group-item-action" onclick="selectProduct(${p.id},'${p.name.replace(/'/g,"\\'")}','${p.sku}',${p.stock});return false">${p.name} <small class="text-muted">(${p.sku}) - Stock: ${p.stock}</small></a>`
            ).join('');
        });
    }, 300);
});
function selectProduct(id, name, sku, stock) {
    document.getElementById('selectedProductId').value = id;
    document.getElementById('selectedProduct').innerHTML = `<strong>${name}</strong> (${sku}) - Stock Actual: ${stock}`;
    document.getElementById('selectedProduct').classList.remove('d-none');
    document.getElementById('searchResults').innerHTML = '';
    document.getElementById('productSearch').value = name;
}
</script>
