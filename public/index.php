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
    'controller' => 'App\Controllers\DashboardController',
    'action' => 'getUserDashboard',
    'auth' => true
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if(!$route){
    echo 'Page not found';
}
else{
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    $needsAuth = $handlerData['auth'] ?? false;

    $sessionUserId = $_SESSION['userId'] ?? null;
    if( $needsAuth && !$sessionUserId  ){
        echo 'Error. This page needs authentication';
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
