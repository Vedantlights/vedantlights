<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Spa::index');
$routes->get('contactus', 'Spa::index');
$routes->post('sendmail', 'Home::sendmail');
$routes->get('aboutus', 'Spa::index');
$routes->get('brandDetails/(:any)/(:any)', 'Spa::index');
$routes->get('productDetails/(:any)', 'Spa::index');
$routes->get('categoryDetails/(:any)/(:any)/(:any)', 'Spa::index');
$routes->get('categorywiseProductdetails/(:any)/(:any)', 'Spa::index');

// ---------------------------------------------------------------
// React admin SPA (new)
// ---------------------------------------------------------------
$routes->get('admin', 'Spa::index');
$routes->get('admin/(:any)', 'Spa::index');
$routes->get('admin/(:any)/(:any)', 'Spa::index');
$routes->get('admin/(:any)/(:any)/(:any)', 'Spa::index');
$routes->get('admin/(:any)/(:any)/(:any)/(:any)', 'Spa::index');
$routes->get('admin/(:any)/(:any)/(:any)/(:any)/(:any)', 'Spa::index');

// ---------------------------------------------------------------
// JSON API (for React frontend)
// ---------------------------------------------------------------
$routes->get('api/brands', 'Api::brands');
$routes->get('api/brands/(:num)/categories', 'Api::categories/$1');
$routes->get('api/brands/(:num)/products', 'Api::products/$1');
$routes->get('api/products/(:num)', 'Api::product/$1');

// ---------------------------------------------------------------
// Admin JSON API (for React admin)
// ---------------------------------------------------------------
$routes->post('api/admin/login', 'AdminApi::login');
$routes->post('api/admin/logout', 'AdminApi::logout');
$routes->get('api/admin/me', 'AdminApi::me');
$routes->get('api/admin/stats', 'AdminApi::stats');

$routes->get('api/admin/brands', 'AdminApi::brands');
$routes->post('api/admin/brands', 'AdminApi::createBrand');
$routes->post('api/admin/brands/(:num)', 'AdminApi::updateBrand/$1');
$routes->delete('api/admin/brands/(:num)', 'AdminApi::deleteBrand/$1');
// Fallback POST route for DELETE (XAMPP/Apache compatibility)
$routes->post('api/admin/brands/(:num)/delete', 'AdminApi::deleteBrand/$1');

$routes->get('api/admin/categories', 'AdminApi::categories');
$routes->post('api/admin/categories', 'AdminApi::createCategory');
$routes->post('api/admin/categories/(:num)', 'AdminApi::updateCategory/$1');
$routes->delete('api/admin/categories/(:num)', 'AdminApi::deleteCategory/$1');
// Fallback POST route for DELETE (XAMPP/Apache compatibility)
$routes->post('api/admin/categories/(:num)/delete', 'AdminApi::deleteCategory/$1');

$routes->get('api/admin/products', 'AdminApi::products');
$routes->get('api/admin/products/(:num)', 'AdminApi::product/$1');
$routes->post('api/admin/products', 'AdminApi::createProduct');
$routes->post('api/admin/products/(:num)', 'AdminApi::updateProduct/$1');
$routes->delete('api/admin/products/(:num)', 'AdminApi::deleteProduct/$1');
// Fallback POST route for DELETE (XAMPP/Apache compatibility)
$routes->post('api/admin/products/(:num)/delete', 'AdminApi::deleteProduct/$1');
