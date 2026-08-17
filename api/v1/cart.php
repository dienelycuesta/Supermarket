<?php
/**
 * Cart API - AJAX endpoints for cart operations
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
}

// Validate CSRF for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::validateHeader()) {
        jsonResponse(['success' => false, 'message' => 'Token CSRF inválido'], 419);
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if (!$productId) {
            jsonResponse(['success' => false, 'message' => 'Producto inválido']);
        }

        $product = Product::find($productId);
        if (!$product || !$product['is_active']) {
            jsonResponse(['success' => false, 'message' => 'Producto no encontrado']);
        }

        if ($product['stock'] < $quantity) {
            jsonResponse(['success' => false, 'message' => 'Stock insuficiente. Disponible: ' . $product['stock']]);
        }

        $userId = Auth::id();
        $sessionId = $userId ? null : session_id();
        Cart::addItem($userId, $sessionId, $productId, $quantity);
        $count = Cart::getCount($userId, $sessionId);

        jsonResponse([
            'success' => true,
            'message' => 'Agregado al carrito',
            'cart_count' => $count
        ]);
        break;

    case 'update':
        $cartItemId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        // Look up the cart item to get the actual product_id for stock validation
        $cartItem = Cart::find($cartItemId);
        if ($cartItem) {
            $product = Product::find($cartItem['product_id']);
            if ($product && $quantity > $product['stock']) {
                $quantity = $product['stock'];
            }
        }

        $userId = Auth::id();
        $sessionId = $userId ? null : session_id();
        Cart::updateQuantity($cartItemId, $quantity, $userId, $sessionId);

        // Recalculate totals
        $items = Cart::getItems($userId, $sessionId);
        $subtotal = 0;
        $lineTotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['line_total'];
            if ($item['id'] == $cartItemId) {
                $lineTotal = $item['line_total'];
            }
        }
        $tax = $subtotal * 0.18;
        $total = $subtotal + $tax;

        jsonResponse([
            'success' => true,
            'line_total' => $lineTotal,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'cart_count' => Cart::getCount($userId, $sessionId)
        ]);
        break;

    case 'remove':
        $cartItemId = (int)($_POST['product_id'] ?? 0);
        $userId = Auth::id();
        $sessionId = $userId ? null : session_id();
        Cart::removeItem($cartItemId, $userId, $sessionId);

        $items = Cart::getItems($userId, $sessionId);
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['line_total'];
        }
        $tax = $subtotal * 0.18;
        $total = $subtotal + $tax;

        jsonResponse([
            'success' => true,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'cart_count' => count($items)
        ]);
        break;

    case 'count':
        $userId = Auth::id();
        $sessionId = $userId ? null : session_id();
        jsonResponse(['success' => true, 'count' => Cart::getCount($userId, $sessionId)]);
        break;

    case 'get':
        $userId = Auth::id();
        $sessionId = $userId ? null : session_id();
        $items = Cart::getItems($userId, $sessionId);
        $subtotal = 0;
        foreach ($items as $item) $subtotal += $item['line_total'];
        $tax = $subtotal * 0.18;
        jsonResponse([
            'success' => true,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'count' => count($items)
        ]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Acción inválida'], 400);
}
