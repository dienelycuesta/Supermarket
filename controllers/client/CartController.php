<?php
class CartController extends Controller {
    public function index() {
        $userId = Auth::check() ? Auth::user()['id'] : null;
        $sessionId = session_id();
        $items = Cart::getItems($userId, $sessionId);
        $subtotal = array_sum(array_column($items, 'line_total'));
        $tax = $subtotal * 0.18;
        $this->render('client/cart/index', [
            'pageTitle' => 'Carrito de Compras',
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ]);
    }
}
