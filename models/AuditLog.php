<?php
class AuditLog extends Model {
    protected static $table = 'audit_log';
    protected static $fillable = ['user_id','action','entity_type','entity_id','old_values','new_values','ip_address','user_agent'];

    public static function log($action, $entityType, $entityId = null, $oldValues = null, $newValues = null) {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);
    }

    public static function getRecent($limit = 50) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT al.*, u.first_name, u.last_name FROM audit_log al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
