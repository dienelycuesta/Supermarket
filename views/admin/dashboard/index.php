<?php $layout = 'admin/layouts/main'; ?>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Panel Principal</h4>
        <small class="text-muted"><?php
            $dias = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
            $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
            $dia = $dias[date('l')];
            $mes = $meses[date('F')];
            echo $dia . ', ' . date('j') . ' de ' . $mes . ' de ' . date('Y');
        ?></small>
    </div>
    <a href="<?= url('admin/orders/in-store') ?>" class="btn btn-success"><i class="fas fa-plus me-1"></i>Nueva Venta</a>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4 g-3">
    <div class="col-6 col-xl-3 col-md-6">
        <div class="card stat-card border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Ventas de Hoy</div>
                        <div class="h4 mb-0 mt-1 text-success fw-bold"><?= formatMoney($todaySales) ?></div>
                    </div>
                    <div class="rounded-3 bg-success bg-opacity-10 d-none d-sm-flex align-items-center justify-content-center" style="width:46px;height:46px">
                        <i class="fas fa-dollar-sign text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 col-md-6">
        <div class="card stat-card border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Ingresos del Mes</div>
                        <div class="h4 mb-0 mt-1 text-primary fw-bold"><?= formatMoney($monthlyRevenue) ?></div>
                    </div>
                    <div class="rounded-3 bg-primary bg-opacity-10 d-none d-sm-flex align-items-center justify-content-center" style="width:46px;height:46px">
                        <i class="fas fa-chart-line text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 col-md-6">
        <div class="card stat-card border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Productos</div>
                        <div class="h4 mb-0 mt-1 text-info fw-bold"><?= $totalProducts ?></div>
                    </div>
                    <div class="rounded-3 bg-info bg-opacity-10 d-none d-sm-flex align-items-center justify-content-center" style="width:46px;height:46px">
                        <i class="fas fa-box text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 col-md-6">
        <div class="card stat-card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Pedidos Pendientes</div>
                        <div class="h4 mb-0 mt-1 text-warning fw-bold"><?= $pendingOrders ?></div>
                    </div>
                    <div class="rounded-3 bg-warning bg-opacity-10 d-none d-sm-flex align-items-center justify-content-center" style="width:46px;height:46px">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-3">
                <div class="row text-center g-3">
                    <div class="col-4 col-sm col-md">
                        <a href="<?= url('admin/products/create') ?>" class="quick-action">
                            <div class="rounded-3 bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:42px;height:42px">
                                <i class="fas fa-plus text-success"></i>
                            </div>
                            <div class="small fw-bold">Agregar Producto</div>
                        </a>
                    </div>
                    <div class="col-4 col-sm col-md">
                        <a href="<?= url('admin/orders/in-store') ?>" class="quick-action">
                            <div class="rounded-3 bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:42px;height:42px">
                                <i class="fas fa-cash-register text-primary"></i>
                            </div>
                            <div class="small fw-bold">Venta POS</div>
                        </a>
                    </div>
                    <div class="col-4 col-sm col-md">
                        <a href="<?= url('admin/inventory/adjust') ?>" class="quick-action">
                            <div class="rounded-3 bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:42px;height:42px">
                                <i class="fas fa-boxes text-info"></i>
                            </div>
                            <div class="small fw-bold">Ajustar Stock</div>
                        </a>
                    </div>
                    <div class="col-4 col-sm col-md">
                        <a href="<?= url('admin/reports') ?>" class="quick-action">
                            <div class="rounded-3 bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:42px;height:42px">
                                <i class="fas fa-chart-bar text-warning"></i>
                            </div>
                            <div class="small fw-bold">Reportes</div>
                        </a>
                    </div>
                    <div class="col-4 col-sm col-md">
                        <a href="<?= url('') ?>" target="_blank" class="quick-action">
                            <div class="rounded-3 bg-secondary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:42px;height:42px">
                                <i class="fas fa-store text-secondary"></i>
                            </div>
                            <div class="small fw-bold">Ver Tienda</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico de Ventas -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6>Ventas - Últimos 30 Días</h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Alertas de Stock Bajo -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6>Alertas de Stock</h6>
                <?php if($lowStockCount > 0): ?>
                <span class="badge bg-danger rounded-pill"><?= $lowStockCount ?></span>
                <?php else: ?>
                <span class="badge bg-success rounded-pill">OK</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height:320px;overflow-y:auto">
                    <?php if(empty($lowStockProducts)): ?>
                    <div class="list-group-item text-center py-4">
                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                        <div class="text-muted small">Todo el stock está en orden</div>
                    </div>
                    <?php else: ?>
                    <?php foreach(array_slice($lowStockProducts, 0, 10) as $p): ?>
                    <a href="<?= url('admin/products/' . $p['id']) ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small"><?= e($p['name']) ?></div>
                                <small class="text-muted"><?= e($p['sku']) ?></small>
                            </div>
                            <?php if($p['stock'] == 0): ?>
                            <span class="badge bg-danger">AGOTADO</span>
                            <?php else: ?>
                            <span class="badge bg-warning text-dark"><?= $p['stock'] ?> restantes</span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if($lowStockCount > 10): ?>
                <div class="card-footer text-center py-2 bg-transparent">
                    <a href="<?= url('admin/inventory/alerts') ?>" class="small text-decoration-none">Ver las <?= $lowStockCount ?> alertas</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Recientes -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6>Pedidos Recientes</h6>
        <a href="<?= url('admin/orders') ?>" class="btn btn-sm btn-outline-success">Ver Todos</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Pedido #</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
                <tbody>
                <?php if(empty($recentOrders)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Sin pedidos aún</td></tr>
                <?php else: ?>
                <?php foreach($recentOrders as $o): ?>
                <?php
                    $statusLabels = ['completed'=>'Completado','pending'=>'Pendiente','confirmed'=>'Confirmado','processing'=>'En Proceso','cancelled'=>'Cancelado','delivered'=>'Entregado','ready'=>'Listo','refunded'=>'Reembolsado'];
                    $colors = ['completed'=>'success','pending'=>'warning','confirmed'=>'info','processing'=>'primary','cancelled'=>'danger','delivered'=>'success','ready'=>'info','refunded'=>'secondary'];
                    $c = isset($colors[$o['status']]) ? $colors[$o['status']] : 'secondary';
                    $label = isset($statusLabels[$o['status']]) ? $statusLabels[$o['status']] : ucfirst($o['status']);
                ?>
                <tr>
                    <td><a href="<?= url('admin/orders/' . $o['id']) ?>" class="fw-bold text-decoration-none"><?= e($o['order_number']) ?></a></td>
                    <td><?= e($o['customer_name'] ?: 'Sin nombre') ?></td>
                    <td class="fw-bold"><?= formatMoney($o['total_amount']) ?></td>
                    <td><span class="badge bg-<?= $c ?>"><?= $label ?></span></td>
                    <td class="text-muted small"><?= timeAgo($o['created_at']) ?></td>
                    <td><a href="<?= url('admin/orders/' . $o['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></a></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('salesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php
                $labels = [];
                for ($i = 29; $i >= 0; $i--) {
                    $labels[] = date('d/m', strtotime("-{$i} days"));
                }
                echo json_encode($labels);
            ?>,
            datasets: [{
                label: 'Ventas (RD$)',
                data: <?php
                    $salesData = Order::getSalesByDay(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'));
                    $dayMap = [];
                    foreach ($salesData as $row) {
                        $dayMap[$row['date']] = (float)$row['total'];
                    }
                    $values = [];
                    for ($i = 29; $i >= 0; $i--) {
                        $d = date('Y-m-d', strtotime("-{$i} days"));
                        $values[] = isset($dayMap[$d]) ? $dayMap[$d] : 0;
                    }
                    echo json_encode($values);
                ?>,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.06)',
                fill: true,
                tension: 0.4,
                pointRadius: 2,
                pointHoverRadius: 5,
                pointBackgroundColor: '#22c55e',
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { size: 12 },
                    bodyFont: { size: 13 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) { return 'RD$ ' + ctx.parsed.y.toLocaleString(); }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) { return 'RD$' + v.toLocaleString(); },
                        font: { size: 11 },
                        color: '#9ca3af'
                    },
                    grid: { color: 'rgba(0,0,0,.03)' }
                },
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 45, font: { size: 10 }, color: '#9ca3af' }
                }
            }
        }
    });
});
</script>
