<?php
class OrderItem extends Model {
    protected static $table = 'order_items';
    protected static $fillable = ['order_id','product_id','product_name','product_sku','quantity','unit_price','tax_rate','subtotal'];

    public static function getByOrder($orderId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public static function getTopProducts($limit = 10, $startDate = null, $endDate = null) {
        return Product::getTopSelling($limit, $startDate, $endDate);
    }
}
