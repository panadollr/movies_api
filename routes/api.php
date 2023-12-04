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
    Route::get('newest', [MovieController::class, 'getMovies']);
    Route::get('single', [MovieController::class, 'getSingleMovies']);
    Route::get('series', [MovieController::class, 'getSeriesMovies']);
    Route::get('cartoon', [MovieController::class, 'getCartoonMovies']);
    Route::get('subteam', [MovieController::class, 'getSubTeamMovies']);
    Route::get('tv-shows', [MovieController::class, 'getTVShowMovies']);
    Route::get('upcoming', [MovieController::class, 'getUpcomingMovies']);
    Route::get('trending', [MovieController::class, 'getTrendingMovies']);
    Route::get('new-updated', [MovieController::class, 'getNewUpdatedMovies']);
    Route::get('new-updated/series', [MovieController::class, 'getNewUpdatedSeriesMovies']);
    Route::get('new-updated/single', [MovieController::class, 'getNewUpdatedSingleMovies']);
    Route::get('popular', [MovieController::class, 'getPopularMovies']);
    Route::get('air_today', [MovieController::class, 'getMoviesAirToday']);
    Route::get('highest-view', [MovieController::class, 'getHighestViewMovie']);
});

use App\Http\Controllers\User\BlogController;
Route::prefix('blogs')->group(function () {
    Route::get('', [BlogController::class, 'getBlogs']);
});

use App\Http\Controllers\User\ScheduledTasksController;
Route::get('run-scheduled-tasks', [ScheduledTasksController::class, 'runScheduledTasks']);

