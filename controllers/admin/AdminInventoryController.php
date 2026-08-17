<?php
class AdminInventoryController extends Controller {
    public function index() {
        $this->requireAdmin();
        $db = Database::getInstance();
        $stmt = $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.stock ASC");
        $products = $stmt->fetchAll();
        $totalValue = 0;
        foreach ($products as $p) $totalValue += $p['stock'] * $p['cost_price'];
        $this->render('admin/inventory/index', [
            'pageTitle' => 'Inventario',
            'products' => $products,
            'alerts' => StockAlert::getActive(),
            'totalProducts' => count($products),
            'lowStockCount' => count(array_filter($products, fn($p) => $p['stock'] <= $p['min_stock'] && $p['stock'] > 0)),
            'outOfStockCount' => count(array_filter($products, fn($p) => $p['stock'] == 0)),
            'totalValue' => $totalValue,
        ]);
    }

    public function movements() {
        $this->requireAdmin();
        $productId = (int)($_GET['product_id'] ?? 0);
        $type = sanitize($_GET['type'] ?? '');
        $startDate = sanitize($_GET['start_date'] ?? date('Y-m-01'));
        $endDate = sanitize($_GET['end_date'] ?? date('Y-m-d'));
        if ($productId) {
            $movements = InventoryMovement::getByProduct($productId);
        } else {
            $movements = ['data' => InventoryMovement::getByDateRange($startDate, $endDate, $type), 'paginator' => null];
        }
        $product = $productId ? Product::find($productId) : null;
        $this->render('admin/inventory/movements', [
            'pageTitle' => 'Movimientos de Inventario',
            'movements' => $movements['data'],
            'paginator' => $movements['paginator'] ?? null,
            'product' => $product,
            'startDate' => $startDate, 'endDate' => $endDate, 'type' => $type,
        ]);
    }

    public function adjust() {
        $this->requireAdmin();
        $this->render('admin/inventory/adjust', ['pageTitle' => 'Ajuste de Stock']);
    }

    public function storeAdjustment() {
        $this->requireAdmin();
        $this->validateCSRF();
        $productId = (int)($_POST['product_id'] ?? 0);
        $type = sanitize($_POST['type'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);
        $notes = sanitize($_POST['notes'] ?? '');

        if (!$productId || !$type || !$quantity) {
            Session::flash('error', 'Todos los campos son requeridos.');
            $this->redirect(url('admin/inventory/adjust'));
        }

        try {
            InventoryMovement::record($productId, $type, $quantity, 'manual', null, $notes, Auth::id());
            Session::flash('success', 'Stock ajustado exitosamente.');
        } catch (Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }
        $this->redirect(url('admin/inventory'));
    }

    public function alerts() {
        $this->requireAdmin();
        $this->render('admin/inventory/alerts', ['pageTitle' => 'Alertas de Stock', 'alerts' => StockAlert::getActive()]);
    }

    public function resolveAlert($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        StockAlert::resolve($id);
        Session::flash('success', 'Alerta resuelta.');
        $this->redirect(url('admin/inventory'));
    }

    public function purchaseOrders() {
        $this->requireAdmin();
        $status = sanitize($_GET['status'] ?? '');
        $page = $this->getPage();
        $result = PurchaseOrder::getAll($status, $page);
        $this->render('admin/inventory/purchase_orders', array_merge($result, ['pageTitle' => 'Órdenes de Compra', 'statusFilter' => $status]));
    }

    public function createPurchaseOrder() {
        $this->requireAdmin();
        $this->render('admin/inventory/purchase_order_create', [
            'pageTitle' => 'Nueva Orden de Compra',
            'suppliers' => Supplier::getActive(),
        ]);
    }

    public function storePurchaseOrder() {
        $this->requireAdmin();
        $this->validateCSRF();
        $poId = PurchaseOrder::create([
            'order_number' => PurchaseOrder::generateOrderNumber(),
            'supplier_id' => (int)$_POST['supplier_id'],
            'status' => 'draft',
            'notes' => sanitize($_POST['notes'] ?? ''),
            'expected_date' => $_POST['expected_date'] ?? null,
            'created_by' => Auth::id(),
        ]);
        $products = $_POST['products'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        $costs = $_POST['unit_costs'] ?? [];
        for ($i = 0; $i < count($products); $i++) {
            if (!$products[$i]) continue;
            $qty = (int)($quantities[$i] ?? 0);
            $cost = (float)($costs[$i] ?? 0);
            PurchaseOrderItem::create([
                'purchase_order_id' => $poId,
                'product_id' => (int)$products[$i],
                'quantity_ordered' => $qty,
                'unit_cost' => $cost,
                'total_cost' => $qty * $cost,
            ]);
        }
        PurchaseOrder::updateTotals($poId);
        Session::flash('success', 'Orden de compra creada.');
        $this->redirect(url('admin/inventory/purchase-orders'));
    }

    public function showPurchaseOrder($id) {
        $this->requireAdmin();
        $po = PurchaseOrder::getWithItems($id);
        if (!$po) $this->redirect(url('admin/inventory/purchase-orders'));
        $this->render('admin/inventory/purchase_order_show', ['pageTitle' => 'OC #' . $po['order_number'], 'po' => $po]);
    }

    public function receivePurchaseOrder($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        $po = PurchaseOrder::getWithItems($id);
        if (!$po) $this->redirect(url('admin/inventory/purchase-orders'));
        $receivedQtys = $_POST['quantity_received'] ?? [];
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            foreach ($po['items'] as $item) {
                $received = (int)($receivedQtys[$item['id']] ?? 0);
                if ($received > 0) {
                    $db->prepare("UPDATE purchase_order_items SET quantity_received = quantity_received + ? WHERE id = ?")->execute([$received, $item['id']]);
                    InventoryMovement::record($item['product_id'], 'entry', $received, 'purchase_order', $id, 'PO #' . $po['order_number'], Auth::id());
                }
            }
            PurchaseOrder::update($id, ['status' => 'received', 'received_date' => date('Y-m-d')]);
            $db->commit();
            Session::flash('success', 'Artículos recibidos e inventario actualizado.');
        } catch (Exception $e) {
            $db->rollBack();
            Session::flash('error', 'Error al recibir artículos: ' . $e->getMessage());
        }
        $this->redirect(url('admin/inventory/purchase-orders/' . $id));
    }
}
