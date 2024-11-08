<?php

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\TypeUsersController;
use App\Http\Controllers\Api\CardsController;
use App\Http\Controllers\Api\ExpensesController;

Route::apiResource('/users', UsersController::class);
Route::apiResource('/type_users', TypeUsersController::class);
Route::apiResource('/cards_users', CardsController::class);
Route::apiResource('/expenses_users', ExpensesController::class);

