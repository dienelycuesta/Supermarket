<?php
class Supplier extends Model {
    protected static $table = 'suppliers';
    protected static $fillable = ['name','contact_person','email','phone','rnc','address','city','notes','status'];

    public static function getActive() {
        return self::where("status = 'active'", [], 'name ASC');
    }

    public static function getWithProductCount() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT s.*, COUNT(p.id) as product_count FROM suppliers s LEFT JOIN products p ON p.supplier_id = s.id GROUP BY s.id ORDER BY s.name ASC");
        return $stmt->fetchAll();
    }

    public static function search($keyword = '', $page = 1) {
        $cond = "1=1";
        $params = [];
        if ($keyword) {
            $cond .= " AND (name LIKE ? OR contact_person LIKE ? OR rnc LIKE ? OR email LIKE ?)";
            $s = "%{$keyword}%";
            $params = [$s, $s, $s, $s];
        }
        return self::paginate($page, ITEMS_PER_PAGE, $cond, $params, 'name ASC');
    }

    public static function hasProducts($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as c FROM products WHERE supplier_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch()['c'] > 0;
    }
}
