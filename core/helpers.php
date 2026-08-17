<?php
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function badgeColor($value, $map, $default = 'secondary') {
    return isset($map[$value]) ? $map[$value] : $default;
}

function sanitize($string) {
    return trim(strip_tags($string ?? ''));
}

function url($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

function asset($path) {
    return APP_URL . '/public/' . ltrim($path, '/');
}

function formatMoney($amount) {
    return 'RD$ ' . number_format((float)$amount, 2);
}

function old($key, $default = '') {
    return $_SESSION['old_input'][$key] ?? $default;
}

function csrf_field() {
    return CSRF::field();
}

function csrf_token() {
    return CSRF::generate();
}

function isActiveRoute($route) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $check = '/supermarket/' . ltrim($route, '/');
    return strpos($uri, $check) !== false;
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[áàäâã]/u', 'a', $text);
    $text = preg_replace('/[éèëê]/u', 'e', $text);
    $text = preg_replace('/[íìïî]/u', 'i', $text);
    $text = preg_replace('/[óòöôõ]/u', 'o', $text);
    $text = preg_replace('/[úùüû]/u', 'u', $text);
    $text = preg_replace('/[ñ]/u', 'n', $text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function generateSKU($prefix = 'PRD') {
    return $prefix . '-' . strtoupper(substr(uniqid(), -6));
}

function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'justo ahora';
    if ($diff < 3600) return 'hace ' . floor($diff / 60) . 'min';
    if ($diff < 86400) return 'hace ' . floor($diff / 3600) . 'h';
    if ($diff < 604800) return 'hace ' . floor($diff / 86400) . 'd';
    return date('d/m/Y', strtotime($datetime));
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function currentUrl() {
    return $_SERVER['REQUEST_URI'] ?? '/';
}

function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function setOldInput() {
    $_SESSION['old_input'] = $_POST;
}

function clearOldInput() {
    unset($_SESSION['old_input']);
}

function buildWhatsAppOrderUrl($order, $items, $phone = null) {
    $phone = $phone ?: WHATSAPP_PHONE;
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $paymentLabels = ['cash'=>'Efectivo','bank_transfer'=>'Transferencia Bancaria','stripe'=>'Tarjeta','card_pos'=>'Tarjeta POS'];
    $pm = isset($paymentLabels[$order['payment_method']]) ? $paymentLabels[$order['payment_method']] : ucfirst(str_replace('_',' ',$order['payment_method'] ?? ''));

    $msg = "*Pedido #{$order['order_number']}*\n";
    $msg .= "Fecha: " . date('d/m/Y h:i A', strtotime($order['created_at'])) . "\n";
    $msg .= "Cliente: " . ($order['customer_name'] ?? 'N/A') . "\n";
    if (!empty($order['customer_phone'])) {
        $msg .= "Tel: " . $order['customer_phone'] . "\n";
    }
    $msg .= "Pago: {$pm}\n";
    if (!empty($order['shipping_address'])) {
        $msg .= "Direccion: " . $order['shipping_address'] . "\n";
    }
    $msg .= "\n*Articulos:*\n";
    foreach ($items as $item) {
        $name = $item['product_name'] ?? $item['name'] ?? '';
        $qty = $item['quantity'];
        $price = formatMoney($item['unit_price'] ?? $item['sale_price'] ?? 0);
        $sub = formatMoney($item['subtotal'] ?? $item['line_total'] ?? 0);
        $msg .= "- {$name} x{$qty} ({$price}) = {$sub}\n";
    }
    $msg .= "\n*Total: " . formatMoney($order['total_amount']) . "*";
    if (!empty($order['notes'])) {
        $msg .= "\nNotas: " . $order['notes'];
    }

    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($msg);
}

function getCartCount() {
    if (!class_exists('Cart')) return 0;
    try {
        $userId = Auth::check() ? Auth::user()['id'] : null;
        $sessionId = session_id();
        return Cart::getCount($userId, $sessionId);
    } catch (Exception $e) {
        return 0;
    }
}
