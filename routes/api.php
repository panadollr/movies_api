<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//ADMIN
use App\Http\Controllers\Admin\DashboardController;
Route::prefix('admin')->group(function () {
    Route::get('general-infomation', [DashboardController::class, 'generalInformation']);

    Route::prefix('movies')->group(function () {
        Route::get('top', [DashboardController::class, 'topMovies']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('top', [DashboardController::class, 'topCategories']);
        });
});


//USER
use App\Http\Controllers\User\MovieController;
Route::prefix('movies')->group(function () {
    Route::get('', [MovieController::class, 'getAllMovies']);
    Route::get('new-updated', [MovieController::class, 'getNewUpdatedMovies']);
    Route::get('trending', [MovieController::class, 'getTrendingMovies']);
    Route::get('popular', [MovieController::class, 'getPopularMovies']);
});

use App\Http\Controllers\User\ScheduledTasksController;
Route::get('run-scheduled-tasks', [ScheduledTasksController::class, 'runScheduledTasks']);



