<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

date_default_timezone_set('Asia/Jakarta');

//  $router->get('/', function () use ($router) {
//      return [
//      	'Company' => 'PT Empore Heze Tama',
//      	'date' => date('Y-m-d H:i:s'),
//      	'Address' => 'Metropolitan tower, level13-A
//  					Jl. R.A. Kartini - T.B. Simatupang Kav. 14
//  					Cilandak, Jakarta Selatan
//  					Jakarta - 12430'

//      ];
//  });

$router->get('/', 'IndexController@index');

$router->post('set-modul-hris', 'CrmController@insertModule');
$router->post('set-user-hris', 'CrmController@insertUser');
$router->post('update-modul-hris', 'CrmController@updateModule');

$router->post('get-modul-crm', 'HrisController@getModule');
$router->post('update-modul-crm', 'HrisController@updateModuleCrm');

$router->post('login', 'AuthController@verify');
$router->post('send-attendance', 'AttendanceController@send');
$router->post('finger-store', 'AttendanceController@fingerStore');
$router->post('attendance-check-auth', 'AttendanceController@attendanceCheckAuth');
$router->post('login-attendance', 'AuthController@verifyAttendance');

// Production
$router->post('send-attendance-mhr', 'AttendanceController@sendMhr');
$router->post('attendance-check-auth-mhr', 'AttendanceController@attendanceCheckAuthMhr');
$router->post('login-attendance-mhr', 'AuthController@verifyAttendanceMhr');