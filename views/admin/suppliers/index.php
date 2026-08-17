<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex justify-content-between mb-4"><h4>Proveedores</h4><a href="<?= url('admin/suppliers/create') ?>" class="btn btn-success"><i class="fas fa-plus me-1"></i>Agregar Proveedor</a></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive">
<table class="table table-hover mb-0">
<thead><tr><th>Nombre</th><th>Contacto</th><th>Teléfono</th><th>RNC</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
<?php foreach ($data as $s): ?>
<tr>
    <td><a href="<?= url('admin/suppliers/' . $s['id']) ?>"><?= e($s['name']) ?></a></td>
    <td><?= e($s['contact_person'] ?? '-') ?></td>
    <td><?= e($s['phone'] ?? '-') ?></td>
    <td><?= e($s['rnc'] ?? '-') ?></td>
    <td><span class="badge bg-<?= $s['status']==='active'?'success':'secondary' ?>"><?= e($s['status']) ?></span></td>
    <td>
        <a href="<?= url('admin/suppliers/' . $s['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
        <form method="POST" action="<?= url('admin/suppliers/' . $s['id'] . '/delete') ?>" class="d-inline delete-form"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
<?php if (isset($paginator)): ?><div class="mt-3"><?= $paginator->render() ?></div><?php endif; ?>
