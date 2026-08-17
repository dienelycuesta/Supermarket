<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($pageTitle ?? '') ?> - <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/client.css') ?>" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top main-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= url('') ?>">
            <i class="fas fa-leaf me-2"></i><?= APP_NAME ?>
        </a>
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <a class="nav-icon position-relative" href="<?= url('cart') ?>">
                <i class="fas fa-shopping-basket"></i>
                <span id="cart-badge-mobile" class="icon-badge"><?= getCartCount() ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navbarMain">
            <form class="search-form d-flex mx-lg-4 my-2 my-lg-0 flex-grow-1" action="<?= url('catalog') ?>" method="GET" style="max-width:480px">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input class="form-control border-start-0 ps-0" type="search" name="search" placeholder="Buscar productos..." value="<?= e($_GET['search'] ?? '') ?>">
                </div>
            </form>
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="<?= url('catalog') ?>">Cat&aacute;logo</a></li>
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link position-relative" href="<?= url('cart') ?>" id="openMiniCart">
                        <i class="fas fa-shopping-basket me-1"></i> Carrito
                        <span id="cart-badge" class="badge bg-danger rounded-pill" style="font-size:.6rem;position:relative;top:-1px"><?= getCartCount() ?></span>
                    </a>
                </li>
                <?php if (Auth::check()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <span class="user-chip"><i class="fas fa-user-circle me-1"></i> <?= e(Auth::user()['first_name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item" href="<?= url('my-orders') ?>"><i class="fas fa-box me-2 text-muted"></i>Mis Pedidos</a></li>
                        <li><a class="dropdown-item" href="<?= url('account') ?>"><i class="fas fa-cog me-2 text-muted"></i>Mi Cuenta</a></li>
                        <?php if (Auth::isAdmin()): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= url('admin/dashboard') ?>"><i class="fas fa-tachometer-alt me-2 text-muted"></i>Panel Admin</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesi&oacute;n</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="<?= url('login') ?>">Entrar</a></li>
                <li class="nav-item d-none d-lg-block"><a class="btn btn-light btn-sm rounded-pill px-3 ms-1 fw-semibold" href="<?= url('register') ?>">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if ($msg = Session::getFlash('success')): ?>
<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="fas fa-check-circle me-2"></i><?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
</div>
<?php endif; ?>
<?php if ($msg = Session::getFlash('error')): ?>
<div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="fas fa-exclamation-circle me-2"></i><?= e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
</div>
<?php endif; ?>

<!-- Cart Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title"><i class="fas fa-shopping-basket me-2 text-success"></i>Mi Carrito</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
        <div id="miniCartItems" class="flex-grow-1 overflow-auto"></div>
        <div id="miniCartLoading" class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Cargando...</p></div>
        <div id="miniCartEmpty" class="text-center py-5 text-muted" style="display:none"><i class="fas fa-shopping-basket fa-2x mb-2"></i><p>Carrito vac&iacute;o</p></div>
        <div id="miniCartFooter" class="border-top p-3" style="display:none">
            <div class="d-flex justify-content-between mb-1 small"><span>Subtotal</span><span id="miniSubtotal"></span></div>
            <div class="d-flex justify-content-between mb-1 small"><span>ITBIS (18%)</span><span id="miniTax"></span></div>
            <div class="d-flex justify-content-between mb-2"><strong>Total</strong><strong class="text-success" id="miniTotal"></strong></div>
            <a href="<?= url('checkout') ?>" class="btn btn-success w-100 mb-2"><i class="fas fa-lock me-1"></i>Pagar</a>
            <a href="<?= url('cart') ?>" class="btn btn-outline-secondary w-100 btn-sm">Ver Carrito Completo</a>
        </div>
    </div>
</div>

<!-- Content -->
<main><?= $content ?></main>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand"><i class="fas fa-leaf me-2"></i><?= APP_NAME ?></div>
                <p class="text-muted small mb-0">Tu supermercado de confianza. Productos frescos, excelentes precios y entrega a domicilio.</p>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="footer-heading">Tienda</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('catalog') ?>">Cat&aacute;logo</a></li>
                    <li><a href="<?= url('catalog?sort=price_asc') ?>">Ofertas</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="footer-heading">Cuenta</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('my-orders') ?>">Mis Pedidos</a></li>
                    <li><a href="<?= url('account') ?>">Mi Cuenta</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-heading">Contacto</h6>
                <ul class="list-unstyled footer-links">
                    <li><i class="fas fa-phone me-2 text-muted"></i>809-555-0001</li>
                    <li><i class="fas fa-envelope me-2 text-muted"></i>info@supermarket.com</li>
                    <li><i class="fas fa-map-marker-alt me-2 text-muted"></i>Santo Domingo, RD</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> <?= APP_NAME ?></span>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>var BASE_URL = '<?= APP_URL ?>';</script>
<script src="<?= asset('js/common/app.js') ?>"></script>
<script src="<?= asset('js/client/catalog.js') ?>"></script>
</body>
</html>
