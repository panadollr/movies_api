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


    //LỌC PHIM
    protected function getMoviesByFilter(Request $request, $query){
        $limit = $request->limit ?? 24;
        $year = $request->year;
        try {
            
        //theo năm
        if ($year) {
            $query->where('movies.year', '=', $year);
        }

        $result = $query->paginate($limit);
        return response()->json(new PaginationResource(MovieResource::collection($result)), 200); 
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
    }

    //PHIM MỚI
    public function getNewestMovies(Request $request){
        $movies = $this->movies_with_movie_details_query->orderByDesc('movies.modified_time');
       return $this->getMoviesByFilter($request, $movies);
    }


    //PHIM THEO THỂ LOẠI
    public function getMoviesByCategory(Request $request, $category){
        $moviesByCategory = $this->movies_with_movie_details_query
        ->whereJsonContains('movie_details.category', ['slug' => $category]);
        return $this->getMoviesByFilter($request, $moviesByCategory);
    } 


    //PHIM THEO QUỐC GIA
    public function getMoviesByCountry(Request $request, $country){
        $moviesByCountry = $this->movies_with_movie_details_query
            ->whereJsonContains('movie_details.country', ['slug' => $country])
            ->paginate($limit);
        return $this->getMoviesByFilter($request, $moviesByCountry);
    } 


    protected function getMoviesByType(Request $request, $type)
    {
    $movies = $this->movies_with_movie_details_query->where('movie_details.type', $type);
    return $this->getMoviesByFilter($request, $movies);
    }


    //PHIM BỘ
    public function getSeriesMovies(Request $request){
        return $this->getMoviesByType($request, 'series');
    }

    
    //PHIM LẺ
    public function getSingleMovies(Request $request){
        return $this->getMoviesByType($request, 'single');
    }


    //PHIM HOẠT HÌNH
    public function getCartoonMovies(Request $request){
        return $this->getMoviesByType($request, 'hoathinh');
    }


    //PHIM SUBTEAM
    public function getSubTeamMovies(Request $request){
        $subTeamMovies = $this->movies_with_movie_details_query->where('movie_details.sub_docquyen', true);
        return $this->getMoviesByFilter($request, $subTeamMovies);
    }


    //TV-SHOWS
    public function getTVShowMovies(Request $request){
        return $this->getMoviesByType($request, 'tvshows');
    }


    //PHIM SẮP CHIẾU
    public function getUpcomingMovies(Request $request){
        $upcomingMovies = $this->movies_with_movie_details_query->where('movie_details.status', 'trailer');
        return $this->getMoviesByFilter($request, $upcomingMovies);
    }


    //PHIM ĐANG THỊNH HÀNH
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


    //PHIM MỚI CẬP NHẬT
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


    protected function getNewUpdatedMoviesByType($request, $type){
        $limit = $request->limit ?? 24;
        try {
        $newUpdatedMoviesByType = $this->movies_with_movie_details_query
        ->orderByDesc('modified_time')
        ->whereHas('movie_details', function ($query) use($type) {
                $query->where('type', $type);
            })
        ->paginate($limit);
    
        return response()->json(new PaginationResource(MovieResource::collection($newUpdatedMoviesByType)), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    //PHIM BỘ MỚI CẬP NHẬT
    public function getNewUpdatedSeriesMovies(Request $request){
        return $this->getNewUpdatedMoviesByType($request, 'series');
    }


    //PHIM LẺ MỚI CẬP NHẬT
    public function getNewUpdatedSingleMovies(Request $request){
        return $this->getNewUpdatedMoviesByType($request, 'single');
    }


    //HÔM NAY XEM GÌ
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


    //PHIM CÓ LƯỢT XEM CAO NHẤT
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
