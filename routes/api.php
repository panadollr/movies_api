<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//ADMIN
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\EpisodeController as AdminEpisodeController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

if(App::environment('production')){
    URL::forceScheme('https');
}

Route::prefix('admin')->group(function () {
    Route::get('general-infomation', [DashboardController::class, 'generalInformation']);

    Route::prefix('movies')->group(function () {
        Route::get('top', [DashboardController::class, 'topMovies']);
    });

    Route::prefix('episodes')->group(function () {
        Route::get('',[AdminEpisodeController::class, 'getEpisodes'])->name('episodes.index');
        Route::get('edit/{slug}',[AdminEpisodeController::class, 'episodeDetail']);
        Route::post('update/{_id}',[AdminEpisodeController::class, 'updateEpisodes']);
    });

    Route::prefix('blogs')->group(function () {
        Route::get('add-index', function(){
            return view('admin.blog.add_blog');
        });
        Route::post('create', [AdminBlogController::class, 'createBlog']);
        Route::get('edit/{slug}', [AdminBlogController::class, 'getBlogDetail']);
        Route::post('update/{slug}', [AdminBlogController::class, 'updateBlog']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('top', [DashboardController::class, 'topCategories']);
        });
});

use App\Http\Controllers\Admin\ImageController;
    // Route::get('image/{slug}-thumb.webp', [ImageController::class, 'getThumb'])->where('slug', '[a-zA-Z0-9\-]+');
    Route::get('image/{slug}-poster', [ImageController::class, 'getPoster'])->where('slug', '[a-zA-Z0-9\-]+');

use App\Http\Controllers\Admin\ScheduledTasksController;
    Route::get('commands/crawl:movies', [ScheduledTasksController::class, 'runCrawlMoviesCommand']);
    Route::get('commands/crawl:movie_details', [ScheduledTasksController::class, 'runCrawlMovieDetailsCommand']);
    // Route::get('commands/run-scheduled-commands', [ScheduledTasksController::class, 'runScheduledCommands']);
    // Route::get('commands/delete-old-movies', [ScheduledTasksController::class, 'deleteOldMovies']);



//USER
use App\Http\Controllers\User\MovieController;
    Route::get('phim-le', [MovieController::class, 'getSingleMovies']);
    Route::get('phim-bo', [MovieController::class, 'getSeriesMovies']);
    Route::get('hoat-hinh', [MovieController::class, 'getCartoonMovies']);
    Route::get('subteam', [MovieController::class, 'getSubTeamMovies']);
    Route::get('phim-sap-chieu', [MovieController::class, 'getUpcomingMovies']);
    
    Route::get('the-loai/{category}', [MovieController::class, 'getMoviesByCategory']);
    Route::get('quoc-gia/{country}', [MovieController::class, 'getMoviesByCountry']);
    Route::get('xu-huong', [MovieController::class, 'getTrendingMovies']);
    Route::get('moi-cap-nhat/phim-bo', [MovieController::class, 'getNewUpdatedSeriesMovies']);
    Route::get('moi-cap-nhat/phim-le', [MovieController::class, 'getNewUpdatedSingleMovies']);
    Route::get('hom-nay-xem-gi', [MovieController::class, 'getMoviesAirToday']);
    Route::get('tim-kiem', [MovieController::class, 'searchMovie']);
    Route::get('phim-18', [MovieController::class, 'get18sMovies']);
    Route::get('total-movies', [MovieController::class, 'getTotalMovies']);
    Route::middleware('cors2')->get('phim-le-2', [MovieController::class, 'getSingleMovies']);

use App\Http\Controllers\User\MovieDetailsController;
    Route::get('phim/{slug}/{episode_slug}', [MovieDetailsController::class, 'getMovieDetail']);
    Route::get('phim/{slug}', [MovieDetailsController::class, 'getMovieDetail']);
    Route::get('phim-tuong-tu/{slug}', [MovieDetailsController::class, 'getSimilarMovies']);

use App\Http\Controllers\User\BlogController;
    Route::get('tin-tuc', [BlogController::class, 'getBlogs']);
    Route::get('tin-tuc/{slug}', [BlogController::class, 'blogDetail']);
    Route::get('tin-tuc-tuong-tu/{slug}', [BlogController::class, 'similarBlogs']);


