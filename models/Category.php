<?php
class Category extends Model {
    protected static $table = 'categories';
    protected static $fillable = ['name','slug','description','image','parent_id','sort_order','status'];

    public static function getActive() {
        return self::where("status = 'active'", [], 'sort_order ASC');
    }

    public static function getParentCategories() {
        return self::where("parent_id IS NULL AND status = 'active'", [], 'sort_order ASC');
    }

    public static function getSubcategories($parentId) {
        return self::where("parent_id = ? AND status = 'active'", [$parentId], 'sort_order ASC');
    }

    public static function getWithProductCount() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1 GROUP BY c.id ORDER BY c.sort_order ASC");
        return $stmt->fetchAll();
    }

    public static function findBySlug($slug) {
        return self::findBy('slug', $slug);
    }

    public static function hasProducts($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) as c FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch()['c'] > 0;
    }
}
