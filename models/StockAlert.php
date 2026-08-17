<?php
class StockAlert extends Model {
    protected static $table = 'stock_alerts';
    protected static $fillable = ['product_id','alert_type','current_stock','min_stock','is_resolved','resolved_at'];

    public static function getActive() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT sa.*, p.name as product_name, p.sku, p.stock as current_stock_now FROM stock_alerts sa JOIN products p ON p.id = sa.product_id WHERE sa.is_resolved = 0 ORDER BY sa.created_at DESC");
        return $stmt->fetchAll();
    }

    public static function checkAndCreate($productId) {
        $product = Product::find($productId);
        if (!$product) return;
        if ($product['stock'] <= 0) {
            self::createAlert($productId, 'out_of_stock', $product['stock'], $product['min_stock']);
        } elseif ($product['stock'] <= $product['min_stock']) {
            self::createAlert($productId, 'low_stock', $product['stock'], $product['min_stock']);
        } else {
            $db = Database::getInstance();
            $db->prepare("UPDATE stock_alerts SET is_resolved = 1, resolved_at = NOW() WHERE product_id = ? AND is_resolved = 0")->execute([$productId]);
        }
    }

    public static function createAlert($productId, $type, $currentStock, $minStock) {
        $existing = self::where("product_id = ? AND alert_type = ? AND is_resolved = 0", [$productId, $type]);
        if (!empty($existing)) return;
        return self::create([
            'product_id' => $productId,
            'alert_type' => $type,
            'current_stock' => $currentStock,
            'min_stock' => $minStock,
        ]);
    }

    public static function resolve($id) {
        return self::update($id, ['is_resolved' => 1, 'resolved_at' => date('Y-m-d H:i:s')]);
    }

    public static function getCount() {
        return self::count("is_resolved = 0");
    }
}
