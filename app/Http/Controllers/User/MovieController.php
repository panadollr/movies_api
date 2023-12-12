<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\SeoResource;

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
    protected $currentYear;
    protected $movieDetailsController;

    public function __construct()
    {
        $this->initializeDates();
        $this->initializeQueries();
    }

    protected function initializeDates()
    {
        $this->yesterday = Carbon::yesterday();
        $this->today = Carbon::now();
        $this->tomorrow = Carbon::tomorrow();
        $this->week = Carbon::now()->subDays(7);
        $this->currentYear = Carbon::now()->year;
    }

    protected function initializeQueries()
    {
        $this->moviesWithMovieDetailsQuery = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')->select($this->selectedColumns)
            ->orderByDesc('movies.year');
        // $this->moviesWithMovieDetailsQuery = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')->orderByDesc('movies.year');
        $this->moviesWithNoTrailer = $this->moviesWithMovieDetailsQuery->where('movie_details.status', '!=', 'trailer')
            ->where('movie_details.episode_current', '!=', 'Trailer');
    }

    protected function generateSeoData($title)
    {
        $seoTitle = "$title | $title hay tuyển chọn | $title mới nhất $this->currentYear";
        $seoDescription = "$title mới nhất tuyển chọn chất lượng cao, $title mới nhất $this->currentYear vietsub cập nhật nhanh nhất. $title vietsub nhanh nhất";

        return [
            'seo_title' =>  $seoTitle,
            'seo_description' => $seoDescription, 
            'og_image' => '',
            'og_url' => request()->path(),
        ];
    }


    //LỌC PHIM
    public function getMoviesByFilter($query, $default_limit, $title){
        $limit = request()->input('limit', $default_limit);
        $year = request()->input('year');
        try {
            
        //theo năm
        if ($year) {
            $query->where('movies.year', '=', $year);
        }

        $result = $query->paginate($limit);
        $seoOnPage = $this->generateSeoData($title);
        
        $data = [
            'data' => MovieResource::collection($result),
            'seoOnPage' => $seoOnPage
         ];
         return response()->json(new PaginationResource($data), 200); 

        // return response()->json(new PaginationResource(MovieResource::collection($result)), 200); 
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
    }


    //PHIM THEO THỂ LOẠI
    public function getMoviesByCategory($category){
        $moviesByCategory = $this->moviesWithNoTrailer->whereJsonContains('movie_details.category', ['slug' => $category]);
        return $this->getMoviesByFilter($moviesByCategory, 24);
    } 


    //PHIM THEO QUỐC GIA
    public function getMoviesByCountry($country){
        $moviesByCountry = $this->moviesWithNoTrailer->whereJsonContains('movie_details.country', ['slug' => $country]);
        return $this->getMoviesByFilter($moviesByCountry, 24);
    } 

    
    //PHIM THEO LOẠI
    public function getMoviesByType($type, $title){
        $movies = $this->moviesWithNoTrailer->where('movie_details.type', $type); 
        return $this->getMoviesByFilter($movies, 24, $title);
    }


    //PHIM BỘ
    public function getSeriesMovies(){
        $title = 'Phim bộ';
        return $this->getMoviesByType('series', $title);
    }

    
    //PHIM LẺ
    public function getSingleMovies(){
        $title = 'Phim lẻ';
        return $this->getMoviesByType('single', $title);
    }


    //PHIM HOẠT HÌNH
    public function getCartoonMovies(){
        $title = 'Hoạt hình';
        return $this->getMoviesByType('hoathinh', $title);
    }


    //PHIM SUBTEAM
    public function getSubTeamMovies(){
        $title = 'Phim Subteam';
        $subTeamMovies = $this->moviesWithNoTrailer->where('movie_details.sub_docquyen', true);
        return $this->getMoviesByFilter($subTeamMovies, 24, $title);
    }


    //TV-SHOWS
    public function getTVShowMovies(){
        $title = 'TV Shows';
        return $this->getMoviesByType('tvshows', $title);
    }


    //PHIM SẮP CHIẾU
    public function getUpcomingMovies(){
        $title = "Phim sắp chiếu";
        $upcomingMovies = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')->select($this->selectedColumns)
        ->where('movie_details.status', 'trailer')->where('movie_details.episode_current', 'Trailer');
        return $this->getMoviesByFilter($upcomingMovies, 24, $title);
    }


    //PHIM ĐANG THỊNH HÀNH
    public function getTrendingMovies(Request $request){
        $title = "Phim đang thịnh hành";
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
        return $this->getMoviesByFilter($topTrendingMovies, 24, $title);
    }


    //PHIM MỚI CẬP NHẬT THEO LOẠI 
    protected function getNewUpdatedMoviesByType($type, $title){
        // $selectedColumns = ['movies.*','movie_details.type','movie_details.episode_current']; 

        $newUpdatedMoviesByType = $this->moviesWithNoTrailer
        ->whereBetween('modified_time', [$this->week, $this->tomorrow])
        ->orderByDesc('modified_time')
        ->whereHas('movie_details', function ($query) use($type) {
                $query->where('type', $type);
            })
        ;
        return $this->getMoviesByFilter($newUpdatedMoviesByType, 24, $title);    
    }

    //PHIM BỘ MỚI CẬP NHẬT
    public function getNewUpdatedSeriesMovies(){
        $title = "Phim bộ mới cập nhật";
        return $this->getNewUpdatedMoviesByType('series', $title);
    }

    //PHIM LẺ MỚI CẬP NHẬT
    public function getNewUpdatedSingleMovies(){
        $title = "Phim lẻ mới cập nhật";
        return $this->getNewUpdatedMoviesByType('single', $title);
    }


    //HÔM NAY XEM GÌ
    public function getMoviesAirToday(){
        $title = 'Hôm nay xem gì';
        $moviesAirToday = $this->moviesWithNoTrailer
        ->whereBetween('modified_time', [$this->week, $this->tomorrow])->orderBy('movie_details.view');
        return $this->getMoviesByFilter($moviesAirToday, 10, $title);
    }


    //TÌM KIẾM PHIM
    public function searchMovie(Request $request){
        $name = $request->keyword;
        $title = "Phim có từ khóa $name";
        $searchedMovies = $this->moviesWithNoTrailer
        ->where('name', 'like', "%$name%");
        return $this->getMoviesByFilter($searchedMovies, 24, $title);
    }  
   
}
