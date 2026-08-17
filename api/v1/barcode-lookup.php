<?php
/**
 * Barcode Lookup API - Find product by barcode
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

$barcode = trim($_GET['barcode'] ?? '');

if (empty($barcode)) {
    jsonResponse(['success' => false, 'message' => 'Código de barras requerido'], 400);
}

$product = Product::findByBarcode($barcode);

if ($product) {
    jsonResponse([
        'success' => true,
        'product' => [
            'id' => $product['id'],
            'name' => $product['name'],
            'sku' => $product['sku'],
            'barcode' => $product['barcode'],
            'sale_price' => $product['sale_price'],
            'stock' => $product['stock'],
            'image' => $product['image']
        ]
    ]);
} else {
    jsonResponse(['success' => false, 'message' => 'Producto no encontrado']);
}
