<?php
/**
 * Orders API - Status updates
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
}

if (!CSRF::validateHeader()) {
    jsonResponse(['success' => false, 'message' => 'Token CSRF inválido'], 419);
}

if (!Auth::check() || !Auth::isAdmin()) {
    jsonResponse(['success' => false, 'message' => 'No autorizado'], 403);
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_status':
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $validStatuses = ['pending', 'confirmed', 'processing', 'ready', 'delivered', 'completed', 'cancelled'];

        if (!$orderId || !in_array($status, $validStatuses)) {
            jsonResponse(['success' => false, 'message' => 'Parámetros inválidos']);
        }

        Order::updateStatus($orderId, $status);

        // If cancelled, restore stock
        if ($status === 'cancelled') {
            $order = Order::find($orderId);
            $items = OrderItem::getByOrder($orderId);
            foreach ($items as $item) {
                if ($item['product_id']) {
                    Product::incrementStock($item['product_id'], $item['quantity']);
                    InventoryMovement::record(
                        $item['product_id'],
                        'return',
                        $item['quantity'],
                        'order',
                        $orderId,
                        'Order cancelled: ' . ($order['order_number'] ?? ''),
                        Auth::id()
                    );
                }
            }
        }

        jsonResponse(['success' => true, 'message' => 'Estado actualizado']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Acción inválida'], 400);
}
