<?php
class InventoryMovement extends Model {
    protected static $table = 'inventory_movements';
    protected static $fillable = ['product_id','type','quantity','previous_stock','new_stock','reference_type','reference_id','notes','created_by'];

    public static function record($productId, $type, $quantity, $referenceType = null, $referenceId = null, $notes = '', $userId = null) {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $product = Product::find($productId);
            if (!$product) throw new Exception('Product not found');
            $prevStock = $product['stock'];
            $change = in_array($type, ['entry', 'return']) ? abs($quantity) : -abs($quantity);
            if ($type === 'adjustment') $change = $quantity;
            $newStock = $prevStock + $change;
            if ($newStock < 0) $newStock = 0;

            Product::updateStock($productId, $newStock);
            $id = self::create([
                'product_id' => $productId,
                'type' => $type,
                'quantity' => $change,
                'previous_stock' => $prevStock,
                'new_stock' => $newStock,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'created_by' => $userId,
            ]);
            StockAlert::checkAndCreate($productId);
            $db->commit();
            return $id;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function getByProduct($productId, $page = 1) {
        $db = Database::getInstance();
        $total = self::count("product_id = ?", [$productId]);
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $stmt = $db->prepare("SELECT im.*, u.first_name, u.last_name FROM inventory_movements im LEFT JOIN users u ON u.id = im.created_by WHERE im.product_id = ? ORDER BY im.created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$productId, $paginator->getLimit(), $paginator->getOffset()]);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'paginator' => $paginator];
    }

    public static function getRecent($limit = 20) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT im.*, p.name as product_name, p.sku, u.first_name, u.last_name FROM inventory_movements im JOIN products p ON p.id = im.product_id LEFT JOIN users u ON u.id = im.created_by ORDER BY im.created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getByDateRange($startDate, $endDate, $type = null) {
        $cond = "im.created_at BETWEEN ? AND ?";
        $params = [$startDate, $endDate . ' 23:59:59'];
        if ($type) { $cond .= " AND im.type = ?"; $params[] = $type; }
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT im.*, p.name as product_name, p.sku, u.first_name, u.last_name FROM inventory_movements im JOIN products p ON p.id = im.product_id LEFT JOIN users u ON u.id = im.created_by WHERE {$cond} ORDER BY im.created_at DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
