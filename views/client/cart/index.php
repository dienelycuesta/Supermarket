<?php $layout = 'client/layouts/main'; ?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= url('') ?>">Inicio</a></li><li class="breadcrumb-item active">Carrito de Compras</li></ol></nav>
    <h4 class="mb-4">Carrito de Compras <?php if(!empty($items)): ?><small class="text-muted">(<?= count($items) ?> art&iacute;culos)</small><?php endif; ?></h4>

    <?php if(empty($items)): ?>
    <div class="text-center py-5"><i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i><h5 class="text-muted">Tu carrito est&aacute; vac&iacute;o</h5><a href="<?= url('catalog') ?>" class="btn btn-success mt-3">Seguir Comprando</a></div>
    <?php else: ?>
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="cartTable">
                            <thead><tr><th>Producto</th><th class="text-nowrap">Precio</th><th class="text-nowrap">Cantidad</th><th class="text-nowrap">Subtotal</th><th></th></tr></thead>
                            <tbody>
                            <?php foreach($items as $item): ?>
                            <tr data-id="<?= $item['id'] ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($item['image']): ?><img src="<?= e(UPLOAD_URL . $item['image']) ?>" class="rounded me-3 d-none d-sm-block" style="width:60px;height:60px;object-fit:cover"><?php endif; ?>
                                        <div><strong><a href="<?= url('product/' . $item['slug']) ?>" class="text-decoration-none text-dark"><?= e($item['name']) ?></a></strong><br><small class="text-muted"><?= e($item['sku']) ?></small></div>
                                    </div>
                                </td>
                                <td><?= formatMoney($item['sale_price']) ?></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary cart-qty-btn" data-action="decrease" data-id="<?= $item['id'] ?>">-</button>
                                        <input type="number" class="form-control text-center cart-qty" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" data-id="<?= $item['id'] ?>">
                                        <button class="btn btn-outline-secondary cart-qty-btn" data-action="increase" data-id="<?= $item['id'] ?>">+</button>
                                    </div>
                                </td>
                                <td class="fw-bold cart-line-total"><?= formatMoney($item['line_total']) ?></td>
                                <td><button class="btn btn-sm btn-outline-danger cart-remove" data-id="<?= $item['id'] ?>"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card"><div class="card-header"><strong>Resumen del Pedido</strong></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span>Subtotal</span><span id="cartSubtotal"><?= formatMoney($subtotal) ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><span>ITBIS (18%)</span><span id="cartTax"><?= formatMoney($tax) ?></span></li>
                <li class="list-group-item d-flex justify-content-between"><strong>Total</strong><strong id="cartTotal"><?= formatMoney($total) ?></strong></li>
            </ul>
            <div class="card-body">
                <a href="<?= url('checkout') ?>" class="btn btn-success w-100 mb-2"><i class="fas fa-lock me-1"></i>Proceder al Pago</a>
                <a href="<?= url('catalog') ?>" class="btn btn-outline-secondary w-100">Seguir Comprando</a>
            </div></div>
        </div>
    </div>
    <?php endif; ?>
</div>
<script src="<?= asset('js/client/cart.js') ?>"></script>
