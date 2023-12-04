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

    protected $selectArray = ['movies.*', 'movie_details.category',
    'movie_details.content', 'movie_details.type', 'movie_details.status',
    'movie_details.sub_docquyen', 'movie_details.time', 'movie_details.quality',
    'movie_details.lang', 'movie_details.showtimes']; 
    protected $today;
    protected $week;

    public function __construct()
    {
        $this->today = Carbon::now();
        $this->week = Carbon::now()->subDays(7);
    }


    public function getAllMovies(Request $request){
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 24;
        $movies = Movie::skip($offset)->paginate($limit);

        return $movies;
    }


    public function getTrendingMovies(Request $request){
        $time_window = $request->time_window ?? 'week';
        try {
            $query = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
            ->select($this->selectArray)->orderByDesc('view');

            if($time_window == "week"){
                $query->whereBetween('modified_time', [$this->week, $this->today]);
            }
            
            if($time_window == "day"){
                $query->whereDate("modified_time", $this->today);
            }

            $topTrendingMovies = $query->paginate(8);

            return response()->json(new PaginationResource(MovieResource::collection($topTrendingMovies)), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getNewUpdatedMovies(){
        try {
        $newUpdatedMovies = Movie::whereDate("modified_time", today())
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select($this->selectArray)
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
        $limit = $request->limit ?? 24;
        try {
        $newUpdatedSeriesMovies = Movie::orderByDesc('modified_time')
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select($this->selectArray)
        ->whereHas('movie_details', function ($query) {
                $query->where('type', 'series');
            })
        ->paginate($limit);
    
        return response()->json(new PaginationResource(MovieResource::collection($newUpdatedSeriesMovies)), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getNewUpdatedSingleMovies(Request $request){
        $limit = $request->limit ?? 24;
        try {
        $newUpdatedSingleMovies = Movie::orderByDesc('modified_time')
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select($this->selectArray)
        ->whereHas('movie_details', function ($query) {
                $query->where('type', 'single');
            })
        ->paginate($limit);

        return response()->json(new PaginationResource(MovieResource::collection($newUpdatedSingleMovies)), 200);  
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getPopularMovies(Request $request){
        $limit = $request->limit ?? 24;
        try {
            $popularMovies = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id')
            ->select($this->selectArray)
            ->paginate($limit);
          
            return response()->json(new PaginationResource(MovieResource::collection($popularMovies)), 200);  
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function getMoviesAirToday(){
        $limit = $request->limit ?? 24;
        try {
        $moviesAirToday = Movie::whereBetween('modified_time', [$this->week, $this->today])    
        ->join('movie_details', 'movie_details._id', 'movies._id')
        ->select($this->selectArray)
        ->orderBy('movie_details.view')
        ->paginate($limit);
          
        return response()->json(new PaginationResource(MovieResource::collection($moviesAirToday)), 200);  
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getHighestViewMovie(){
        try {
            $highestViewMovie = MovieDetails::orderByDesc('view')
            ->join('movies', 'movies._id', '=', 'movie_details._id')
            ->select($this->selectArray)
            ->first();
           
            return response()->json(new MovieResource($highestViewMovie), 200);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
    }


    public function filter(Request $request){
        $category = $request->category;
        $country = $request->country;
        $year = $request->year;
        try {
        $query = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
        ->select($this->selectArray);

        if ($category) {
            $query->where(function ($query) use ($category) {
                $query->whereJsonContains('movie_details.category', ['slug' => $category]);
            });
        }
    
        if ($country) {
            $query->where(function ($query) use ($country) {
                $query->whereJsonContains('movie_details.country', ['slug' => $country]);
            });
        }
    
        if ($year) {
            $query->where('movies.year', $year);
        }
    
        $result = $query->paginate(24);
        return response()->json(new PaginationResource(MovieResource::collection($result)), 200); 
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
    }
    
   
}
