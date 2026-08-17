<?php
class CheckoutController extends Controller {
    public function index() {
        $this->requireAuth();
        $userId = Auth::user()['id'];
        $sessionId = session_id();
        $items = Cart::getItems($userId, $sessionId);
        if (empty($items)) {
            Session::flash('error', 'Tu carrito está vacío.');
            $this->redirect(url('cart'));
        }
        $issues = Cart::validateStock($userId, $sessionId);
        if (!empty($issues)) {
            Session::flash('error', 'Algunos artículos tienen stock insuficiente. Por favor actualice su carrito.');
            $this->redirect(url('cart'));
        }
        $subtotal = array_sum(array_column($items, 'line_total'));
        $tax = $subtotal * 0.18;
        $addresses = CustomerAddress::getByUser($userId);
        $defaultAddress = CustomerAddress::getDefault($userId);
        $this->render('client/checkout/index', [
            'pageTitle' => 'Pagar',
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'user' => Auth::user(),
        ]);
    }

    public function process() {
        $this->requireAuth();
        $this->validateCSRF();
        $userId = Auth::user()['id'];
        $sessionId = session_id();
        $items = Cart::getItems($userId, $sessionId);
        if (empty($items)) {
            Session::flash('error', 'Tu carrito está vacío.');
            $this->redirect(url('cart'));
        }
        try {
            $paymentMethod = sanitize($_POST['payment_method'] ?? 'cash');
            if (!in_array($paymentMethod, ['cash', 'bank_transfer'])) {
                $paymentMethod = 'cash';
            }
            $orderData = [
                'customer_name' => sanitize($_POST['customer_name'] ?? ''),
                'customer_email' => sanitize($_POST['customer_email'] ?? ''),
                'customer_phone' => sanitize($_POST['customer_phone'] ?? ''),
                'shipping_address' => sanitize($_POST['address'] ?? ''),
                'notes' => sanitize($_POST['notes'] ?? ''),
                'order_type' => 'online',
                'status' => 'pending',
                'payment_method' => $paymentMethod,
            ];
            $order = Order::createFromCart($userId, $sessionId, $orderData);
            Payment::create([
                'order_id' => $order['id'],
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'amount' => $order['total_amount'],
            ]);
            Notification::createOrderNotification($order);
            Session::flash('success', 'Pedido realizado exitosamente!');
            $this->redirect(url('checkout/confirmation/' . $order['order_number']));
        } catch (Exception $e) {
            Session::flash('error', 'Error al realizar el pedido: ' . $e->getMessage());
            $this->redirect(url('checkout'));
        }
    }

    public function confirmation($orderNumber) {
        $order = Order::findBy('order_number', $orderNumber);
        if (!$order) $this->redirect(url(''));
        $orderItems = OrderItem::getByOrder($order['id']);
        $this->render('client/checkout/confirmation', ['pageTitle' => 'Confirmación de Pedido', 'order' => $order, 'orderItems' => $orderItems]);
    }
}
