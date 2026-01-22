<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;

/*
 * Public Routes
*/
Route::post('/students/register', [StudentController::class, 'store']);

