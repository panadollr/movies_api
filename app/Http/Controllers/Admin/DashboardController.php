<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Carbon\Carbon;

class DashboardController
{
    
    public function __construct()
    {
    
    }
    

    public function generalInformation()
{
    try {
    $cacheKey = 'movies_info';
    $data = cache()->remember($cacheKey, 120, function () {
        $totalMovies = Movie::count();
        $totalNewestMovies = Movie::whereDate("modified_time", [now()])->count();

        return [
            ['title' => "Tổng số phim", 'total_count' => $totalMovies],
            ['title' => "Tổng số phim mới cập nhật", 'total_count' => $totalNewestMovies],
        ];
    });
    if(!$data){
        return response()->json(['error' => 'Không có dữ liệu !'], 404);
    }
    return response()->json($data, 200);
} catch (\Throwable $th) {
    //throw $th;
    return response()->json($th->getMessage(), 500);
}
}


public function topCategories(Request $request){
        $time_type = $request->time_type;
        $category_slugs = ['hanh-dong', 'co-trang', 'chien-tranh', 'vien-tuong', 'kinh-di', 'tai-lieu', 'bi-an', 'phim-18', 'tinh-cam', 'tam-ly',
     'the-thao', 'phieu-luu', 'am-nhac', 'gia-dinh', 'hoc-duong', 'hai-huoc', 'vo-thuat', 'khoa-hoc', 'than-thoai', ' chinh-kich', 'kinh-dien']; 

    $movies = Movie::select('movies.modified_time', 'movie_details.view', 'movie_details.category')
    ->join('movie_details', 'movie_details._id', '=', 'movies._id')
    ->when($time_type == 'day', function ($query) {
        return $query->whereDate('movies.modified_time', Carbon::now()->toDateString());
    })
    ->when($time_type == 'week', function ($query) {
        return $query->whereBetween('movies.modified_time', [
            Carbon::now()->subDays(7),
            Carbon::now()
        ]);
    })
    ->when($time_type == 'month', function ($query) {
        return $query->whereBetween('movies.modified_time', [
            Carbon::now()->subDays(30),
            Carbon::now()
        ]);
    })
    ->get();

    $top_categories = array_fill_keys($category_slugs, ['total_views' => 0]);

    foreach ($category_slugs as $category_slug) {
    foreach ($movies as $movie) {
        $categories = json_decode($movie->category);
        if (is_array($categories)) {
            foreach ($categories as $category) {
                if ($category_slug == $category->slug) {
                    if (isset($top_categories[$category_slug])) {
                        $top_categories[$category_slug]["total_views"] += $movie->view;
                    }
                }
            }
        }
    }
}

$totalViews = array_column($top_categories, 'total_views');
$categoryKeys = array_keys($top_categories);
array_multisort($totalViews, SORT_DESC, $categoryKeys);
$top_categories = array_replace(array_flip($categoryKeys), $top_categories);
$top_categories = array_slice($top_categories, 0, 10, true);

        $data =  [
            'time_type' => $time_type,
            'top_categories' => $top_categories,
        ];

    return response()->json($data, 200);
    }


public function topMovies(){
    $top_movies_by_day = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
    ->select('movies.name','movie_details.view', 'movies.modified_time')->whereDate('movies.modified_time', now())->get()
    ->sortByDesc('view')->take(10)->values();

    $top_movies_by_week = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
    ->select('movies.name','movie_details.view', 'movies.modified_time')->whereBetween('movies.modified_time', [now()->subDays(7), now()])->get()
    ->sortByDesc('view')->take(10)->values();

    $top_movies_by_month = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
    ->select('movies.name','movie_details.view', 'movies.modified_time')->whereBetween('movies.modified_time', [now()->subDays(30), now()])->get()
    ->sortByDesc('view')->take(10)->values();

    $data =  [
        'top_movies_by_day' => $top_movies_by_day,
        'top_movies_by_week' => $top_movies_by_week,
        'top_movies_by_month' => $top_movies_by_month,
    ];

return response()->json($data, 200);
}


public function add_new(Request $request){
    
}

   
}
