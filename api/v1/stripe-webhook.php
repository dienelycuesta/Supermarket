<?php
/**
 * Stripe Webhook Handler - Verify and process payment events
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

// Stripe sends raw JSON
$payload = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (!defined('STRIPE_WEBHOOK_SECRET') || STRIPE_WEBHOOK_SECRET === 'REPLACE_ME') {
    http_response_code(500);
    exit('Webhook secret not configured');
}

try {
    require_once __DIR__ . '/../../vendor/autoload.php';

    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        STRIPE_WEBHOOK_SECRET
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit('Invalid payload');
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit('Invalid signature');
}

// Handle the event
$db = Database::getInstance();

switch ($event->type) {
    case 'payment_intent.succeeded':
        $intent = $event->data->object;
        $intentId = $intent->id;
        $userId = $intent->metadata->user_id ?? null;

        // Find payment record by stripe intent
        $payment = Payment::findByStripeIntent($intentId);

        if ($payment) {
            // Update payment status
            Payment::update($payment['id'], ['status' => 'completed']);

            // Update order
            $order = Order::find($payment['order_id']);
            if ($order) {
                Order::updateStatus($order['id'], 'confirmed');
                Order::update($order['id'], ['payment_status' => 'paid']);

                // Decrement stock
                $items = OrderItem::getByOrder($order['id']);
                foreach ($items as $item) {
                    if ($item['product_id']) {
                        Product::decrementStock($item['product_id'], $item['quantity']);
                        InventoryMovement::record(
                            $item['product_id'],
                            'sale',
                            $item['quantity'],
                            'Order: ' . $order['order_number'],
                            $userId
                        );
                    }
                }

                AuditLog::log('stripe_payment', 'payments', $payment['id'], null, [
                    'intent_id' => $intentId,
                    'amount' => $intent->amount / 100,
                    'order' => $order['order_number']
                ]);
            }
        }
        break;

    case 'payment_intent.payment_failed':
        $intent = $event->data->object;
        $payment = Payment::findByStripeIntent($intent->id);
        if ($payment) {
            Payment::update($payment['id'], ['status' => 'failed']);
        }
        break;
}

http_response_code(200);
echo json_encode(['received' => true]);
