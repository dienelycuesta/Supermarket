<?php
/**
 * Inventory API - Stock operations
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

if (!Auth::check() || !Auth::isAdmin()) {
    jsonResponse(['success' => false, 'message' => 'No autorizado'], 403);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'low_stock':
            $products = Product::getLowStock(20);
            jsonResponse(['success' => true, 'products' => $products]);
            break;

        case 'alerts':
            $alerts = StockAlert::getActive();
            jsonResponse(['success' => true, 'alerts' => $alerts, 'count' => count($alerts)]);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Acción inválida']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::validateHeader()) {
        jsonResponse(['success' => false, 'message' => 'Token CSRF inválido'], 419);
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'adjust':
            $productId = (int)($_POST['product_id'] ?? 0);
            $type = $_POST['type'] ?? '';
            $quantity = (int)($_POST['quantity'] ?? 0);
            $reason = trim($_POST['reason'] ?? '');

            if (!$productId || !in_array($type, ['entry', 'exit', 'adjustment']) || $quantity <= 0) {
                jsonResponse(['success' => false, 'message' => 'Parámetros inválidos']);
            }

            InventoryMovement::record($productId, $type, $quantity, $reason, Auth::id());
            $product = Product::find($productId);

            jsonResponse([
                'success' => true,
                'message' => 'Stock ajustado',
                'new_stock' => $product['stock'] ?? 0
            ]);
            break;

        case 'resolve_alert':
            $alertId = (int)($_POST['alert_id'] ?? 0);
            if ($alertId) {
                StockAlert::resolve($alertId);
                jsonResponse(['success' => true, 'message' => 'Alerta resuelta']);
            }
            jsonResponse(['success' => false, 'message' => 'Alerta inválida']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Acción inválida']);
    }
}
