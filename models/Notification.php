<?php
class Notification extends Model {
    protected static $table = 'notifications';
    protected static $fillable = ['type', 'title', 'message', 'data', 'link', 'is_read'];

    public static function getUnread($limit = 20) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getUnreadCount() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM notifications WHERE is_read = 0");
        return (int)$stmt->fetch()['cnt'];
    }

    public static function markRead($id) {
        return self::update($id, ['is_read' => 1]);
    }

    public static function markAllRead() {
        $db = Database::getInstance();
        $db->exec("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    }

    public static function createOrderNotification($order) {
        return self::create([
            'type' => 'new_order',
            'title' => 'Nuevo Pedido #' . $order['order_number'],
            'message' => ($order['customer_name'] ?: 'Cliente') . ' ha realizado un pedido por ' . formatMoney($order['total_amount']),
            'data' => json_encode(['order_id' => $order['id'], 'order_number' => $order['order_number'], 'total' => $order['total_amount']]),
            'link' => 'admin/orders/' . $order['id'],
        ]);
    }
}
