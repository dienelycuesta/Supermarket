<?php
// Public / Client routes
$router->get('/', 'HomeController@index');
$router->get('/catalog', 'CatalogController@index');
$router->get('/product/{slug}', 'ClientProductController@show');

// Auth routes
$router->get('/login', 'LoginController@showLogin');
$router->post('/login', 'LoginController@login');
$router->get('/register', 'RegisterController@showRegister');
$router->post('/register', 'RegisterController@register');
$router->get('/logout', 'LogoutController@logout');

// Cart
$router->get('/cart', 'CartController@index');

// Checkout (requires auth or guest checkout)
$router->get('/checkout', 'CheckoutController@index');
$router->post('/checkout', 'CheckoutController@process');
$router->get('/checkout/confirmation/{orderNumber}', 'CheckoutController@confirmation');

// Client orders & account
$router->get('/my-orders', 'ClientOrderController@index');
$router->get('/my-orders/{id}', 'ClientOrderController@show');
$router->get('/account', 'AccountController@index');
$router->post('/account/update', 'AccountController@updateProfile');
$router->post('/account/password', 'AccountController@changePassword');
$router->post('/account/address', 'AccountController@addAddress');
$router->post('/account/address/{id}/delete', 'AccountController@deleteAddress');

// Shortcut: /orders redirects to admin orders
$router->get('/orders', 'AdminOrderController@index');
$router->get('/orders/{id}', 'AdminOrderController@show');

// Admin routes
$router->group('/admin', function($router) {
    $router->get('/dashboard', 'DashboardController@index');

    // Products
    $router->get('/products', 'AdminProductController@index');
    $router->get('/products/create', 'AdminProductController@create');
    $router->post('/products/store', 'AdminProductController@store');
    $router->get('/products/{id}', 'AdminProductController@show');
    $router->get('/products/{id}/edit', 'AdminProductController@edit');
    $router->post('/products/{id}/update', 'AdminProductController@update');
    $router->post('/products/{id}/delete', 'AdminProductController@delete');

    // Categories
    $router->get('/categories', 'AdminCategoryController@index');
    $router->get('/categories/create', 'AdminCategoryController@create');
    $router->post('/categories/store', 'AdminCategoryController@store');
    $router->get('/categories/{id}/edit', 'AdminCategoryController@edit');
    $router->post('/categories/{id}/update', 'AdminCategoryController@update');
    $router->post('/categories/{id}/delete', 'AdminCategoryController@delete');

    // Suppliers
    $router->get('/suppliers', 'AdminSupplierController@index');
    $router->get('/suppliers/create', 'AdminSupplierController@create');
    $router->post('/suppliers/store', 'AdminSupplierController@store');
    $router->get('/suppliers/{id}', 'AdminSupplierController@show');
    $router->get('/suppliers/{id}/edit', 'AdminSupplierController@edit');
    $router->post('/suppliers/{id}/update', 'AdminSupplierController@update');
    $router->post('/suppliers/{id}/delete', 'AdminSupplierController@delete');

    // Inventory
    $router->get('/inventory', 'AdminInventoryController@index');
    $router->get('/inventory/movements', 'AdminInventoryController@movements');
    $router->get('/inventory/adjust', 'AdminInventoryController@adjust');
    $router->post('/inventory/adjust', 'AdminInventoryController@storeAdjustment');
    $router->get('/inventory/alerts', 'AdminInventoryController@alerts');
    $router->post('/inventory/alerts/{id}/resolve', 'AdminInventoryController@resolveAlert');
    $router->get('/inventory/purchase-orders', 'AdminInventoryController@purchaseOrders');
    $router->get('/inventory/purchase-orders/create', 'AdminInventoryController@createPurchaseOrder');
    $router->post('/inventory/purchase-orders/store', 'AdminInventoryController@storePurchaseOrder');
    $router->get('/inventory/purchase-orders/{id}', 'AdminInventoryController@showPurchaseOrder');
    $router->post('/inventory/purchase-orders/{id}/receive', 'AdminInventoryController@receivePurchaseOrder');

    // Orders
    $router->get('/orders', 'AdminOrderController@index');
    $router->get('/orders/in-store', 'AdminOrderController@createInStore');
    $router->post('/orders/in-store', 'AdminOrderController@storeInStore');
    $router->post('/orders/clear-all', 'AdminOrderController@clearAll');
    $router->get('/orders/{id}', 'AdminOrderController@show');
    $router->post('/orders/{id}/status', 'AdminOrderController@updateStatus');
    $router->post('/orders/{id}/delete', 'AdminOrderController@delete');

    // Payments
    $router->get('/payments', 'AdminPaymentController@index');
    $router->get('/payments/{orderId}/record', 'AdminPaymentController@recordPayment');
    $router->post('/payments/{orderId}/store', 'AdminPaymentController@storePayment');

    // Reports
    $router->get('/reports', 'AdminReportController@index');
    $router->get('/reports/export/{type}', 'AdminReportController@exportCsv');
});
