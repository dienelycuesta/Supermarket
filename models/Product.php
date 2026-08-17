<?php
class Product extends Model {
    protected static $table = 'products';
    protected static $fillable = ['sku','barcode','name','slug','description','category_id','supplier_id','cost_price','sale_price','compare_price','stock','min_stock','max_stock','unit','image','is_featured','is_active','tax_rate','weight','brand'];

    public static function search($keyword = '', $categoryId = 0, $sortBy = 'name_asc', $page = 1, $perPage = 20) {
        $cond = "p.is_active = 1";
        $params = [];
        if ($keyword) {
            $cond .= " AND MATCH(p.name, p.description, p.brand) AGAINST(? IN BOOLEAN MODE)";
            $params[] = $keyword . '*';
        }
        if ($categoryId) {
            $cond .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        $sortMap = [
            'name_desc' => 'p.name DESC',
            'price_asc' => 'p.sale_price ASC',
            'price_desc' => 'p.sale_price DESC',
            'newest' => 'p.created_at DESC',
        ];
        $order = isset($sortMap[$sortBy]) ? $sortMap[$sortBy] : 'p.name ASC';
        $db = Database::getInstance();
        // Count
        $countSql = "SELECT COUNT(*) as total FROM products p WHERE {$cond}";
        $cs = $db->prepare($countSql);
        $cs->execute($params);
        $total = $cs->fetch()['total'];
        $paginator = new Paginator($total, $perPage, $page);
        // Data
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE {$cond} ORDER BY {$order} LIMIT {$paginator->getLimit()} OFFSET {$paginator->getOffset()}";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'paginator' => $paginator];
    }

    public static function findByBarcode($barcode) {
        return self::findBy('barcode', $barcode);
    }

    public static function findBySku($sku) {
        return self::findBy('sku', $sku);
    }

    public static function findBySlug($slug) {
        return self::findBy('slug', $slug);
    }

    public static function getFeatured($limit = 8) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_featured = 1 AND p.is_active = 1 ORDER BY p.created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getOnSale($limit = 10) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 AND p.compare_price IS NOT NULL AND p.compare_price > p.sale_price AND p.stock > 0 ORDER BY ((p.compare_price - p.sale_price) / p.compare_price) DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }


    public static function getByCategory($categoryId, $page = 1, $perPage = 20) {
        return self::search('', $categoryId, 'name_asc', $page, $perPage);
    }

    public static function getLowStock() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM products WHERE is_active = 1 AND stock <= min_stock ORDER BY stock ASC");
        return $stmt->fetchAll();
    }

    public static function getOutOfStock() {
        return self::where("is_active = 1 AND stock = 0", [], 'name ASC');
    }

    public static function updateStock($id, $quantity) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET stock = ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }

    public static function decrementStock($id, $quantity) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmt->execute([$quantity, $id, $quantity]);
        return $stmt->rowCount() > 0;
    }

    public static function incrementStock($id, $quantity) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }

    public static function getTopSelling($limit = 10, $startDate = null, $endDate = null) {
        $db = Database::getInstance();
        $cond = "o.status = 'completed'";
        $params = [];
        if ($startDate) { $cond .= " AND o.created_at >= ?"; $params[] = $startDate; }
        if ($endDate) { $cond .= " AND o.created_at <= ?"; $params[] = $endDate . ' 23:59:59'; }
        $sql = "SELECT oi.product_name, oi.product_sku, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as total_revenue FROM order_items oi JOIN orders o ON o.id = oi.order_id WHERE {$cond} GROUP BY oi.product_id ORDER BY total_sold DESC LIMIT " . (int)$limit;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countActive() {
        return self::count("is_active = 1");
    }

    public static function getAllActive($sortBy = 'name_asc', $page = 1, $perPage = 20) {
        return self::search('', 0, $sortBy, $page, $perPage);
    }

    public static function getWithCategory($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, s.name as supplier_name FROM products p LEFT JOIN categories c ON c.id = p.category_id LEFT JOIN suppliers s ON s.id = p.supplier_id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function adminSearch($keyword = '', $categoryId = 0, $page = 1) {
        $cond = "1=1";
        $params = [];
        if ($keyword) {
            $cond .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $s = "%{$keyword}%";
            $params = [$s, $s, $s];
        }
        if ($categoryId) {
            $cond .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        $db = Database::getInstance();
        $cs = $db->prepare("SELECT COUNT(*) as total FROM products p WHERE {$cond}");
        $cs->execute($params);
        $total = $cs->fetch()['total'];
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE {$cond} ORDER BY p.created_at DESC LIMIT {$paginator->getLimit()} OFFSET {$paginator->getOffset()}";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(), 'total' => $total, 'paginator' => $paginator];
    }
}
