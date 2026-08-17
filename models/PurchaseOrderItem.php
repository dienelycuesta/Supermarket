<?php
class PurchaseOrderItem extends Model {
    protected static $table = 'purchase_order_items';
    protected static $fillable = ['purchase_order_id','product_id','quantity_ordered','quantity_received','unit_cost','total_cost'];

    public static function getByOrder($orderId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT poi.*, p.name as product_name, p.sku FROM purchase_order_items poi JOIN products p ON p.id = poi.product_id WHERE poi.purchase_order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
