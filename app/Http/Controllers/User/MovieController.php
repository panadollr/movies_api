<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Models\Movie;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationResource;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;

class MovieController
{

    protected $selectedColumns = ['movies.*', 'movie_details.content',
    'movie_details.type', 'movie_details.status', 'movie_details.sub_docquyen', 'movie_details.time',
    'movie_details.episode_current', 'movie_details.quality', 'movie_details.lang',
    'movie_details.showtimes', 'movie_details.category', 'movie_details.country']; 
    public $selectedColumnsV2 = ['movies._id', 'movies.name', 'movies.thumb_url', 'movies.slug', 'movies.year',
    'movie_details.type', 'movie_details.episode_current', 'movie_details.category'];
    protected $moviesWithMovieDetailsQuery; 
    public $moviesWithNoTrailer;
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
        $this->moviesWithNoTrailer = $this->moviesWithMovieDetailsQuery->where('movie_details.status', '!=', 'trailer')
            ->where('movie_details.episode_current', '!=', 'Trailer');
    }

    protected function generateSeoData($title, $description, $year)
    {
        $year = $year ? "năm " . $year : "";
        $yearPlaceholder = '$year';

        $title = str_replace([$yearPlaceholder, '  '], [$year, ' '], $title);
        $title = str_replace(' ,', ',', $title);
        $title = str_replace(',', ' |', $title);
        $description = str_replace([$yearPlaceholder, '  '], [$year, ' '], $description);
        $description = str_replace('  ', ' ', $description);

        $seoTitle = trim($title);
        $seoDescription = trim($description);

        return [
            'seo_title' =>  $seoTitle,
            'seo_description' => $seoDescription, 
            'og_url' => request()->path(),
        ];
    }


    //LỌC PHIM
    public function getMoviesByFilter($query, $default_limit, $title, $description){
        $limit = request()->input('limit', $default_limit);
        $year = request()->input('year');
        try {
            //theo năm
            if ($year) {
                $query->where('movies.year', '=', $year);
            }

            $columns = ['movies._id', 'name', 'slug', 'thumb_url', 'year', 'episode_current', 'category'];
            $result = ($limit === 'all') ? $query->select($columns)->get() : $query->paginate($limit);
            $data = MovieResource::collection($result);
        
            $responseData = [
                'data' => $data,
                'seoOnPage' => $this->generateSeoData($title, $description, $year),
                'count' => $data->count()
            ];
        
            $response = ($limit === 'all') ? $responseData : new PaginationResource($responseData);

            return response()->json($response, 200); 
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);   
        }
    }

    //LẤY TÊN THỂ LOẠI
    protected function getCategoryNameBySlug($slug){
        $categories = config('api_settings.categories');
        return $categories[$slug] ?? null;
    }

    //PHIM THEO THỂ LOẠI
    public function getMoviesByCategory($category){
        $categoryName = $this->getCategoryNameBySlug($category);
        $title = "Phim Mới $categoryName \$year, Phim hay, Xem phim nhanh, Xem phim online, Phim mới $categoryName \$year vietsub hay nhất";
        $description = "Xem phim mới $categoryName \$year miễn phí nhanh chất lượng cao." .
        " Xem phim $categoryName \$year online Việt Sub, Thuyết minh, lồng tiếng chất lượng HD." .
        " Xem phim nhanh online chất lượng cao"; 
        $moviesByCategory = $this->moviesWithNoTrailer->whereJsonContains('movie_details.category', ['slug' => $category])
        ->select($this->selectedColumnsV2);
        return $this->getMoviesByFilter($moviesByCategory, 24, $title, $description);
    } 

    //LẤY TÊN QUỐC GIA
    protected function getCountryNameBySlug($slug){
        $countries = config('api_settings.countries');
        return $countries[$slug] ?? null;
    }

    //PHIM THEO QUỐC GIA
    public function getMoviesByCountry($slug){
        $countryName = $this->getCountryNameBySlug($slug);
        $title = "Phim Mới $countryName \$year, Phim hay, Xem phim nhanh, Xem phim online, Phim mới $countryName \$year vietsub hay nhất";
        $description = "Xem phim mới $countryName \$year miễn phí nhanh chất lượng cao." .
        "Xem phim $countryName \$year online Việt Sub, Thuyết minh, lồng tiếng chất lượng HD." .
        "Xem phim nhanh online chất lượng cao";
        $moviesByCountry = $this->moviesWithNoTrailer->whereJsonContains('movie_details.country', ['slug' => $slug])
        ->select($this->selectedColumnsV2);
        return $this->getMoviesByFilter($moviesByCountry, 24, $title, $description);
    } 
    
    //PHIM THEO LOẠI
    public function getMoviesByType($type, $title, $description){
        $movies = $this->moviesWithNoTrailer->where('movie_details.type', $type)->select($this->selectedColumnsV2);
        return $this->getMoviesByFilter($movies, 24, $title, $description);
    }

    //PHIM BỘ
    public function getSeriesMovies(){
        $title = "Phim bộ \$year, Phim bộ \$year hay tuyển chọn, Phim bộ mới nhất \$year";
        $description = "Phim bộ mới nhất tuyển chọn chất lượng cao, phim bộ mới nhất \$year vietsub cập nhật nhanh nhất. Phim bộ \$year vietsub nhanh nhất";
        return $this->getMoviesByType('series', $title, $description);
    }

    //PHIM LẺ
    public function getSingleMovies(){
        $title = "Phim lẻ \$year, Phim lẻ \$year hay tuyển chọn, Phim lẻ mới nhất \$year";
        $description = "Phim lẻ mới nhất tuyển chọn chất lượng cao, phim lẻ mới nhất \$year vietsub cập nhật nhanh nhất. Phim lẻ \$year vietsub nhanh nhất";
        return $this->getMoviesByType('single', $title, $description);
    }

    //PHIM HOẠT HÌNH
    public function getCartoonMovies(){
        $title = "Phim hoạt hình, Phim hoạt hình hay tuyển chọn, Phim hoạt hình mới nhất \$year";
        $description = "Phim hoạt hình mới nhất tuyển chọn chất lượng cao, Phim hoạt hình mới nhất \$year vietsub cập nhật nhanh nhất. Phim hoạt hình vietsub nhanh nhất";
        return $this->getMoviesByType('hoathinh', $title, $description);
    }

    //PHIM SUBTEAM
    public function getSubTeamMovies(){
        $title = 'Flashmov Subteam - Tuyển tập phim được dịch bởi Flashmov';
        $description = "Tổng hợp những bộ phim hot được vietsub trên Flashmov. Phim hay chọn lọc vietsub nhanh nhất \$year, cập nhật hàng ngày.";
        $subTeamMovies = $this->moviesWithNoTrailer->where('movie_details.sub_docquyen', true)
        ->select($this->selectedColumnsV2);
        return $this->getMoviesByFilter($subTeamMovies, 24, $title, $description);
    }

    //TV-SHOWS
    public function getTVShowMovies(){
        $title = "Tv Shows, Tv Shows hay tuyển chọn, Tv Shows mới nhất \$year";
        $description = "TV Shows mới nhất tuyển chọn chất lượng cao, TV Shows mới nhất \$year vietsub cập nhật nhanh nhất. TV Shows vietsub nhanh nhất.";
        return $this->getMoviesByType('tvshows', $title, $description);
    }

    //PHIM SẮP CHIẾU
    public function getUpcomingMovies(){
        $title = "Phim sắp chiếu \$year";
        $description = "$title mới nhất, tuyển chọn chất lượng cao, $title mới nhất, chọn lọc cập nhật nhanh nhất. $title phụ đề hay nhất.";
        $upcomingMovies = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')->select($this->selectedColumns)
        ->where('movie_details.episode_current', 'Trailer')
        ->where('movie_details.trailer_url', '!=', '')
        ->select($this->selectedColumnsV2);
        return $this->getMoviesByFilter($upcomingMovies, 24, $title, $description);
    }

    //PHIM ĐANG THỊNH HÀNH
    public function getTrendingMovies(Request $request){
        $title = "Phim đang thịnh hành";
        $description = "";
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
        return $this->getMoviesByFilter($topTrendingMovies, 24, $title, $description);
    }

    //PHIM MỚI CẬP NHẬT THEO LOẠI 
    protected function getNewUpdatedMoviesByType($type, $title, $description){
        $newUpdatedMoviesByType = $this->moviesWithNoTrailer
        ->whereBetween('modified_time', [$this->week, $this->tomorrow])
        ->orderByDesc('modified_time')
        ->where('type', $type)
        ->select($this->selectedColumnsV2);
        return $this->getMoviesByFilter($newUpdatedMoviesByType, 24, $title, $description);    
    }

    //PHIM BỘ MỚI CẬP NHẬT
    public function getNewUpdatedSeriesMovies(){
        $title = "Phim bộ mới cập nhật";
        $description = "";
        return $this->getNewUpdatedMoviesByType('series', $title, $description);
    }

    //PHIM LẺ MỚI CẬP NHẬT
    public function getNewUpdatedSingleMovies(){
        $title = "Phim lẻ mới cập nhật";
        $description = "";
        return $this->getNewUpdatedMoviesByType('single', $title, $description);
    }

    //HÔM NAY XEM GÌ
    public function getMoviesAirToday(){
        $title = 'Hôm nay xem gì';
        $description = "";
        $moviesAirToday = $this->moviesWithNoTrailer
        ->whereBetween('modified_time', [$this->week, $this->tomorrow])->orderBy('movie_details.view')
        ->select($this->selectedColumnsV2);
        return $this->getMoviesByFilter($moviesAirToday, 10, $title, $description);
    }

    //TÌM KIẾM PHIM
    public function searchMovie(Request $request){
        $name = $request->keyword;
        $title = "Phim $name | $name vietsub | Phim $name hay | Tuyển tập $name mới nhất \$year";
        $description = "Phim $name hay tuyển tập, phim $name mới nhất, tổng hợp phim $name, $name full HD, $name vietsub, xem $name online";
        $keywords = explode(' ', $name);
        
    //     $searchedMovies = $this->moviesWithNoTrailer
    // ->where(function ($query) use ($name) {
    //     $query->where('name', 'like', "%{$name}%")
    //           ->orWhere('origin_name', 'like', "%{$name}%")
    //           ->orWhere('content', 'like', "%{$name}%");
    // });

    $searchedMovies = $this->moviesWithNoTrailer
        ->where('name', $name)
        ->orWhere('name', 'like', "%$name%");

        return $this->getMoviesByFilter($searchedMovies, 24, $title, $description);
    } 
    
    public function getPoster($slug)
    {
        try {
            $width = request()->input('w', 280);
            $imageName = $slug . '-thumb.jpg';
            $imageUrl = config('api_settings.image_domain') . $imageName;
            $client = new Client();
            $response = $client->get($imageUrl);
            
            $imageContents = $response->getBody()->getContents();

            // Resize the image and convert it to WebP format with reduced quality
        $image = Image::make($imageContents)->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $webpImage = $image->encode('webp', 70);
    
            return response()->stream(
                function () use ($webpImage) {
                    echo $webpImage;
                },
                200,
                ['Content-Type' => 'image/webp']
            );
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Không tìm thấy ảnh'], 404);
        }
    }

}
