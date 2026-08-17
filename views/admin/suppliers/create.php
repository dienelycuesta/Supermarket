<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>Agregar Proveedor</h4><a href="<?= url('admin/suppliers') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a></div>
<div class="card"><div class="card-body">
<form method="POST" action="<?= url('admin/suppliers/store') ?>">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Nombre *</label><input type="text" name="name" class="form-control" value="<?= e(old('name')) ?>" required></div>
        <div class="col-md-6 mb-3"><label class="form-label">Persona de Contacto</label><input type="text" name="contact_person" class="form-control" value="<?= e(old('contact_person')) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Teléfono</label><input type="tel" name="phone" class="form-control" value="<?= e(old('phone')) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">RNC</label><input type="text" name="rnc" class="form-control" value="<?= e(old('rnc')) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Ciudad</label><input type="text" name="city" class="form-control" value="<?= e(old('city')) ?>"></div>
        <div class="col-12 mb-3"><label class="form-label">Dirección</label><textarea name="address" class="form-control" rows="2"><?= e(old('address')) ?></textarea></div>
        <div class="col-12 mb-3"><label class="form-label">Notas</label><textarea name="notes" class="form-control" rows="2"><?= e(old('notes')) ?></textarea></div>
        <div class="col-md-6 mb-3"><label class="form-label">Estado</label><select name="status" class="form-select"><option value="active">Activo</option><option value="inactive">Inactivo</option></select></div>
    </div>
    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar</button>
</form></div></div>
