<?php
class AdminOrderController extends Controller {
    public function index() {
        $this->requireAdmin();
        $status = sanitize($_GET['status'] ?? '');
        $type = sanitize($_GET['type'] ?? '');
        $page = $this->getPage();
        $result = Order::getAll($status, $type, $page);
        $this->render('admin/orders/index', array_merge($result, [
            'pageTitle' => 'Pedidos', 'statusFilter' => $status, 'typeFilter' => $type,
        ]));
    }

    public function show($id) {
        $this->requireAdmin();
        $order = Order::getWithItems($id);
        if (!$order) $this->redirect(url('admin/orders'));
        $this->render('admin/orders/show', ['pageTitle' => 'Pedido #' . $order['order_number'], 'order' => $order]);
    }

    public function updateStatus($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        $status = sanitize($_POST['status'] ?? '');
        if (!in_array($status, ['processing','completed','cancelled'])) {
            Session::flash('error', 'Estado inválido.');
            $this->redirect(url('admin/orders/' . $id));
        }
        $order = Order::getWithItems($id);
        if (!$order) $this->redirect(url('admin/orders'));

        if ($status === 'completed') {
            foreach ($order['items'] as $item) {
                Product::decrementStock($item['product_id'], $item['quantity']);
                InventoryMovement::record($item['product_id'], 'sale', $item['quantity'], 'order', $id, 'Order #' . $order['order_number'], Auth::id());
            }
        }
        Order::updateStatus($id, $status);
        Session::flash('success', 'Estado del pedido actualizado a ' . $status . '.');
        $this->redirect(url('admin/orders/' . $id));
    }

    public function delete($id) {
        $this->requireAdmin();
        $this->validateCSRF();
        $order = Order::find($id);
        if (!$order) {
            Session::flash('error', 'Pedido no encontrado.');
            $this->redirect(url('admin/orders'));
        }
        Order::deleteWithRelations($id);
        Session::flash('success', 'Pedido #' . $order['order_number'] . ' eliminado.');
        $this->redirect(url('admin/orders'));
    }

    public function clearAll() {
        $this->requireAdmin();
        $this->validateCSRF();
        Order::clearAll();
        Session::flash('success', 'Todos los pedidos han sido eliminados.');
        $this->redirect(url('admin/orders'));
    }

    public function createInStore() {
        $this->requireAdmin();
        $this->render('admin/orders/in_store', ['pageTitle' => 'Nueva Venta en Tienda']);
    }

    public function storeInStore() {
        $this->requireAdmin();
        $this->validateCSRF();
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $products = $_POST['products'] ?? [];
            $quantities = $_POST['quantities'] ?? [];
            $subtotal = 0;
            $items = [];
            for ($i = 0; $i < count($products); $i++) {
                $pid = (int)($products[$i] ?? 0);
                $qty = (int)($quantities[$i] ?? 0);
                if (!$pid || !$qty) continue;
                $prod = Product::find($pid);
                if (!$prod) continue;
                $lineTotal = $prod['sale_price'] * $qty;
                $subtotal += $lineTotal;
                $items[] = ['product' => $prod, 'quantity' => $qty, 'line_total' => $lineTotal];
            }
            $taxAmount = $subtotal * 0.18;
            $total = $subtotal + $taxAmount;

            $orderId = Order::create([
                'order_number' => generateOrderNumber(),
                'customer_name' => sanitize($_POST['customer_name'] ?? 'Cliente en tienda'),
                'order_type' => 'in_store',
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'created_by' => Auth::id(),
                'completed_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $item['product']['id'],
                    'product_name' => $item['product']['name'],
                    'product_sku' => $item['product']['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product']['sale_price'],
                    'tax_rate' => 18,
                    'subtotal' => $item['line_total'],
                ]);
                Product::decrementStock($item['product']['id'], $item['quantity']);
                InventoryMovement::record($item['product']['id'], 'sale', $item['quantity'], 'order', $orderId, 'Venta en tienda', Auth::id());
            }

            $method = sanitize($_POST['payment_method'] ?? 'cash');
            Payment::create([
                'order_id' => $orderId,
                'payment_method' => $method,
                'status' => 'completed',
                'amount' => $total,
                'reference' => sanitize($_POST['reference'] ?? ''),
                'processed_by' => Auth::id(),
                'processed_at' => date('Y-m-d H:i:s'),
            ]);

            $db->commit();
            Session::flash('success', 'Venta en tienda creada exitosamente.');
            $this->redirect(url('admin/orders/' . $orderId));
        } catch (Exception $e) {
            $db->rollBack();
            Session::flash('error', 'Error: ' . $e->getMessage());
            $this->redirect(url('admin/orders/in-store'));
        }
    }
}
