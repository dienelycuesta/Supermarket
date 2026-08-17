<?php
class ClientOrderController extends Controller {
    public function index() {
        $this->requireAuth();
        $page = $this->getPage();
        $result = Order::getByUser(Auth::user()['id'], $page);
        $this->render('client/orders/index', array_merge($result, ['pageTitle' => 'Mis Pedidos']));
    }

    public function show($id) {
        $this->requireAuth();
        $order = Order::getWithItems($id);
        if (!$order || $order['user_id'] != Auth::user()['id']) {
            $this->redirect(url('my-orders'));
        }
        $orderItems = $order['items'] ?? [];
        $this->render('client/orders/show', ['pageTitle' => 'Pedido #' . $order['order_number'], 'order' => $order, 'orderItems' => $orderItems]);
    }
}
