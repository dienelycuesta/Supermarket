<?php
class User extends Model {
    protected static $table = 'users';
    protected static $fillable = ['first_name','last_name','email','password','phone','role','status','login_attempts','locked_until','last_login','avatar','email_verified_at','reset_token','reset_token_expires'];

    public static function authenticate($email, $password) {
        $user = self::findBy('email', $email);
        if (!$user || !password_verify($password, $user['password'])) return null;
        if ($user['status'] !== 'active') return null;
        return $user;
    }

    public static function createUser($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return self::create($data);
    }

    public static function updateProfile($id, $data) {
        unset($data['password']);
        return self::update($id, $data);
    }

    public static function changePassword($id, $newPassword) {
        return self::update($id, ['password' => password_hash($newPassword, PASSWORD_BCRYPT)]);
    }

    public static function getFullName($user) {
        return ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
    }

    public static function findCustomers($search = '', $page = 1) {
        $cond = "role = 'customer'";
        $params = [];
        if ($search) {
            $cond .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $s = "%{$search}%";
            $params = [$s, $s, $s];
        }
        return self::paginate($page, ITEMS_PER_PAGE, $cond, $params, 'created_at DESC');
    }

    public static function findStaff($search = '', $page = 1) {
        $cond = "role IN ('admin','manager','cashier')";
        $params = [];
        if ($search) {
            $cond .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $s = "%{$search}%";
            $params = [$s, $s, $s];
        }
        return self::paginate($page, ITEMS_PER_PAGE, $cond, $params, 'created_at DESC');
    }
}
