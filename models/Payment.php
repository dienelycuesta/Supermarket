<?php
class Payment extends Model {
    protected static $table = 'payments';
    protected static $fillable = ['order_id','payment_method','status','amount','reference','stripe_payment_intent_id','notes','processed_by','processed_at'];

    public static function getByOrder($orderId) {
        return self::where("order_id = ?", [$orderId], 'created_at DESC');
    }

    public static function recordManualPayment($orderId, $method, $amount, $reference = '', $notes = '', $processedBy = null) {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $id = self::create([
                'order_id' => $orderId,
                'payment_method' => $method,
                'status' => 'completed',
                'amount' => $amount,
                'reference' => $reference,
                'notes' => $notes,
                'processed_by' => $processedBy,
                'processed_at' => date('Y-m-d H:i:s'),
            ]);
            $order = Order::getWithItems($orderId);
            if ($order) {
                Order::updateStatus($orderId, 'completed');
                foreach ($order['items'] as $item) {
                    Product::decrementStock($item['product_id'], $item['quantity']);
                    InventoryMovement::record($item['product_id'], 'sale', $item['quantity'], 'order', $orderId, 'Sale from order #' . $order['order_number'], $processedBy);
                }
            }
            $db->commit();
            return $id;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getByDateRange($startDate, $endDate, $method = '') {
        $cond = "created_at BETWEEN ? AND ?";
        $params = [$startDate, $endDate . ' 23:59:59'];
        if ($method) { $cond .= " AND payment_method = ?"; $params[] = $method; }
        return self::where($cond, $params, 'created_at DESC');
    }

    public static function getTotalByMethod($startDate, $endDate) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM payments WHERE status = 'completed' AND created_at BETWEEN ? AND ? GROUP BY payment_method");
        $stmt->execute([$startDate, $endDate . ' 23:59:59']);
        return $stmt->fetchAll();
    }

    public static function findByStripeIntent($intentId) {
        return self::findBy('stripe_payment_intent_id', $intentId);
    }
}
