<?php $layout = 'admin/layouts/main'; $errors = Session::getFlash('errors') ?? []; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Editar Producto</h4>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
</div>
<form method="POST" action="<?= url('admin/products/' . $product['id'] . '/update') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">Información Básica</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="name" class="form-control" value="<?= e($product['name']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?= e($product['slug']) ?>"></div>
                    <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" class="form-control" rows="3"><?= e($product['description']) ?></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Categoría</label><select name="category_id" class="form-select"><option value="">Ninguno</option><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Proveedor</label><select name="supplier_id" class="form-select"><option value="">Ninguno</option><?php foreach ($suppliers as $s): ?><option value="<?= $s['id'] ?>" <?= $product['supplier_id'] == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Marca</label><input type="text" name="brand" class="form-control" value="<?= e($product['brand']) ?>"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">Precios e Inventario</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">SKU</label><input type="text" name="sku" class="form-control" value="<?= e($product['sku']) ?>"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Código de Barras</label><input type="text" name="barcode" class="form-control" value="<?= e($product['barcode']) ?>"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Precio de Costo</label><input type="number" name="cost_price" class="form-control" step="0.01" value="<?= e($product['cost_price']) ?>"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Precio de Venta *</label><input type="number" name="sale_price" class="form-control" step="0.01" value="<?= e($product['sale_price']) ?>" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Precio de Comparación</label><input type="number" name="compare_price" class="form-control" step="0.01" value="<?= e($product['compare_price']) ?>"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Stock Mínimo</label><input type="number" name="min_stock" class="form-control" value="<?= e($product['min_stock']) ?>"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Stock Máximo</label><input type="number" name="max_stock" class="form-control" value="<?= e($product['max_stock']) ?>"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Unidad</label><select name="unit" class="form-select"><?php foreach(['unit','kg','lb','liter'] as $u): ?><option value="<?= $u ?>" <?= $product['unit'] === $u ? 'selected' : '' ?>><?= ucfirst($u) ?></option><?php endforeach; ?></select></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Impuesto %</label><input type="number" name="tax_rate" class="form-control" step="0.01" value="<?= e($product['tax_rate']) ?>"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Peso</label><input type="number" name="weight" class="form-control" step="0.001" value="<?= e($product['weight']) ?>"></div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Imagen y Opciones</div>
                <div class="card-body">
                    <?php if ($product['image']): ?><div class="mb-2"><img src="<?= e(UPLOAD_URL . $product['image']) ?>" class="rounded" style="max-height:120px"></div><?php endif; ?>
                    <div class="mb-3"><label class="form-label">Cambiar Imagen</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                    <div class="form-check mb-2"><input type="checkbox" name="is_featured" class="form-check-input" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>><label class="form-check-label">Destacado</label></div>
                    <div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" value="1" <?= $product['is_active'] ? 'checked' : '' ?>><label class="form-check-label">Activo</label></div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-1"></i>Actualizar Producto</button>
</form>
