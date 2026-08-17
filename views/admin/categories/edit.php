<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>Editar Categoría</h4><a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<div class="card"><div class="card-body">
<form method="POST" action="<?= url('admin/categories/' . $category['id'] . '/update') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="name" class="form-control" value="<?= e($category['name']) ?>" required></div>
    <div class="mb-3"><label class="form-label">Categoría Padre</label><select name="parent_id" class="form-select"><option value="">Ninguna</option><?php foreach ($parentCategories as $c): ?><?php if($c['id'] != $category['id']): ?><option value="<?= $c['id'] ?>" <?= $category['parent_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endif; ?><?php endforeach; ?></select></div>
    <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" class="form-control" rows="3"><?= e($category['description']) ?></textarea></div>
    <?php if ($category['image']): ?><div class="mb-2"><img src="<?= e(UPLOAD_URL . $category['image']) ?>" class="rounded" style="max-height:80px"></div><?php endif; ?>
    <div class="mb-3"><label class="form-label">Cambiar Imagen</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Orden de Clasificación</label><input type="number" name="sort_order" class="form-control" value="<?= e($category['sort_order']) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Estado</label><select name="status" class="form-select"><option value="active" <?= $category['status']==='active'?'selected':'' ?>>Activa</option><option value="inactive" <?= $category['status']==='inactive'?'selected':'' ?>>Inactiva</option></select></div>
    </div>
    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Actualizar</button>
</form>
</div></div>
