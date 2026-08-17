<?php
class CustomerAddress extends Model {
    protected static $table = 'customer_addresses';
    protected static $fillable = ['user_id','label','address_line1','address_line2','city','state','postal_code','is_default','latitude','longitude'];

    public static function getByUser($userId) {
        return self::where("user_id = ?", [$userId], 'is_default DESC, id DESC');
    }

    public static function getDefault($userId) {
        $addr = self::where("user_id = ? AND is_default = 1", [$userId], 'id DESC', 1);
        return $addr[0] ?? null;
    }

    public static function setDefault($id, $userId) {
        $db = Database::getInstance();
        $db->prepare("UPDATE customer_addresses SET is_default = 0 WHERE user_id = ?")->execute([$userId]);
        $db->prepare("UPDATE customer_addresses SET is_default = 1 WHERE id = ? AND user_id = ?")->execute([$id, $userId]);
    }
}
