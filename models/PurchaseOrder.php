<?php
class PurchaseOrder extends Model {
    protected static $table = 'purchase_orders';
    protected static $fillable = ['order_number','supplier_id','status','subtotal','tax_amount','total','notes','expected_date','received_date','created_by'];

    public static function getAll($status = '', $page = 1) {
        $cond = "1=1";
        $params = [];
        if ($status) { $cond .= " AND po.status = ?"; $params[] = $status; }
        $db = Database::getInstance();
        $cs = $db->prepare("SELECT COUNT(*) as total FROM purchase_orders po WHERE {$cond}");
        $cs->execute($params);
        $total = $cs->fetch()['total'];
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $stmt = $db->prepare("SELECT po.*, s.name as supplier_name, (SELECT COUNT(*) FROM purchase_order_items WHERE purchase_order_id = po.id) as items_count FROM purchase_orders po JOIN suppliers s ON s.id = po.supplier_id WHERE {$cond} ORDER BY po.created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute(array_merge($params, [$paginator->getLimit(), $paginator->getOffset()]));
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'paginator' => $paginator];
    }

    public static function getWithItems($id) {
        $order = self::find($id);
        if (!$order) return null;
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT s.name as supplier_name FROM suppliers s WHERE s.id = ?");
        $stmt->execute([$order['supplier_id']]);
        $supplier = $stmt->fetch();
        $order['supplier_name'] = $supplier['supplier_name'] ?? '';
        $stmt = $db->prepare("SELECT poi.*, p.name as product_name, p.sku FROM purchase_order_items poi JOIN products p ON p.id = poi.product_id WHERE poi.purchase_order_id = ?");
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();
        return $order;
    }

    public static function generateOrderNumber() {
        return 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }

    public static function updateTotals($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT SUM(total_cost) as subtotal FROM purchase_order_items WHERE purchase_order_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $subtotal = $row['subtotal'] ?? 0;
        $tax = $subtotal * 0.18;
        self::update($id, ['subtotal' => $subtotal, 'tax_amount' => $tax, 'total' => $subtotal + $tax]);
    }
}
