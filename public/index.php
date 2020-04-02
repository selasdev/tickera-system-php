<?php

ini_set('display_errors', 1);
ini_set('display_startup_erro', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;

session_start();
if(file_exists("../.env")){
    $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/..');
    $dotenv->load();
}

$routerContainer = new RouterContainer();

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$map = $routerContainer->getMap();
$map->get('index', "/", [
    'controller' => 'App\Controllers\MainController',
    'action' => 'mainAction'
]);
$map->post('dummyUser', "/", [
    'controller' => 'App\Controllers\MainController',
    'action' => 'createDummyUser'
]);
$map->get('showRegisterForm', '/signup', [
    'controller' => 'App\Controllers\RegisterController',
    'action' => 'getRegister'
]);
$map->post('registerUser', '/signup', [
    'controller' => 'App\Controllers\RegisterController',
    'action' => 'postRegister'
]);
$map->get('getLogin', '/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogin'
]);
$map->post('loginUser', '/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLogin'
]);
$map->get('home', '/home', [
    'controller' => 'App\Controllers\HomeController',
    'action' => 'getUserDashboard',
    'auth' => true,
    'forUser' => true,
]);
$map->post('homePost', '/home', [
    'controller' => 'App\Controllers\HomeController',
    'action' => 'postUserDashboard',
    'auth' => true,
    'forUser' => true,
]);

$map->get('buy-ticket-form', "/buy/ticket", [
    'controller' => 'App\Controllers\TicketController',
    'action' => 'getTicketForm',
    'auth' => true,
    'forUser' => true,
]);

$map->post('buy-ticket-form-post', "/buy/ticket", [
    'controller' => 'App\Controllers\TicketController',
    'action' => 'postTicketForm',
    'auth' => true,
    'forUser' => true,
]);

$map->get('buy-success', "/buy/success", [
    'controller' => 'App\Controllers\TicketController',
    'action' => 'getBuyTicketSuccess',
    'auth' => true,
    'forUser' => true,
]);
$map->get('homeAdmin', '/home/admin', [
    'controller' => 'App\Controllers\HomeController',
    'action' => 'getAdminDashboard',
    'auth' => true,
    'forAdmin' => true,
]);
$map->post('homeAdminPost', '/home/admin', [
    'controller' => 'App\Controllers\HomeController',
    'action' => 'handleButtonClick',
    'auth' => true,
    'forAdmin' => true,
]);
$map->get('showTicket', "/entry/show", [
    'controller' => 'App\Controllers\TicketController',
    'action' => 'getShowTicketEntry',
    'auth' => true,
    'forAdmin' => true,
]);
$map->get('logout', "/logout", [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'logout',
    'auth' => true
]);
$map->get('editTicket', "/entry/edit", [
    'controller' => 'App\Controllers\TicketController',
    'action' => 'getEditTicketEntry',
    'auth' => true,
    'forAdmin' => true,
]);
$map->post('postEditTicket', "/entry/edit", [
    'controller' => 'App\Controllers\TicketController',
    'action' => 'postEditTicketEntry',
$map->get('addEvent', "/event/add", [
    'controller' => 'App\Controllers\EventController',
    'action' => 'getAddEventForm',
    'auth' => true,
    'forAdmin' => true,
]);
$map->post('addEventPost', "/event/add", [
    'controller' => 'App\Controllers\EventController',
    'action' => 'postAddEventForm',
    'auth' => true,
    'forAdmin' => true,
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if(!$route){
    echo 'Page not found';
}
else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    $needsAuth = $handlerData['auth'] ?? false;
    $forUser = $handlerData['forUser'] ?? false;
    $forAdmin = $handlerData['forAdmin'] ?? false;
    
    $isAdmin = $_SESSION['isAdmin'] ?? false;
    $sessionUserId = $_SESSION['userId'] ?? null;

    if( $needsAuth && !$sessionUserId  ){
        echo 'Error. This page needs authentication';
        die;
    }
    else if($forAdmin && !$isAdmin){
        echo 'Error. This page is for admins';
        die;
    }
    else if($forUser && $isAdmin){
        echo 'Error. This page is for users';
        die;
    }

    $controller = new $controllerName;
    $response = $controller->$actionName($request);

    foreach($response->getHeaders() as $headerKey => $headerValues){
        foreach($headerValues as $header){
            header(sprintf('%s: %s', $headerKey, $header), false);
        }
    }
    http_response_code($response->getStatusCode());
    echo $response->getBody();
}
