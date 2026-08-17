<?php
class AdminReportController extends Controller {
    public function index() {
        $this->requireAdmin();
        $startDate = sanitize($_GET['start_date'] ?? date('Y-m-01'));
        $endDate = sanitize($_GET['end_date'] ?? date('Y-m-d'));
        $salesStats = Order::getSalesStats($startDate, $endDate);
        $topProducts = Product::getTopSelling(10, $startDate, $endDate);
        $paymentTotals = Payment::getTotalByMethod($startDate, $endDate);
        $this->render('admin/reports/index', [
            'pageTitle' => 'Reportes',
            'startDate' => $startDate, 'endDate' => $endDate,
            'salesStats' => $salesStats, 'topProducts' => $topProducts,
            'paymentTotals' => $paymentTotals,
        ]);
    }

    public function exportCsv($type) {
        $this->requireAdmin();
        $startDate = sanitize($_GET['start_date'] ?? date('Y-m-01'));
        $endDate = sanitize($_GET['end_date'] ?? date('Y-m-d'));
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $type . '_' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');

        if ($type === 'sales') {
            fputcsv($out, ['Date', 'Orders', 'Total']);
            $data = Order::getSalesByDay($startDate, $endDate);
            foreach ($data as $row) fputcsv($out, [$row['date'], 1, $row['total']]);
        } elseif ($type === 'inventory') {
            fputcsv($out, ['Product', 'SKU', 'Stock', 'Cost Price', 'Stock Value']);
            $products = Product::where("is_active = 1", [], 'name ASC');
            foreach ($products as $p) fputcsv($out, [$p['name'], $p['sku'], $p['stock'], $p['cost_price'], $p['stock'] * $p['cost_price']]);
        } elseif ($type === 'payments') {
            fputcsv($out, ['Date', 'Order', 'Method', 'Amount', 'Status', 'Reference']);
            $payments = Payment::getByDateRange($startDate, $endDate);
            foreach ($payments as $p) fputcsv($out, [$p['created_at'], $p['order_id'], $p['payment_method'], $p['amount'], $p['status'], $p['reference']]);
        }
        fclose($out);
        exit;
    }
}
