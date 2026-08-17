<?php
class DashboardController extends Controller {
    public function index() {
        $this->requireAdmin();
        $data = [
            'pageTitle' => 'Panel Principal',
            'todaySales' => Order::getTodaySales(),
            'monthlyRevenue' => Order::getMonthlyRevenue(),
            'totalProducts' => Product::countActive(),
            'pendingOrders' => Order::count("status = 'pending'"),
            'lowStockCount' => StockAlert::getCount(),
            'recentOrders' => Order::getRecentOrders(10),
            'lowStockProducts' => Product::getLowStock(),
        ];
        $this->render('admin/dashboard/index', $data);
    }
}
