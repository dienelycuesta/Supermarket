<?php
class Validator {
    public static function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;
            $label = ucfirst(str_replace('_', ' ', $field));

            foreach ($fieldRules as $rule) {
                $param = null;
                if (strpos($rule, ':') !== false) {
                    [$rule, $param] = explode(':', $rule, 2);
                }

                switch ($rule) {
                    case 'required':
                        if (empty($value) && $value !== '0') {
                            $errors[$field][] = "{$label} is required.";
                        }
                        break;
                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "{$label} must be a valid email.";
                        }
                        break;
                    case 'min':
                        if ($value && strlen($value) < (int)$param) {
                            $errors[$field][] = "{$label} must be at least {$param} characters.";
                        }
                        break;
                    case 'max':
                        if ($value && strlen($value) > (int)$param) {
                            $errors[$field][] = "{$label} must not exceed {$param} characters.";
                        }
                        break;
                    case 'numeric':
                        if ($value && !is_numeric($value)) {
                            $errors[$field][] = "{$label} must be a number.";
                        }
                        break;
                    case 'integer':
                        if ($value && !ctype_digit((string)$value)) {
                            $errors[$field][] = "{$label} must be an integer.";
                        }
                        break;
                    case 'unique':
                        [$table, $col] = explode(',', $param);
                        $excludeId = $data['_exclude_id'] ?? null;
                        $db = Database::getInstance();
                        $sql = "SELECT COUNT(*) as c FROM {$table} WHERE {$col} = ?";
                        $params2 = [$value];
                        if ($excludeId) {
                            $sql .= " AND id != ?";
                            $params2[] = $excludeId;
                        }
                        $stmt = $db->prepare($sql);
                        $stmt->execute($params2);
                        if ($stmt->fetch()['c'] > 0) {
                            $errors[$field][] = "{$label} already exists.";
                        }
                        break;
                    case 'in':
                        $allowed = explode(',', $param);
                        if ($value && !in_array($value, $allowed)) {
                            $errors[$field][] = "{$label} is invalid.";
                        }
                        break;
                    case 'confirmed':
                        $confirm = $data[$field . '_confirmation'] ?? '';
                        if ($value !== $confirm) {
                            $errors[$field][] = "{$label} confirmation does not match.";
                        }
                        break;
                    case 'ean13':
                        if ($value && !self::validateEAN13($value)) {
                            $errors[$field][] = "{$label} is not a valid EAN-13 barcode.";
                        }
                        break;
                }
            }
        }
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    public static function validateEAN13($barcode) {
        if (!preg_match('/^\d{13}$/', $barcode)) return false;
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$barcode[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        $check = (10 - ($sum % 10)) % 10;
        return $check === (int)$barcode[12];
    }
}
