<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/dashboard', 'Home::dashboard', ['filter' => 'login']);

$routes->group('', ['filter' => ['login', 'role:Admin']], function ($routes) {
    // Master Medicine
    $routes->get('medicinelist/index', 'AdminController::index');
    $routes->get('medicinelist', 'AdminController::index');
    $routes->get('medicine/edit/(:num)', 'AdminController::edit/$1');
    $routes->get('add/medicine', 'AdminController::add');
    $routes->post('save/medicine', 'AdminController::save');
    $routes->post('save/medicine/(:num)', 'AdminController::save/$1');
    $routes->get('medicinelist/export-excel', 'AdminController::exportExcel');

    // Trx Medicine Stockin
    $routes->get('medicinestockin', 'AdminController::stockin');
    $routes->get('add/medicinestockin', 'AdminController::add_stockin');
    $routes->get('get-stok-obat', 'AdminController::getStokObat');
    $routes->post('save/stockin', 'AdminController::save_stockin');
    $routes->get('edit/medicinestockin/(:num)', 'AdminController::edit_stockin/$1');
    $routes->post('update/stockin/(:num)', 'AdminController::save_stockin/$1');

    // Trx Medicine Stockout
    $routes->get('medicinestockout', 'AdminController::stockout');
    $routes->get('add/medicinestockout', 'AdminController::add_stockout');
    $routes->post('save/stockout', 'AdminController::save_stockout');
    $routes->get('edit/medicinestockout/(:num)', 'AdminController::edit_stockout/$1');
    $routes->post('save/stockout/(:num)', 'AdminController::save_stockout/$1');

    // Trx Total Stock
    $routes->get('all-stock', 'AdminController::all_stock');
    $routes->get('getlogs/(:num)', 'AdminController::getLogs/$1');

    //User Management 
    $routes->get('admin', 'UserManagementController::index');
    $routes->get('add/admin', 'UserManagementController::add');
    $routes->post('save/admin', 'UserManagementController::store');

    //List Order Admin
    $routes->get('order', 'AdminController::order');
    $routes->get('order/detail/(:num)', 'AdminController::detail_order/$1');
    $routes->post('order/updateStatus/(:num)', 'AdminController::updateStatus/$1');

    //List Refund Admin
    $routes->get('refund', 'AdminController::refund_list');
    $routes->get('edit/refund/(:num)', 'AdminController::edit_refund/$1');
    $routes->post('update/refund/(:num)', 'AdminController::update_refund/$1');
});

// Role : User
$routes->group('', ['filter' => 'login'], function ($routes) {
    //E Commerce 
    $routes->get('/catalog', 'UserController::index');
    $routes->get('medicine/loadMore', 'UserController::loadMoreMedicines');
    $routes->get('cart', 'UserController::cart');
    $routes->post('cart/add', 'UserController::add_cart');
    $routes->get('cart/remove/(:num)', 'UserController::remove/$1');
    $routes->post('cart/ajax-update', 'UserController::ajaxUpdate');
    $routes->get('checkout', 'UserController::checkout');
    $routes->get('transaction', 'UserController::transaction');
    $routes->get('transaction/detail/(:num)', 'UserController::detail_transaction/$1');
    $routes->post('checkout/process', 'UserController::process_trx');
    $routes->post('transaction/confirm/(:num)', 'UserController::cancel/$1');
    $routes->get('transaction/invoice/(:num)', 'UserController::trxinvoicePdf/$1');

    // Refund
    $routes->get('req_refund', 'UserController::req_refund_list');
    $routes->get('form_refund', 'UserController::add_refund');
    $routes->post('refund/submit', 'UserController::submit_refund');
    $routes->get('confirm/refund/(:num)', 'UserController::confirm_refund/$1');
    $routes->get('detail/refund/(:num)', 'UserController::detail_refund/$1');
    $routes->get('refund/invoice/(:num)', 'UserController::refundinvoicePdf/$1');
});
// End Role : User
