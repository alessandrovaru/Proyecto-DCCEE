<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
session_start();

// DECLARAR BASE_URL
$baseDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME'] );
$baseURL= 'http://' . $_SERVER['HTTP_HOST'] . $baseDir;
define('BASE_URL',$baseURL);

//CONECTAR BASE DE DATOS
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;

use Illuminate\Database\Connectors;


$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => 'ec2-54-225-92-1.compute-1.amazonaws.com',
    'database'  => 'd2cfsit453o1kj',
    'username'  => 'mpyevgppabkxvg',
    'password'  => '3f43b47371a72d2b5673fcb717eafa5f5c7f482454dff9d6a5b4e181d7524df9',
    'port'  	=> 5432,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();




//RUTAS
use Phroute\Phroute\RouteCollector;

$route = $_GET['route'] ?? '/';
$router = new RouteCollector();

//filtro para llevarte siempre al login si no has iniciado sesion
$router->filter('auth', function(){
    if(!isset($_SESSION['userId'])){
        header('location: ' . BASE_URL . 'auth/login');
        return false;
    }
});

//Login
$router->controller('/auth', App\Controllers\AuthController::class);

//no deja ir a estas rutas a menos que no hayas iniciado sesion
$router->group(['before' => 'auth'], function($router){
    $router->controller('/', App\Controllers\IndexController::class);
    if($_SESSION['role'] == 'Admin'){
        $router->controller('/admin', App\Controllers\Admin\AdminController::class);
    }
    $router->controller('/users', App\Controllers\UserController::class);
});



//iniciador de rutas
$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());
$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $route);
echo $response;


// cracion de usuario administrador

use App\Models\User;
use App\Models\Roleuser;

$newUserAdmin = User::firstOrNew(['email' => 'admin@admin.com'],
    ['name' => 'Admin',
    'last_name' => 'Admin',
    'password' => password_hash('admin', PASSWORD_DEFAULT)]);

$newUserAdmin->save();

//CREACION AUTOMATICA DE ROLES
use App\Models\Role;

$newAdmin = Role::firstOrNew(['role' => 'Admin']);
$newNormal = Role::firstOrNew(['role' => 'Normal']);

$newAdmin->save();
$newNormal->save();



$newRoleUser = Roleuser::firstOrNew(
    ['user_id' => $newUserAdmin->id],
    ['role_id' => '1']);

$newRoleUser->save();
