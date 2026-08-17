<?php
class Cart extends Model {
    protected static $table = 'cart';
    protected static $fillable = ['user_id','session_id','product_id','quantity'];

    private static function ownerCond($userId, $sessionId) {
        if ($userId) return ["user_id = ?", [$userId]];
        return ["session_id = ?", [$sessionId]];
    }

    public static function getItems($userId, $sessionId) {
        [$cond, $params] = self::ownerCond($userId, $sessionId);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT c.*, p.name, p.slug, p.sale_price, p.compare_price, p.image, p.stock, p.sku, (c.quantity * p.sale_price) as line_total FROM cart c JOIN products p ON p.id = c.product_id WHERE {$cond}");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function addItem($userId, $sessionId, $productId, $quantity = 1) {
        $db = Database::getInstance();
        if ($userId) {
            $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
        } else {
            $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$sessionId, $productId]);
        }
        $existing = $stmt->fetch();
        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?")->execute([$newQty, $existing['id']]);
            return $existing['id'];
        }
        return self::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public static function updateQuantity($id, $quantity, $userId, $sessionId) {
        [$cond, $params] = self::ownerCond($userId, $sessionId);
        $db = Database::getInstance();
        if ($quantity <= 0) {
            $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND {$cond}");
            $stmt->execute(array_merge([$id], $params));
        } else {
            $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND {$cond}");
            $stmt->execute(array_merge([$quantity, $id], $params));
        }
    }

    public static function removeItem($id, $userId, $sessionId) {
        [$cond, $params] = self::ownerCond($userId, $sessionId);
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND {$cond}");
        $stmt->execute(array_merge([$id], $params));
    }

    public static function clear($userId, $sessionId) {
        [$cond, $params] = self::ownerCond($userId, $sessionId);
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cart WHERE {$cond}");
        $stmt->execute($params);
    }

    public static function getCount($userId, $sessionId) {
        [$cond, $params] = self::ownerCond($userId, $sessionId);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COALESCE(SUM(quantity),0) as cnt FROM cart WHERE {$cond}");
        $stmt->execute($params);
        return (int)$stmt->fetch()['cnt'];
    }

    public static function getTotal($userId, $sessionId) {
        [$cond, $params] = self::ownerCond($userId, $sessionId);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COALESCE(SUM(c.quantity * p.sale_price),0) as total FROM cart c JOIN products p ON p.id = c.product_id WHERE {$cond}");
        $stmt->execute($params);
        return (float)$stmt->fetch()['total'];
    }

    public static function mergeGuestToUser($sessionId, $userId) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM cart WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $guestItems = $stmt->fetchAll();
        foreach ($guestItems as $item) {
            $exists = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $exists->execute([$userId, $item['product_id']]);
            $existing = $exists->fetch();
            if ($existing) {
                $db->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?")->execute([$item['quantity'], $existing['id']]);
                $db->prepare("DELETE FROM cart WHERE id = ?")->execute([$item['id']]);
            } else {
                $db->prepare("UPDATE cart SET user_id = ?, session_id = NULL WHERE id = ?")->execute([$userId, $item['id']]);
            }
        }
    }

    public static function validateStock($userId, $sessionId) {
        $items = self::getItems($userId, $sessionId);
        $issues = [];
        foreach ($items as $item) {
            if ($item['quantity'] > $item['stock']) {
                $issues[] = ['product' => $item['name'], 'requested' => $item['quantity'], 'available' => $item['stock']];
            }
        }
        return $issues;
    }
}
