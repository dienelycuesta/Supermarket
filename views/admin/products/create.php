<?php $layout = 'admin/layouts/main'; $errors = Session::getFlash('errors') ?? []; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Agregar Producto</h4>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
</div>
<form method="POST" action="<?= url('admin/products/store') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">Información Básica</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= e(old('name')) ?>" required id="productName">
                        <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name'][0]) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" id="productSlug" value="<?= e(old('slug')) ?>"></div>
                    <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" class="form-control" rows="3"><?= e(old('description')) ?></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="category_id" class="form-select">
                                <option value="">Ninguno</option>
                                <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= old('category_id') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Proveedor</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">Ninguno</option>
                                <?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>" <?= old('supplier_id') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Marca</label><input type="text" name="brand" class="form-control" value="<?= e(old('brand')) ?>"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">Precios e Inventario</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKU</label><input type="text" name="sku" class="form-control" value="<?= e(old('sku')) ?>" placeholder="Auto-generado"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Código de Barras (EAN-13)</label><input type="text" name="barcode" class="form-control" value="<?= e(old('barcode')) ?>"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Precio de Costo</label><input type="number" name="cost_price" class="form-control" step="0.01" value="<?= e(old('cost_price', '0')) ?>"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Precio de Venta *</label><input type="number" name="sale_price" class="form-control" step="0.01" value="<?= e(old('sale_price')) ?>" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Precio de Comparación</label><input type="number" name="compare_price" class="form-control" step="0.01" value="<?= e(old('compare_price')) ?>"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Stock</label><input type="number" name="stock" class="form-control" value="<?= e(old('stock', '0')) ?>"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Stock Mínimo</label><input type="number" name="min_stock" class="form-control" value="<?= e(old('min_stock', '5')) ?>"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Stock Máximo</label><input type="number" name="max_stock" class="form-control" value="<?= e(old('max_stock', '1000')) ?>"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Unidad</label><select name="unit" class="form-select"><option value="unit">Unidad</option><option value="kg">Kg</option><option value="lb">Lb</option><option value="liter">Litro</option></select></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Impuesto %</label><input type="number" name="tax_rate" class="form-control" step="0.01" value="<?= e(old('tax_rate', '18')) ?>"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Peso</label><input type="number" name="weight" class="form-control" step="0.001" value="<?= e(old('weight')) ?>"></div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Imagen y Opciones</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Imagen del Producto</label><input type="file" name="image" class="form-control" accept="image/*" id="imageInput"><div id="imagePreview" class="mt-2"></div></div>
                    <div class="form-check mb-2"><input type="checkbox" name="is_featured" class="form-check-input" id="featured" value="1"><label class="form-check-label" for="featured">Producto Destacado</label></div>
                    <div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" id="active" value="1" checked><label class="form-check-label" for="active">Activo</label></div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-1"></i>Guardar Producto</button>
</form>
<script>
document.getElementById('productName').addEventListener('input', function() {
    document.getElementById('productSlug').value = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g,'').replace(/[\s]+/g,'-');
});
document.getElementById('imageInput').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    if (e.target.files[0]) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(e.target.files[0]);
        img.style.maxHeight = '150px';
        img.className = 'rounded';
        preview.appendChild(img);
    }
});
</script>
