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
// Route::prefix('movies')->group(function () {
    Route::get('phim-moi', [MovieController::class, 'getNewestMovies']);
    Route::get('phim-le', [MovieController::class, 'getSingleMovies']);
    Route::get('phim-bo', [MovieController::class, 'getSeriesMovies']);
    Route::get('hoat-hinh', [MovieController::class, 'getCartoonMovies']);
    Route::get('subteam', [MovieController::class, 'getSubTeamMovies']);
    Route::get('tv-shows', [MovieController::class, 'getTVShowMovies']);
    Route::get('phim-sap-chieu', [MovieController::class, 'getUpcomingMovies']);
    
    Route::get('the-loai/{category}', [MovieController::class, 'getMoviesByCategory']);
    Route::get('quoc-gia/{country}', [MovieController::class, 'getMoviesByCountry']);
    Route::get('xu-huong', [MovieController::class, 'getTrendingMovies']);
    Route::get('moi-cap-nhat', [MovieController::class, 'getNewUpdatedMovies']);
    Route::get('moi-cap-nhat/phim-bo', [MovieController::class, 'getNewUpdatedSeriesMovies']);
    Route::get('moi-cap-nhat/phim-le', [MovieController::class, 'getNewUpdatedSingleMovies']);
    Route::get('pho-bien', [MovieController::class, 'getPopularMovies']);
    Route::get('hom-nay-xem-gi', [MovieController::class, 'getMoviesAirToday']);
    Route::get('luot-xem-cao-nhat', [MovieController::class, 'getHighestViewMovie']);
// });

use App\Http\Controllers\User\BlogController;
Route::prefix('blogs')->group(function () {
    Route::get('', [BlogController::class, 'getBlogs']);
});

use App\Http\Controllers\User\ScheduledTasksController;
Route::get('run-scheduled-tasks', [ScheduledTasksController::class, 'runScheduledTasks']);

