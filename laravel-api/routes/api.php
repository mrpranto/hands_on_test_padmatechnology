<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function (Router $router) {

    $router->post('login', [AuthController::class, 'login'])->name('user.login');

    $router->group(['middleware' => 'auth:sanctum'], function ($router) {

        $router->get('/user', function (Request $request) {
            return $request->user();
        });

        $router->resource('contact', ContactController::class);

        $router->post('logout', [AuthController::class, 'logout'])->name('user.logout');

    });
});


//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
