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
    protected $moviesWithMovieDetailsQuery; 
    protected $moviesWithNoTrailer;
    protected $today;
    protected $yesterday;
    protected $tomorrow;
    protected $week;

    public function __construct()
    {
        $this->yesterday = Carbon::yesterday();
        $this->today = Carbon::now();
        $this->tomorrow = Carbon::tomorrow();
        $this->week = Carbon::now()->subDays(7);
        $this->moviesWithMovieDetailsQuery = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
                                            ->select($this->selectedColumns);
        $this->moviesWithNoTrailer = $this->moviesWithMovieDetailsQuery->where('movie_details.status', '!=', 'trailer');

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


    //PHIM THEO THỂ LOẠI
    public function getMoviesByCategory(Request $request, $category){
        $moviesByCategory = $this->moviesWithNoTrailer->whereJsonContains('movie_details.category', ['slug' => $category]);
        return $this->getMoviesByFilter($request, $moviesByCategory);
    } 


    //PHIM THEO QUỐC GIA
    public function getMoviesByCountry(Request $request, $country){
        $moviesByCountry = $this->moviesWithNoTrailer->whereJsonContains('movie_details.country', ['slug' => $country]);
        return $this->getMoviesByFilter($request, $moviesByCountry);
    } 

    
    //PHIM THEO LOẠI
    public function getMoviesByType(Request $request, $type){
        $movies = $this->moviesWithNoTrailer->where('movie_details.type', $type);
        return $this->getMoviesByFilter($request, $movies);
    }


    //PHIM MỚI CẬP NHẬT THEO LOẠI 
    protected function getNewUpdatedMoviesByType($request, $type){
        $newUpdatedMoviesByType = $this->moviesWithNoTrailer
        ->whereBetween('modified_time', [$this->yesterday, $this->tomorrow])
        ->whereHas('movie_details', function ($query) use($type) {
                $query->where('type', $type);
            });
        return $this->getMoviesByFilter($request, $newUpdatedMoviesByType);    
    }

    //-----//


    // //PHIM MỚI
    // public function getNewestMovies(Request $request){
    //     $movies = $this->movies_with_movie_details_query->orderByDesc('movies.modified_time');
    //    return $this->getMoviesByFilter($request, $movies);
    // }

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
        $subTeamMovies = $this->moviesWithNoTrailer->where('movie_details.sub_docquyen', true);
        return $this->getMoviesByFilter($request, $subTeamMovies);
    }


    //TV-SHOWS
    public function getTVShowMovies(Request $request){
        return $this->getMoviesByType($request, 'tvshows');
    }


    //PHIM SẮP CHIẾU
    public function getUpcomingMovies(Request $request){
        $upcomingMovies = $this->moviesWithMovieDetailsQuery->where('movie_details.status', 'trailer');
        return $this->getMoviesByFilter($request, $upcomingMovies);
    }


    //PHIM ĐANG THỊNH HÀNH
    public function getTrendingMovies(Request $request){
        $time_window = $request->time_window ?? 'week';
        $query = $this->moviesWithNoTrailer
        ->orderByDesc('view');

        if($time_window == "week"){
                $query->whereBetween('modified_time', [$this->week, $this->tomorrow]);
        }
            
        if($time_window == "day"){
                $query->whereBetween('modified_time', [$this->today, $this->tomorrow]);
        }

        $topTrendingMovies = $query;
        return $this->getMoviesByFilter($request, $topTrendingMovies);
    }


    // //PHIM MỚI CẬP NHẬT
    // public function getNewUpdatedMovies(Request $request){
    //     $newUpdatedMovies = $this->moviesWithNoTrailer
    //     ->whereDate("modified_time", today());
    //     return $this->getMoviesByFilter($request, $newUpdatedMovies);
    // }

    
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
        $moviesAirToday = $this->moviesWithNoTrailer
        ->whereBetween('modified_time', [$this->week, $this->tomorrow])    
        ->orderBy('movie_details.view');
        return $this->getMoviesByFilter($request, $moviesAirToday);
    }


    //PHIM CÓ LƯỢT XEM CAO NHẤT
    public function getHighestViewMovie(){
        try {
            $highestViewMovie = $this->moviesWithNoTrailer
            ->orderByDesc('view')->first();
           
            return response()->json(new MovieResource($highestViewMovie), 200);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
    }

    //TÌM KIẾM PHIM
    public function searchMovie(Request $request){
        $name = $request->keyword;
        $searchedMovies = $this->moviesWithNoTrailer
                        ->where('movies.name', 'LIKE', '%' . $name . '%');
        return $this->getMoviesByFilter($request, $searchedMovies);
    }
    
   
}
