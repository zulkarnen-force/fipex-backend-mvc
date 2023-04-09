<?php

namespace Config;


// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// auth

/*
 * --------------------------------------------------------------------
 * REST API
 * --------------------------------------------------------------------
 */
 
 $routes->get('fitalks', 'Fitalk\Controller\FitalkController::list');
$routes->put('fitalks/(:segment)', 'Fitalk\Controller\FitalkController::setStatus/$1');
$routes->get('fitalks/check/partisipant/(:segment)', 'Fitalk\Controller\FitalkController::isExists/$1');

/**
 * products members
 */
$routes->get('banners', 'Banner\BannerController::index');

$routes->get('member/product', 'ProductMemberController::getProductOfUserMember', ['filter' => 'auth']);
$routes->post('products/members/', 'ProductMemberController::create', ['filter' => 'OnlyAuthor']);
$routes->get('products/members', 'ProductMemberController::index');
$routes->get('products/members/(:segment)', 'ProductMemberController::show/$1');
$routes->put('products/members/(:segment)', 'ProductMemberController::update/$1');
$routes->delete('products/members/(:segment)', 'ProductMemberController::destroy/$1');


/**
 * Guest Books
 */
$routes->post('products/thumbnails', 'ProductThumbnailController::create');
$routes->get('products/thumbnails', 'ProductThumbnailController::index');
$routes->get('products/thumbnails/(:segment)', 'ProductThumbnailController::show/$1');
$routes->put('products/thumbnails/(:segment)', 'ProductThumbnailController::update/$1');
$routes->delete('products/thumbnails/(:segment)', 'ProductThumbnailController::destroy/$1');
$routes->post('upload/products/thumbnails/(:segment)', 'ProductThumbnailController::saveThumbnail/$1');

/**
 * Products
 */
// $routes->get('author/product', 'ProductController::getProductOfAuthor', ['filter' => 'auth']);
$routes->get('products/(:segment)/detail', 'ProductController::getProductDetail/$1');
$routes->get('products', 'ProductController::index');
$routes->get('products/categories/(:segment)', 'ProductController::productsOnCategory/$1');
$routes->get('products/(:segment)/author', 'ProductController::getAuthor/$1');
$routes->post('products', 'ProductController::create');
$routes->get('products/(:segment)', 'ProductController::getProductDetail/$1');

$routes->put('products/(:segment)', 'ProductController::update/$1');
$routes->delete('products/(:segment)', 'ProductController::destroy/$1');
$routes->get('products/leaderboard/categories/(:segment)', 'ProductController::getLeaderboardProductCategoryBased/$1');



// User CRUD

$routes->post('users/upload/', 'UserController::storeImage');
$routes->get('users', 'UserController::index');
$routes->get('users/(:segment)', 'UserController::show/$1');
$routes->put('users/(:segment)', 'UserController::update/$1');
$routes->delete('users/(:segment)', 'UserController::destroy/$1');


/**
 * Auth
 */
$routes->post('auth/register', 'UserController::register');
$routes->post('auth/login', 'UserController::login/$1');
$routes->get('auth/me', 'UserController::me', ['filter' => 'auth']);

/**
 * Use cases
 */
$routes->get('user/check/products/(:segment)', 'BadgeCollectionController::checkUserHasGivenBadge/$1');
$routes->get('user/send/badge/products/(:segment)', 'BadgeCollectionController::sendBadgeUserToProduct/$1', ['filter' => 'MakeSureEnoughBadge']);
$routes->post('user/cancle/badge/products/(:segment)', 'BadgeCollectionController::cancleBadgeOfProduct/$1', ['filter' => 'auth']);
$routes->get('products/(:segment)/badges', 'BadgeCollectionController::getBadgesOfProduct/$1');
$routes->get('products/(:segment)/comments', 'BadgeCollectionController::getCommentsOfProduct/$1');

/**
 * Badge Inventories
 */
$routes->get('badges/inventories', 'BadgeInventoryController::index');
$routes->get('badges/inventories/(:segment)', 'BadgeInventoryController::show/$1');
$routes->get('user/badges', 'BadgeInventoryController::getBadgesOfUser', ['filter' => 'auth']);
$routes->post('badges/inventories', 'BadgeInventoryController::create', ['filter' => 'EnsureOneUserOneBadgeInventory']);
$routes->put('badges/inventories/(:segment)', 'BadgeInventoryController::update/$1');
$routes->delete('badges/inventories/(:segment)', 'BadgeInventoryController::destroy/$1');



/**
 * Exhibitions
 */
$routes->post('exhibitions', 'ExhibitionController::create');
$routes->get('exhibitions', 'ExhibitionController::index');
$routes->get('exhibitions/(:segment)', 'ExhibitionController::show/$1');
$routes->put('exhibitions/(:segment)', 'ExhibitionController::update/$1');
$routes->delete('exhibitions/(:segment)', 'ExhibitionController::destroy/$1');

/**
 * Categories
 */

$routes->get('categories', 'CategoryController::index');
$routes->post('categories/', 'CategoryController::create');
$routes->get('categories/(:segment)', 'CategoryController::show/$1');
$routes->put('categories/(:segment)', 'CategoryController::update/$1');
$routes->delete('categories/(:segment)', 'CategoryController::destroy/$1');
$routes->get('categories/exhibitions/(:segment)', 'CategoryController::getCategoriesOfExhibition/$1');


/**
 * Guest Books
 */
$routes->post('guests/books/', 'GuestBookController::create');
$routes->get('guests/books', 'GuestBookController::index');
$routes->get('guests/books/limit/(:segment)/', 'GuestBookController::showLimit/$1');
$routes->get('guests/books/(:segment)', 'GuestBookController::show/$1');
$routes->put('guests/books/(:segment)', 'GuestBookController::update/$1');
$routes->delete('guests/books/(:segment)', 'GuestBookController::destroy/$1');

/**
 * Badge Collections
 */

$routes->get('badges/collections/', 'BadgeCollectionController::index');
$routes->get('badges/collections/(:segment)', 'BadgeCollectionController::show/$1');
$routes->post('badges/collections/', 'BadgeCollectionController::create');
$routes->put('badges/collections/(:segment)', 'BadgeCollectionController::update/$1', ['filter' => 'auth']);
$routes->delete('badges/collections/(:segment)', 'BadgeCollectionController::destroy/$1');

$routes->get('/', 'Home::index');




/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
