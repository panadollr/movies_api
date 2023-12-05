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

    protected $selectedColumns = ['movies.*', 'movie_details.content',
    'movie_details.type', 'movie_details.status', 'movie_details.sub_docquyen', 'movie_details.time',
    'movie_details.episode_current', 'movie_details.quality', 'movie_details.lang',
    'movie_details.showtimes', 'movie_details.category', 'movie_details.country']; 
    protected $movies_with_movie_details_query; 
    protected $today;
    protected $week;

    public function __construct()
    {
        $this->today = Carbon::now();
        $this->week = Carbon::now()->subDays(7);
        $this->movies_with_movie_details_query = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
        ->select($this->selectedColumns);
    }

    protected function getMoviesByFilter(Request $request, $query){
        $limit = $request->limit ?? 24;
        $category = $request->category;
        $country = $request->country;
        $year = $request->year;
        try {

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
            $query->where('movies.year', '=', $year);
        }
    
        $result = $query->paginate($limit);
        return response()->json(new PaginationResource(MovieResource::collection($result)), 200); 
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
    }


    public function getNewestMovies(Request $request){
        $movies = $this->movies_with_movie_details_query->orderByDesc('movies.modified_time');
       return $this->getMoviesByFilter($request, $movies);
    }


    protected function getMoviesByType(Request $request, $type)
    {
    $movies = $this->movies_with_movie_details_query->where('movie_details.type', $type);
    return $this->getMoviesByFilter($request, $movies);
    }


    public function getSeriesMovies(Request $request){
        return $this->getMoviesByType($request, 'series');
    }
    

    public function getSingleMovies(Request $request){
        return $this->getMoviesByType($request, 'single');
    }


    public function getCartoonMovies(Request $request){
        return $this->getMoviesByType($request, 'hoathinh');
    }


    public function getSubTeamMovies(Request $request){
        $subTeamMovies = $this->movies_with_movie_details_query->where('movie_details.sub_docquyen', true);
        return $this->getMoviesByFilter($request, $subTeamMovies);
    }


    public function getTVShowMovies(Request $request){
        return $this->getMoviesByType($request, 'tvshows');
    }


    public function getUpcomingMovies(Request $request){
        $upcomingMovies = $this->movies_with_movie_details_query->where('movie_details.status', 'trailer');
        return $this->getMoviesByFilter($request, $upcomingMovies);
    }


    public function getTrendingMovies(Request $request){
        $time_window = $request->time_window ?? 'week';
        try {
            $query = $this->movies_with_movie_details_query->orderByDesc('view');

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
        $newUpdatedMovies = $this->movies_with_movie_details_query
        ->whereDate("modified_time", today())
        ->get();
        if(is_null($newUpdatedMovies)){
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json(MovieResource::collection($newUpdatedMovies), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getNewUpdatedSeriesMovies(Request $request){
        $limit = $request->limit ?? 24;
        try {
        $newUpdatedSeriesMovies = $this->movies_with_movie_details_query
        ->orderByDesc('modified_time')
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
        $newUpdatedSingleMovies = $this->movies_with_movie_details_query
        ->orderByDesc('modified_time')
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
            $popularMovies = $this->movies_with_movie_details_query
            ->paginate($limit);
          
            return response()->json(new PaginationResource(MovieResource::collection($popularMovies)), 200);  
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function getMoviesAirToday(Request $request){
        $limit = $request->limit ?? 24;
        try {
        $moviesAirToday = $this->movies_with_movie_details_query
        ->whereBetween('modified_time', [$this->week, $this->today])    
        ->orderBy('movie_details.view')
        ->paginate($limit);
          
        return response()->json(new PaginationResource(MovieResource::collection($moviesAirToday)), 200);  
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function getHighestViewMovie(){
        try {
            $highestViewMovie = $this->movies_with_movie_details_query
            ->orderByDesc('view')->first();
           
            return response()->json(new MovieResource($highestViewMovie), 200);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
    }
    
   
}
