<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//ADMIN
use App\Http\Controllers\Admin\DashboardController;
Route::get('general-infomation', [DashboardController::class, 'generalInformation']);
Route::get('top-categories', [DashboardController::class, 'topCategories']);
Route::get('top-movies', [DashboardController::class, 'topMovies']);


//USER
use App\Http\Controllers\User\MovieController;
Route::get('new-updated-movies', [MovieController::class, 'getNewUpdatedMovies']);
Route::get('trending-movies', [MovieController::class, 'getTrendingMovies']);
Route::get('popular-movies', [MovieController::class, 'getPopularMovies']);


Route::get('run_scheduled_tasks', [MovieController::class, 'run_scheduled_tasks']);



