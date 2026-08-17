<?php
/**
 * Reports API - JSON data for charts
 */
require_once __DIR__ . '/../../core/Bootstrap.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$db = Database::getInstance();

switch ($action) {
    case 'sales_chart':
        $days = min(90, max(7, (int)($_GET['days'] ?? 30)));
        $from = $_GET['from'] ?? date('Y-m-d', strtotime("-{$days} days"));
        $to = $_GET['to'] ?? date('Y-m-d');

        $stmt = $db->prepare("
            SELECT DATE(created_at) as sale_date, SUM(total_amount) as total
            FROM orders
            WHERE status NOT IN ('cancelled')
            AND DATE(created_at) BETWEEN :from AND :to
            GROUP BY DATE(created_at)
            ORDER BY sale_date ASC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);
        $rows = $stmt->fetchAll();

        $labels = [];
        $data = [];
        $dateMap = [];
        foreach ($rows as $r) {
            $dateMap[$r['sale_date']] = (float)$r['total'];
        }

        $current = new DateTime($from);
        $end = new DateTime($to);
        while ($current <= $end) {
            $d = $current->format('Y-m-d');
            $labels[] = $current->format('M d');
            $data[] = $dateMap[$d] ?? 0;
            $current->modify('+1 day');
        }

        jsonResponse(['success' => true, 'labels' => $labels, 'data' => $data]);
        break;

    case 'payment_methods':
        $stmt = $db->query("
            SELECT payment_method, SUM(amount) as total
            FROM payments
            WHERE status = 'completed'
            GROUP BY payment_method
            ORDER BY total DESC
        ");
        $rows = $stmt->fetchAll();

        $labels = [];
        $data = [];
        $methodNames = ['cash'=>'Cash','card_pos'=>'Card POS','stripe'=>'Stripe','bank_transfer'=>'Bank Transfer'];
        foreach ($rows as $r) {
            $labels[] = $methodNames[$r['payment_method']] ?? ucfirst($r['payment_method']);
            $data[] = (float)$r['total'];
        }

        jsonResponse(['success' => true, 'labels' => $labels, 'data' => $data]);
        break;

    case 'sales_summary':
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');

        $stmt = $db->prepare("
            SELECT
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_order,
                SUM(CASE WHEN payment_status='paid' THEN total_amount ELSE 0 END) as paid_amount
            FROM orders
            WHERE status NOT IN ('cancelled')
            AND DATE(created_at) BETWEEN :from AND :to
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);
        $summary = $stmt->fetch();

        jsonResponse(['success' => true, 'summary' => $summary]);
        break;

    case 'top_products':
        $limit = min(20, max(5, (int)($_GET['limit'] ?? 10)));
        $stmt = $db->prepare("
            SELECT p.name, p.sku, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as total_revenue
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status NOT IN ('cancelled')
            GROUP BY oi.product_id
            ORDER BY total_sold DESC
            LIMIT :lim
        ");
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();

        jsonResponse(['success' => true, 'products' => $products]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Acción inválida'], 400);
}
