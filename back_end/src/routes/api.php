<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ProfileController;


Route::get('/jobs/search', [JobController::class, 'search']);

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('companies', CompanyController::class)->only(['index', 'show']);
Route::apiResource('blog-posts', BlogPostController::class)->only(['index', 'show']);
Route::apiResource('jobs', JobController::class)->only(['index', 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('profile');
    });

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::post('/jobs/{id}/apply', [JobController::class, 'apply']);
    Route::get('/my-applications', [ApplicationController::class, 'myApplications']);
    Route::get('/employer/applications', [ApplicationController::class, 'employerApplications']);
    Route::put('/employer/applications/{id}', [ApplicationController::class, 'updateEmployerApplication']);

    Route::get('/favourites', [FavouriteController::class, 'index']);
    Route::post('/favourites', [FavouriteController::class, 'store']);
    Route::delete('/favourites/{jobId}', [FavouriteController::class, 'destroy']);

    Route::apiResource('jobs', JobController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('companies', CompanyController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('blog-posts', BlogPostController::class)->only(['store', 'update', 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/admin/overview', [AdminController::class, 'overview']);
});