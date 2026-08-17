<?php
class Order extends Model {
    protected static $table = 'orders';
    protected static $fillable = ['order_number','user_id','customer_name','customer_email','customer_phone','order_type','status','payment_status','payment_method','subtotal','tax_amount','discount_amount','total_amount','shipping_address','notes','created_by','completed_at','cancelled_at'];

    public static function createFromCart($userId, $sessionId, $orderData) {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $items = Cart::getItems($userId, $sessionId);
            if (empty($items)) throw new Exception('Cart is empty');
            $subtotal = array_sum(array_column($items, 'line_total'));
            $taxRate = 0.18;
            $taxAmount = $subtotal * $taxRate;
            $total = $subtotal + $taxAmount;

            $orderId = self::create(array_merge($orderData, [
                'order_number' => generateOrderNumber(),
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
            ]));

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['sale_price'],
                    'tax_rate' => $taxRate * 100,
                    'subtotal' => $item['line_total'],
                ]);
            }

            Cart::clear($userId, $sessionId);
            $db->commit();
            $order = self::find($orderId);
            return $order;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getAll($status = '', $type = '', $page = 1) {
        $cond = "1=1";
        $params = [];
        if ($status) { $cond .= " AND o.status = ?"; $params[] = $status; }
        if ($type) { $cond .= " AND o.order_type = ?"; $params[] = $type; }
        $db = Database::getInstance();
        $cs = $db->prepare("SELECT COUNT(*) as total FROM orders o WHERE {$cond}");
        $cs->execute($params);
        $total = $cs->fetch()['total'];
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $stmt = $db->prepare("SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count FROM orders o WHERE {$cond} ORDER BY o.created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute(array_merge($params, [$paginator->getLimit(), $paginator->getOffset()]));
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'paginator' => $paginator];
    }

    public static function getByUser($userId, $page = 1) {
        return self::paginate($page, ITEMS_PER_PAGE, "user_id = ?", [$userId], 'created_at DESC');
    }

    public static function getWithItems($id) {
        $order = self::find($id);
        if (!$order) return null;
        $order['items'] = OrderItem::getByOrder($id);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM payments WHERE order_id = ?");
        $stmt->execute([$id]);
        $order['payments'] = $stmt->fetchAll();
        return $order;
    }

    public static function updateStatus($id, $status) {
        $data = ['status' => $status];
        if ($status === 'completed') $data['completed_at'] = date('Y-m-d H:i:s');
        if ($status === 'cancelled') $data['cancelled_at'] = date('Y-m-d H:i:s');
        return self::update($id, $data);
    }

    public static function deleteWithRelations($id) {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $db->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM payments WHERE order_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function clearAll() {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $db->exec("DELETE FROM order_items");
            $db->exec("DELETE FROM payments");
            $db->exec("DELETE FROM orders");
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getRecentOrders($limit = 10) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getTodaySales() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COALESCE(SUM(total_amount),0) as total FROM orders WHERE status = 'completed' AND DATE(completed_at) = CURDATE()");
        $stmt->execute();
        return (float)$stmt->fetch()['total'];
    }

    public static function getMonthlyRevenue() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COALESCE(SUM(total_amount),0) as total FROM orders WHERE status = 'completed' AND YEAR(completed_at) = YEAR(CURDATE()) AND MONTH(completed_at) = MONTH(CURDATE())");
        $stmt->execute();
        return (float)$stmt->fetch()['total'];
    }

    public static function getSalesStats($startDate, $endDate) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount),0) as total_sales, COALESCE(AVG(total_amount),0) as avg_order FROM orders WHERE status = 'completed' AND completed_at BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate . ' 23:59:59']);
        return $stmt->fetch();
    }

    public static function getSalesByDay($startDate, $endDate) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT DATE(completed_at) as date, SUM(total_amount) as total FROM orders WHERE status = 'completed' AND completed_at BETWEEN ? AND ? GROUP BY DATE(completed_at) ORDER BY date ASC");
        $stmt->execute([$startDate, $endDate . ' 23:59:59']);
        return $stmt->fetchAll();
    }

    public static function getSalesByPaymentMethod($startDate, $endDate) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT p.payment_method, COUNT(*) as count, SUM(p.amount) as total FROM payments p JOIN orders o ON o.id = p.order_id WHERE p.status = 'completed' AND o.completed_at BETWEEN ? AND ? GROUP BY p.payment_method");
        $stmt->execute([$startDate, $endDate . ' 23:59:59']);
        return $stmt->fetchAll();
    }
}
