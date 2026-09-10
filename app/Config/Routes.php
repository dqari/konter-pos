<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =====================================
// RUTE AUTENTIKASI & DASHBOARD
// =====================================
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->post('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index');

// =====================================
// RUTE MODUL INVENTORI (V2 - Modal Pop-Up)
// =====================================
$routes->get('/inventori', 'Inventori::index');
$routes->post('/inventori/simpan', 'Inventori::simpan');
$routes->post('/inventori/update', 'Inventori::update');
$routes->get('/inventori/stok/(:segment)', 'Inventori::stok/$1');
$routes->post('/inventori/simpan_stok', 'Inventori::simpan_stok');
$routes->post('/inventori/hapus_stok', 'Inventori::hapus_stok');

// =====================================
// RUTE MODUL KASIR (V2 - Cerdas Barcode)
// =====================================
$routes->get('/kasir', 'Kasir::index');
$routes->get('/kasir/pelanggan', 'Kasir::pelanggan');
$routes->post('/kasir/scan', 'Kasir::scan'); 
$routes->post('/kasir/update_diskon', 'Kasir::update_diskon');
$routes->post('/kasir/hapus_keranjang/(:segment)', 'Kasir::hapus_keranjang/$1');
$routes->post('/kasir/checkout', 'Kasir::checkout');
$routes->get('/kasir/cetak/(:segment)', 'Kasir::cetak/$1');
$routes->get('/kasir/cetak/(:segment)/(:segment)', 'Kasir::cetak/$1/$2');

// =====================================
// RUTE RETUR TRANSAKSI
// =====================================
$routes->get('/retur', 'Retur::index');
$routes->post('/retur/proses', 'Retur::proses');

// =====================================
// RUTE STOCK OPNAME
// =====================================
$routes->get('/opname', 'StockOpname::index');
$routes->post('/opname/simpan', 'StockOpname::simpan');

// =====================================
// RUTE MODUL LAPORAN (V2 - Hitung QTY & Modal)
// =====================================
$routes->group('laporan', ['filter' => 'role:Owner'], static function ($routes) {
	$routes->get('keuangan', 'Laporan::keuangan');
	$routes->get('cetak', 'Laporan::cetak');
});

// =====================================
// RUTE MANAJEMEN USER (Khusus Owner)
// =====================================
$routes->group('usermanajemen', ['filter' => 'role:Owner'], static function ($routes) {
	$routes->get('/', 'UserManajemen::index');
	$routes->get('tambah', 'UserManajemen::tambah');
	$routes->post('simpan', 'UserManajemen::simpan');
	$routes->get('edit/(:num)', 'UserManajemen::edit/$1');
	$routes->post('update', 'UserManajemen::update');
	$routes->post('hapus/(:num)', 'UserManajemen::hapus/$1');
});

// =====================================
// RUTE PROFIL TOKO (Khusus Owner)
// =====================================
$routes->group('toko', ['filter' => 'role:Owner'], static function ($routes) {
	$routes->get('/', 'Toko::index');
	$routes->post('update', 'Toko::update');
});

// =====================================
// RUTE BACKUP DATABASE (KHUSUS OWNER)
// =====================================
$routes->group('backup', ['filter' => 'role:Owner'], static function ($routes) {
	$routes->get('/', 'Backup::index');
	$routes->post('create', 'Backup::create');
	$routes->get('download/(:segment)', 'Backup::download/$1');
});

// =====================================
// RUTE AUDIT LOG (KHUSUS OWNER)
// =====================================
$routes->group('audit', ['filter' => 'role:Owner'], static function ($routes) {
	$routes->get('/', 'AuditLog::index');
});