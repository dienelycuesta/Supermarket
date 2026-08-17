<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($pageTitle ?? 'Admin') ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<!-- Sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <a href="<?= url('admin/dashboard') ?>" class="sidebar-brand">
            <i class="fas fa-store"></i>
            <span><?= APP_NAME ?></span>
        </a>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-section">MENÚ PRINCIPAL</li>
        <li class="<?= isActiveRoute('admin/dashboard') ? 'active' : '' ?>">
            <a href="<?= url('admin/dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Panel Principal</a>
        </li>
        <li class="sidebar-section">CATÁLOGO</li>
        <li class="<?= isActiveRoute('admin/products') ? 'active' : '' ?>">
            <a href="<?= url('admin/products') ?>"><i class="fas fa-box"></i> Productos</a>
        </li>
        <li class="<?= isActiveRoute('admin/categories') ? 'active' : '' ?>">
            <a href="<?= url('admin/categories') ?>"><i class="fas fa-tags"></i> Categorías</a>
        </li>
        <li class="<?= isActiveRoute('admin/suppliers') ? 'active' : '' ?>">
            <a href="<?= url('admin/suppliers') ?>"><i class="fas fa-truck"></i> Proveedores</a>
        </li>
        <li class="sidebar-section">OPERACIONES</li>
        <li class="<?= isActiveRoute('admin/inventory') ? 'active' : '' ?>">
            <a href="<?= url('admin/inventory') ?>"><i class="fas fa-warehouse"></i> Inventario</a>
        </li>
        <li class="<?= isActiveRoute('admin/orders') ? 'active' : '' ?>">
            <a href="<?= url('admin/orders') ?>"><i class="fas fa-shopping-bag"></i> Pedidos</a>
        </li>
        <li class="<?= isActiveRoute('admin/payments') ? 'active' : '' ?>">
            <a href="<?= url('admin/payments') ?>"><i class="fas fa-credit-card"></i> Pagos</a>
        </li>
        <li class="sidebar-section">ANÁLISIS</li>
        <li class="<?= isActiveRoute('admin/reports') ? 'active' : '' ?>">
            <a href="<?= url('admin/reports') ?>"><i class="fas fa-chart-bar"></i> Reportes</a>
        </li>
        <li class="sidebar-divider"></li>
        <li>
            <a href="<?= url('') ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Tienda</a>
        </li>
    </ul>
</nav>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <button class="btn btn-link sidebar-toggle d-lg-none p-0" onclick="toggleSidebar()">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <div class="ms-auto d-flex align-items-center gap-3">
            <!-- Order Notifications -->
            <div class="dropdown" id="notifDropdown">
                <button class="btn btn-link top-navbar-icon position-relative p-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" title="Notificaciones" id="notifBtn">
                    <i class="fas fa-bell"></i>
                    <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.65rem;display:none">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" style="width:360px;max-height:440px;overflow:hidden">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
                        <strong class="small">Notificaciones</strong>
                        <button class="btn btn-link btn-sm text-decoration-none p-0 small" id="markAllReadBtn" style="display:none">Marcar todas leidas</button>
                    </div>
                    <div id="notifList" style="max-height:350px;overflow-y:auto">
                        <div class="text-center py-4 text-muted small" id="notifEmpty"><i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>Sin notificaciones nuevas</div>
                    </div>
                </div>
            </div>
            <!-- Stock Alerts -->
            <?php $alertCount = StockAlert::getCount(); ?>
            <a href="<?= url('admin/inventory/alerts') ?>" class="top-navbar-icon position-relative" title="Alertas de stock">
                <i class="fas fa-boxes"></i>
                <?php if($alertCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size:.65rem"><?= $alertCount ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle text-decoration-none p-0" data-bs-toggle="dropdown">
                    <span class="user-avatar"><?= strtoupper(substr(Auth::user()['first_name'] ?? 'A', 0, 1)) ?></span>
                    <span class="ms-1 d-none d-md-inline text-dark fw-medium" style="font-size:.88rem"><?= e(Auth::user()['first_name'] ?? 'Admin') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><span class="dropdown-item-text text-muted small"><?= e(Auth::user()['email'] ?? '') ?></span></li>
                    <li><span class="dropdown-item-text"><span class="badge bg-success"><?= ucfirst(Auth::user()['role'] ?? 'admin') ?></span></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
        <?php if($msg = Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if($msg = Session::getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= e($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</div>

<div id="sidebarOverlay" class="sidebar-overlay" onclick="document.getElementById('sidebar').classList.remove('show');this.style.display='none'"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>var BASE_URL = '<?= APP_URL ?>';</script>
<script src="<?= asset('js/common/app.js') ?>"></script>
<script>
function toggleSidebar() {
    var sb = document.getElementById('sidebar');
    var ov = document.getElementById('sidebarOverlay');
    sb.classList.toggle('show');
    ov.style.display = sb.classList.contains('show') ? 'block' : 'none';
}

// Notification polling system
(function(){
    var lastCount = 0;
    var notifSound = null;

    function playNotifSound() {
        try {
            if (!notifSound) {
                var AudioCtx = window.AudioContext || window.webkitAudioContext;
                var ctx = new AudioCtx();
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 880;
                osc.type = 'sine';
                gain.gain.value = 0.15;
                osc.start();
                setTimeout(function(){ osc.frequency.value = 1100; }, 120);
                setTimeout(function(){ gain.gain.value = 0; osc.stop(); }, 300);
            }
        } catch(e) {}
    }

    function timeAgo(dateStr) {
        var diff = Math.floor((Date.now() - new Date(dateStr + ' GMT-0400').getTime()) / 1000);
        if (diff < 60) return 'justo ahora';
        if (diff < 3600) return 'hace ' + Math.floor(diff/60) + 'min';
        if (diff < 86400) return 'hace ' + Math.floor(diff/3600) + 'h';
        return 'hace ' + Math.floor(diff/86400) + 'd';
    }

    function renderNotifications(data) {
        var badge = document.getElementById('notifBadge');
        var list = document.getElementById('notifList');
        var empty = document.getElementById('notifEmpty');
        var markAllBtn = document.getElementById('markAllReadBtn');
        var count = data.count || 0;
        var items = data.notifications || [];

        // Update badge
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline-block';
            markAllBtn.style.display = 'inline';
        } else {
            badge.style.display = 'none';
            markAllBtn.style.display = 'none';
        }

        // Play sound for new notifications
        if (count > lastCount && lastCount >= 0) {
            playNotifSound();
            if (count > lastCount) {
                document.title = '(' + count + ') ' + (document.title.replace(/^\(\d+\)\s*/, ''));
            }
        }
        if (count === 0) {
            document.title = document.title.replace(/^\(\d+\)\s*/, '');
        }
        lastCount = count;

        if (items.length === 0) {
            list.innerHTML = '<div class="text-center py-4 text-muted small"><i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>Sin notificaciones nuevas</div>';
            return;
        }

        var icons = {new_order: 'fa-shopping-bag text-success', order_status: 'fa-sync text-info', low_stock: 'fa-exclamation-triangle text-warning', info: 'fa-info-circle text-primary'};
        var html = '';
        items.forEach(function(n) {
            var icon = icons[n.type] || icons['info'];
            html += '<a href="' + BASE_URL + '/' + (n.link || '#') + '" class="d-flex px-3 py-2 text-decoration-none border-bottom notif-item" data-id="' + n.id + '">'
                + '<div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:40px;height:40px"><i class="fas ' + icon + '"></i></div>'
                + '<div class="flex-grow-1 overflow-hidden">'
                + '<div class="fw-semibold text-dark small text-truncate">' + n.title + '</div>'
                + '<div class="text-muted small text-truncate">' + (n.message || '') + '</div>'
                + '<div class="text-muted" style="font-size:.7rem">' + timeAgo(n.created_at) + '</div>'
                + '</div></a>';
        });
        list.innerHTML = html;

        // Mark as read on click
        list.querySelectorAll('.notif-item').forEach(function(el) {
            el.addEventListener('click', function() {
                var nid = this.dataset.id;
                $.post(BASE_URL + '/api/v1/notifications.php', {action: 'mark_read', id: nid});
            });
        });
    }

    function pollNotifications() {
        $.ajax({
            url: BASE_URL + '/api/v1/notifications.php?action=unread',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) renderNotifications(res);
            }
        });
    }

    // Mark all as read
    document.getElementById('markAllReadBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $.ajax({
            url: BASE_URL + '/api/v1/notifications.php',
            method: 'POST',
            data: {action: 'mark_all_read'},
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    lastCount = 0;
                    renderNotifications({count: 0, notifications: []});
                }
            }
        });
    });

    // Initial fetch + poll every 15 seconds
    pollNotifications();
    setInterval(pollNotifications, 15000);
})();
</script>
</body>
</html>
