<?php $layout = 'admin/layouts/main'; $errors = Session::getFlash('errors') ?? []; ?>
<div class="d-flex justify-content-between mb-4"><h4>Agregar Categoría</h4><a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<div class="card"><div class="card-body">
<form method="POST" action="<?= url('admin/categories/store') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required></div>
    <div class="mb-3"><label class="form-label">Categoría Padre</label><select name="parent_id" class="form-select"><option value="">Ninguna (Nivel Principal)</option><?php foreach ($parentCategories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
    <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" class="form-control" rows="3"><?= e(old('description')) ?></textarea></div>
    <div class="mb-3"><label class="form-label">Imagen</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Orden de Clasificación</label><input type="number" name="sort_order" class="form-control" value="<?= e(old('sort_order', '0')) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Estado</label><select name="status" class="form-select"><option value="active">Activa</option><option value="inactive">Inactiva</option></select></div>
    </div>
    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar Categoría</button>
</form>
</div></div>
