<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Http\Resources\MovieResource;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class MovieController
{

    // protected $moviesJsonData;
    
    // public function __construct()
    // {
    //     $jsonFilePath = storage_path('movies.json');

    //     if (file_exists($jsonFilePath)) {
    //         $jsonContent = file_get_contents($jsonFilePath);
        
    //         // Decode JSON content into an associative array
    //         $this->moviesJsonData = json_decode($jsonContent, true);
        
    //         // Check if decoding was successful
    //         if ($this->moviesJsonData !== null) {
    //             // $moviesArray now contains the data from the JSON file
    //             return response()->json($this->moviesJsonData);
    //         } else {
    //             return response()->json(['error' => 'Error decoding JSON file']);
    //         }
    //     } else {
    //         return response()->json(['error' => 'JSON file not found']);
    //     }
    // }

    
    // public function addMoviesToJson()
    // {
    //     $movies = Movie::all();
    //     $jsonArray = [];
    //     foreach ($movies as $value) {
    //         $movieData = [
    //             'modified_time' => trim($value->modified_time, "'"),
    //             '_id' => trim($value->_id, "'"),
    //             'name' => trim($value->name, "'"),
    //             'origin_name' => trim($value->origin_name, "'"),
    //             'thumb_url' => trim($value->thumb_url, "'"),
    //             'slug' => trim($value->slug, "'"),
    //             'year' => trim($value->year, "'"),
    //             'poster_url' => trim($value->poster_url, "'"),
    //         ];

    //         $jsonArray[] = $movieData;
    //     }
    //     $jsonContent = json_encode($jsonArray, JSON_PRETTY_PRINT);
    //     $jsonFilePath = storage_path('movies.json');
    //     file_put_contents($jsonFilePath, $jsonContent);

    //     return response()->json(['success' => 'JSON file created successfully']);
    // }


    public function getAllMovies(Request $request){
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 24;
        $movies = Movie::skip($offset)->paginate($limit);

        return $movies;
    }

    public function getNewUpdatedMovies(){
        try {
        $newUpdatedMovies = Movie::whereDate("modified_time", today())
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select('movies.modified_time', 'movies._id', 'movies.name', 'movies.origin_name', 'movies.thumb_url',
        'movies.slug', 'movies.year', 'movies.poster_url', 'movie_details.category',
        'movie_details.content', 'movie_details.type', 'movie_details.status',
        'movie_details.sub_docquyen', 'movie_details.time', 'movie_details.quality',
        'movie_details.lang', 'movie_details.showtimes')
        ->get();
        if(is_null($newUpdatedMovies)){
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json(MovieResource::collection($newUpdatedMovies), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getTrendingMovies(){
        try {
            $topTrendingMovies = Movie::whereBetween('modified_time', [Carbon::now()->subDays(10), Carbon::now()])
    ->orderByDesc('view')->join('movie_details', 'movie_details._id', '=', 'movies._id')
    ->select('movies.modified_time', 'movies._id', 'movies.name', 'movies.origin_name', 'movies.thumb_url',
    'movies.slug', 'movies.year', 'movies.poster_url', 'movie_details.category',
    'movie_details.content', 'movie_details.type', 'movie_details.status',
    'movie_details.sub_docquyen', 'movie_details.time', 'movie_details.quality',
    'movie_details.lang', 'movie_details.showtimes')
    ->take(8)
    ->get();
            if(is_null($topTrendingMovies)){
            return response()->json(['error' => 'Not found'], 404);
            }
            return response()->json(MovieResource::collection($topTrendingMovies), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getPopularMovies(){
        try {
            $popularMovies = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id')
            ->select('movies.*', 'movie_details.category','movie_details.content', 'movie_details.type', 
            'movie_details.status','movie_details.sub_docquyen', 'movie_details.time', 
            'movie_details.quality','movie_details.lang', 'movie_details.showtimes')
            ->take(8)
            ->get();
          
            if(is_null($popularMovies)){
                return response()->json(['error' => 'Not found'], 404);
            }
            return response()->json(MovieResource::collection($popularMovies), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    

public function top_categories(Request $request){
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


public function top_movies(){
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
