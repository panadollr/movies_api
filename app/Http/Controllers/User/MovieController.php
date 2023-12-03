<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationResource;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class MovieController
{

    public function getAllMovies(Request $request){
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 24;
        $movies = Movie::skip($offset)->paginate($limit);

        return $movies;
    }

    public function getTrendingMovies(){
        try {
        $topTrendingMovies =  Movie::orderByDesc('view')
    ->whereBetween('modified_time', [Carbon::now()->subDays(10), Carbon::now()])
    ->orderByDesc('view')->join('movie_details', 'movie_details._id', '=', 'movies._id')
    ->select('movies.*', 'movie_details.category',
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


    public function getNewUpdatedSeriesMovies(){
        $limit = $request->limit ?? 5;
        try {
        $newUpdatedSeriesMovies = Movie::orderByDesc('modified_time')
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select('movies.*', 'movie_details.category',
        'movie_details.content', 'movie_details.type', 'movie_details.status',
        'movie_details.sub_docquyen', 'movie_details.time', 'movie_details.quality',
        'movie_details.lang', 'movie_details.showtimes')
        ->whereHas('movie_details', function ($query) {
                $query->where('type', 'series');
            })
        ->paginate($limit);
    
        return response()->json(new PaginationResource($newUpdatedSeriesMovies), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getNewUpdatedSingleMovies(Request $request){
        $limit = $request->limit ?? 5;
        try {
        $newUpdatedSingleMovies = Movie::orderByDesc('modified_time')
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select('movies.*', 'movie_details.category',
        'movie_details.content', 'movie_details.type', 'movie_details.status',
        'movie_details.sub_docquyen', 'movie_details.time', 'movie_details.quality',
        'movie_details.lang', 'movie_details.showtimes')
        ->whereHas('movie_details', function ($query) {
                $query->where('type', 'single');
            })
        ->paginate($limit);

        return response()->json(new PaginationResource($newUpdatedSingleMovies), 200);  
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getPopularMovies(){
        try {
            $popularMovies = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id')
            ->select('movies.*', 'movie_details.category',
        'movie_details.content', 'movie_details.type', 'movie_details.status',
        'movie_details.sub_docquyen', 'movie_details.time', 'movie_details.quality',
        'movie_details.lang', 'movie_details.showtimes')
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

    public function getMoviesToWatchToday(){
        $limit = $request->limit ?? 5;
        try {
        $moviesToWatchToday = Movie::whereBetween('modified_time', [Carbon::now()->subDays(7)->toDateString(), Carbon::now()->endOfDay()])    
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select('movies.*', 'movie_details.category',
        'movie_details.content', 'movie_details.type', 'movie_details.status',
        'movie_details.sub_docquyen', 'movie_details.time', 'movie_details.quality',
        'movie_details.lang', 'movie_details.showtimes')
        ->paginate($limit);
          
        return response()->json(new PaginationResource($moviesToWatchToday), 200);  
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getHighestViewMovie(){
        try {
        $cacheKey =  'highest_viedw_movie';
        $highestViewMovie = MovieDetails::orderByDesc('view')
        ->join('movies', 'movies._id', '=', 'movie_details._id')
        ->select([
        'movies._id',
        'movies.name',
        'movies.year',
        'movies.thumb_url',
        'movie_details.type',
        'movie_details.view',
        'movie_details.time',
        ])
        ->first();
       
        return response()->json(new MovieResource($highestViewMovie), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
   
}
