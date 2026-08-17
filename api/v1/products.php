<?php
/**
 * Products API - Search/autocomplete for admin
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

$search = trim($_GET['search'] ?? '');

if (strlen($search) < 2) {
    jsonResponse(['success' => true, 'products' => []]);
}

$db = Database::getInstance();

$sql = "SELECT id, name, sku, barcode, stock, sale_price, image
        FROM products
        WHERE is_active = 1
        AND (name LIKE :search OR sku LIKE :search2 OR barcode LIKE :search3)
        ORDER BY name ASC
        LIMIT 15";

$stmt = $db->prepare($sql);
$like = '%' . $search . '%';
$stmt->execute(['search' => $like, 'search2' => $like, 'search3' => $like]);
$products = $stmt->fetchAll();

jsonResponse(['success' => true, 'products' => $products]);
