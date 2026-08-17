<?php
/**
 * Checkout API - Create Stripe PaymentIntent
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
}

if (!CSRF::validateHeader()) {
    jsonResponse(['success' => false, 'message' => 'Token CSRF inválido'], 419);
}

if (!Auth::check()) {
    jsonResponse(['success' => false, 'message' => 'Inicie sesión'], 401);
}

$action = $_POST['action'] ?? '';

if ($action === 'create_intent') {
    // Get cart items
    $userId = Auth::id();
    $items = Cart::getItems($userId, null);

    if (empty($items)) {
        jsonResponse(['success' => false, 'message' => 'El carrito está vacío']);
    }

    // Calculate total
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['line_total'];
    }
    $tax = $subtotal * 0.18;
    $total = $subtotal + $tax;

    // Amount in cents for Stripe (DOP)
    $amountCents = (int)round($total * 100);

    if ($amountCents < 50) {
        jsonResponse(['success' => false, 'message' => 'Monto del pedido muy bajo']);
    }

    // Check if Stripe is configured
    if (!defined('STRIPE_SECRET_KEY') || STRIPE_SECRET_KEY === 'REPLACE_ME') {
        jsonResponse(['success' => false, 'message' => 'Stripe no está configurado']);
    }

    try {
        require_once __DIR__ . '/../../vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $user = Auth::user();
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $amountCents,
            'currency' => 'dop',
            'metadata' => [
                'user_id' => $userId,
                'user_email' => $user['email']
            ],
            'description' => 'SuperMarket Order - ' . $user['email']
        ]);

        jsonResponse([
            'success' => true,
            'client_secret' => $intent->client_secret,
            'intent_id' => $intent->id
        ]);
    } catch (\Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Error de pago: ' . $e->getMessage()]);
    }
}

jsonResponse(['success' => false, 'message' => 'Acción inválida'], 400);
