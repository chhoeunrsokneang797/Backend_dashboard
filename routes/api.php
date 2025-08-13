<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProvinceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// rest route
// localhost:8000/api/role , method get
Route::get("role", [RoleController::class, 'index']);
Route::post("role", [RoleController::class, 'store']);
Route::get("role/{id}", [RoleController::class, 'show']);
Route::put("role/{id}", [RoleController::class, 'update']);
Route::delete("role/{id}", [RoleController::class, 'destory']);
Route::post("role/changeStatus", [RoleController::class, 'changeStatus']);

// category
Route::apiResource("/categories", CategoryController::class);
Route::apiResource("province", ProvinceController::class);