<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CreditAccountController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\IconController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
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

Route::post("/token", [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/token/revoke", [AuthController::class, "logout"]);
    Route::post("/token/revoke/all", [
        AuthController::class,
        "logoutAllDevices",
    ]);

    Route::get("/user", function (Request $request) {
        return $request->user();
    });

    Route::apiResource("budgets", BudgetController::class);
    Route::apiResource("credit-accounts", CreditAccountController::class);

    Route::post('/goals/{goal}/achived', [GoalController::class, 'setAchived']);
    Route::apiResource("goals", GoalController::class);

    Route::apiResource("icons", IconController::class);
    Route::apiResource("transactions", TransactionController::class);
});
