<?php
abstract class Model {
    protected static $table;
    protected static $primaryKey = 'id';
    protected static $fillable = [];

    public static function find($id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findBy($field, $value) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::$table . " WHERE {$field} = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetch() ?: null;
    }

    public static function all($orderBy = 'id DESC') {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::$table . " ORDER BY {$orderBy}";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    public static function where($conditions = '1=1', $params = [], $orderBy = 'id DESC', $limit = null, $offset = null) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::$table . " WHERE {$conditions} ORDER BY {$orderBy}";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count($conditions = '1=1', $params = []) {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE {$conditions}";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    public static function create($data) {
        $db = Database::getInstance();
        $filtered = array_intersect_key($data, array_flip(static::$fillable));
        $fields = implode(', ', array_keys($filtered));
        $placeholders = implode(', ', array_fill(0, count($filtered), '?'));
        $sql = "INSERT INTO " . static::$table . " ({$fields}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);
        $stmt->execute(array_values($filtered));
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getInstance();
        $filtered = array_intersect_key($data, array_flip(static::$fillable));
        $sets = implode(', ', array_map(fn($f) => "{$f} = ?", array_keys($filtered)));
        $sql = "UPDATE " . static::$table . " SET {$sets} WHERE " . static::$primaryKey . " = ?";
        $params = array_values($filtered);
        $params[] = $id;
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id) {
        $db = Database::getInstance();
        $sql = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public static function paginate($page = 1, $perPage = 20, $conditions = '1=1', $params = [], $orderBy = 'id DESC') {
        $total = static::count($conditions, $params);
        $paginator = new Paginator($total, $perPage, $page);
        $data = static::where($conditions, $params, $orderBy, $paginator->getLimit(), $paginator->getOffset());
        return ['data' => $data, 'total' => $total, 'paginator' => $paginator];
    }

    public static function query($sql, $params = []) {
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
