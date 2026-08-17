<?php
class AdminPaymentController extends Controller {
    public function index() {
        $this->requireAdmin();
        $method = sanitize($_GET['method'] ?? '');
        $startDate = sanitize($_GET['start_date'] ?? date('Y-m-01'));
        $endDate = sanitize($_GET['end_date'] ?? date('Y-m-d'));
        $payments = Payment::getByDateRange($startDate, $endDate, $method);
        $totals = Payment::getTotalByMethod($startDate, $endDate);
        $this->render('admin/payments/index', [
            'pageTitle' => 'Pagos', 'payments' => $payments, 'totals' => $totals,
            'method' => $method, 'startDate' => $startDate, 'endDate' => $endDate,
        ]);
    }

    public function recordPayment($orderId) {
        $this->requireAdmin();
        $order = Order::getWithItems($orderId);
        if (!$order) $this->redirect(url('admin/orders'));
        $paidAmount = 0;
        foreach ($order['payments'] as $p) {
            if ($p['status'] === 'completed') $paidAmount += $p['amount'];
        }
        $this->render('admin/payments/record', [
            'pageTitle' => 'Registrar Pago', 'order' => $order,
            'paidAmount' => $paidAmount, 'balance' => $order['total_amount'] - $paidAmount,
        ]);
    }

    public function storePayment($orderId) {
        $this->requireAdmin();
        $this->validateCSRF();
        $method = sanitize($_POST['payment_method'] ?? '');
        $amount = (float)($_POST['amount'] ?? 0);
        $reference = sanitize($_POST['reference'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');

        if (!$method || $amount <= 0) {
            Session::flash('error', 'El método de pago y el monto son requeridos.');
            $this->redirect(url("admin/payments/{$orderId}/record"));
        }

        try {
            Payment::recordManualPayment($orderId, $method, $amount, $reference, $notes, Auth::id());
            Session::flash('success', 'Pago registrado y pedido completado.');
        } catch (Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
        }
        $this->redirect(url('admin/orders/' . $orderId));
    }
}
