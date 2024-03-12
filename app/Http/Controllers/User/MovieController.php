<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Models\Movie;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MovieController
{

    protected $selectedColumns = ['movies.*', 'movie_details.content',
    'movie_details.type', 'movie_details.status', 'movie_details.sub_docquyen', 'movie_details.time',
    'movie_details.episode_current', 'movie_details.quality', 'movie_details.lang',
    'movie_details.showtimes', 'movie_details.category', 'movie_details.country']; 
    public $selectedColumnsV2 = ['movies._id', 'movies.name', 'movies.thumb_url', 'movies.slug', 'movies.year',
    'movie_details.sub_docquyen', 'movie_details.type', 'movie_details.episode_current', 'movie_details.category'];
    protected $moviesQuery; 
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
        // $this->moviesQuery = Movie::select('_id', 'name', 'slug', 'thumb_url', 'poster_url', 'year')
        // ->with('movie_detail');
        // $this->moviesWithNoTrailer = $this->moviesQuery->whereHas('movie_detail', function ($query) {
        //     $query->where(function ($subquery) {
        //         $subquery->where('status', '!=', 'trailer')
        //             ->orWhere('episode_current', '!=', 'Trailer');
        //     });
        // });

        $this->moviesQuery = Movie::select('_id', 'name', 'poster_url', 'thumb_url', 'slug', 'year', 'content', 'type',
    'status', 'view', 'time', 'episode_current', 'quality', 'lang', 'category', 'country');
        $this->moviesWithNoTrailer = $this->moviesQuery->where('status', '!=', 'trailer')
            ->where('episode_current', '!=', 'Trailer');
        
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
                $query->where('year', '=', $year);
            }

            $result = ($limit === 'all') ? $query->get() : $query->paginate($limit);
        
            $responseData = [
                'data' => MovieResource::collection($result),
                'seoOnPage' => $this->generateSeoData($title, $description, $year),
                'count' => $result->count()
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
    
     /**
     * @OA\Get(
     *     path="/the-loai/{category}",
     *     tags={"categories"},
     *     summary="Danh sách phim theo thể loại",
     *     @OA\Parameter(
     *         name="category_slug",
     *         in="path",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
    *     @OA\Response(response="200", description="Successful response"),
     * )
     */

     
     
    //PHIM THEO THỂ LOẠI
    public function getMoviesByCategory($category){
        $categoryName = $this->getCategoryNameBySlug($category);
        $title = "Phim Mới $categoryName \$year, Phim hay, Xem phim nhanh, Xem phim online, Phim mới $categoryName \$year vietsub hay nhất";
        $description = "Xem phim mới $categoryName \$year miễn phí nhanh chất lượng cao." .
        " Xem phim $categoryName \$year online Việt Sub, Thuyết minh, lồng tiếng chất lượng HD." .
        " Xem phim nhanh online chất lượng cao"; 
        // $moviesByCategory = $this->moviesWithNoTrailer->whereHas('movie_detail', function ($query) use ($category) {
        //     $query->whereJsonContains('category', ['slug' => $category]);
        // });

        $moviesByCategory = $this->moviesWithNoTrailer->whereJsonContains('category', ['slug' => $category]);
        return $this->getMoviesByFilter($moviesByCategory, 24, $title, $description);
    } 

    //LẤY TÊN QUỐC GIA
    protected function getCountryNameBySlug($slug){
        $countries = config('api_settings.countries');
        return $countries[$slug] ?? null;
    }


     /**
     * @OA\Get(
     *     path="/quoc-gia/{country_slug}",
     *     tags={"countries"},
     *     summary="Danh sách phim theo quốc gia",
     *     @OA\Parameter(
     *         name="country_slug",
     *         in="path",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM THEO QUỐC GIA
    public function getMoviesByCountry($country_slug){
        $countryName = $this->getCountryNameBySlug($country_slug);
        $title = "Phim Mới $countryName \$year, Phim hay, Xem phim nhanh, Xem phim online, Phim mới $countryName \$year vietsub hay nhất";
        $description = "Xem phim mới $countryName \$year miễn phí nhanh chất lượng cao." .
        "Xem phim $countryName \$year online Việt Sub, Thuyết minh, lồng tiếng chất lượng HD." .
        "Xem phim nhanh online chất lượng cao";
        // $moviesByCountry = $this->moviesWithNoTrailer->whereHas('movie_detail', function ($query) use ($slug) {
        //     $query->whereJsonContains('country', ['slug' => $slug]);
        // });

        $moviesByCountry = $this->moviesWithNoTrailer->whereJsonContains('country', ['slug' => $country_slug]);
        return $this->getMoviesByFilter($moviesByCountry, 24, $title, $description);
    } 
    
    //PHIM THEO LOẠI
    public function getMoviesByType($type, $title, $description){
        // $movies = $this->moviesWithNoTrailer
        // ->whereHas('movie_detail', function ($query) use ($type) {
        //     $query->where('type', $type);
        // });
        
        $movies = $this->moviesWithNoTrailer->where('type', $type);
        return $this->getMoviesByFilter($movies, 24, $title, $description);
    }

    /**
     * @OA\Get(
     *     path="/phim-bo",
     *     tags={"type"},
     *     summary="Danh sách phim bộ",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */

    //PHIM BỘ
    public function getSeriesMovies(){
        $title = "Phim bộ \$year, Phim bộ \$year hay tuyển chọn, Phim bộ mới nhất \$year";
        $description = "Phim bộ mới nhất tuyển chọn chất lượng cao, phim bộ mới nhất \$year vietsub cập nhật nhanh nhất. Phim bộ \$year vietsub nhanh nhất";
        return $this->getMoviesByType('series', $title, $description);
    }

         /**
     * @OA\Get(
     *     path="/phim-le",
     *     tags={"type"},
     *     summary="Danh sách phim lẻ",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM LẺ
    public function getSingleMovies(){
        $title = "Phim lẻ \$year, Phim lẻ \$year hay tuyển chọn, Phim lẻ mới nhất \$year";
        $description = "Phim lẻ mới nhất tuyển chọn chất lượng cao, phim lẻ mới nhất \$year vietsub cập nhật nhanh nhất. Phim lẻ \$year vietsub nhanh nhất";
        return $this->getMoviesByType('single', $title, $description);
    }

         /**
     * @OA\Get(
     *     path="/hoat-hinh",
     *     tags={"type"},
     *     summary="Danh sách phim hoạt hình",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM HOẠT HÌNH
    public function getCartoonMovies(){
        $title = "Phim hoạt hình, Phim hoạt hình hay tuyển chọn, Phim hoạt hình mới nhất \$year";
        $description = "Phim hoạt hình mới nhất tuyển chọn chất lượng cao, Phim hoạt hình mới nhất \$year vietsub cập nhật nhanh nhất. Phim hoạt hình vietsub nhanh nhất";
        return $this->getMoviesByType('hoathinh', $title, $description);
    }

         /**
     * @OA\Get(
     *     path="/subteam",
     *     tags={"type"},
     *     summary="Danh sách phim subteam",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM SUBTEAM
    public function getSubTeamMovies(){
        $title = 'Flashmov Subteam - Tuyển tập phim được dịch bởi Flashmov';
        $description = "Tổng hợp những bộ phim hot được vietsub trên Flashmov. Phim hay chọn lọc vietsub nhanh nhất \$year, cập nhật hàng ngày.";
        // $subTeamMovies = $this->moviesWithNoTrailer->whereHas('movie_detail', function ($query) {
        //     $query->where('movie_details.sub_docquyen', true);
        // });
        $subTeamMovies = $this->moviesWithNoTrailer->where('sub_docquyen', true);
        return $this->getMoviesByFilter($subTeamMovies, 24, $title, $description);
    }

         /**
     * @OA\Get(
     *     path="/phim-sap-chieu",
     *     tags={"type"},
     *     summary="Danh sách phim sắp chiếu",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM SẮP CHIẾU
    public function getUpcomingMovies(){
        $title = "Phim sắp chiếu \$year";
        $description = "$title mới nhất, tuyển chọn chất lượng cao, $title mới nhất, chọn lọc cập nhật nhanh nhất. $title phụ đề hay nhất.";
        // $upcomingMovies = Movie::leftJoin('movie_details', 'movie_details._id', '=', 'movies._id')
        // ->where('movie_details.episode_current', 'Trailer')
        // ->where('movie_details.trailer_url', '!=', '');

        $upcomingMovies = Movie::where('episode_current', 'Trailer')
        ->where('trailer_url', '!=', '');
        return $this->getMoviesByFilter($upcomingMovies, 24, $title, $description);
    }

    /**
     * @OA\Get(
     *     path="/thinh-hanh",
     *     tags={"type"},
     *     summary="Danh sách phim thịnh hành",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM ĐANG THỊNH HÀNH
    public function getTrendingMovies(Request $request){
        $title = "Xem phim mới, Phim đang thịnh hành | Flashmov | flashmov.xyz";
        $description = "";
        $time_window = $request->time_window ?? 'week';

    //     $topTrendingMovies = $this->moviesWithNoTrailer
    // ->whereHas('movie_detail', function ($query) {
    //     $query->latest('view');
    // })
    // ->when($time_window == "week", function ($query) {
    //     $query->whereBetween('modified_time', [$this->week, $this->tomorrow]);
    // })
    // ->when($time_window == "day", function ($query) {
    //     $query->whereBetween('modified_time', [$this->today, $this->tomorrow]);
    // });

    $topTrendingMovies = $this->moviesWithNoTrailer
    ->when($time_window == "week", function ($query) {
        // $query->whereBetween('modified_time', [$this->week, $this->tomorrow]);
    })
    ->when($time_window == "day", function ($query) {
        // $query->whereBetween('modified_time', [$this->today, $this->tomorrow]);
    })->latest('view');

        return $this->getMoviesByFilter($topTrendingMovies, 24, $title, $description);
    }

    //PHIM MỚI CẬP NHẬT THEO LOẠI 
    protected function getNewUpdatedMoviesByType($type, $title, $description){
        // $newUpdatedMoviesByType = $this->moviesWithNoTrailer
        // ->orderByDesc('year')
        // ->orderByDesc('modified_time')
        // ->whereHas('movie_detail', function ($query) use ($type) {
        //     $query->where('type', $type);
        // });

        $newUpdatedMoviesByType = $this->moviesWithNoTrailer
        ->orderByDesc('year')   
        ->orderByDesc('modified_time')
        ->where('type', $type);
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

    /**
     * @OA\Get(
     *     path="/hom-nay-xem-gi",
     *     tags={"type"},
     *     summary="Danh sách phim hôm nay xem gì",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //HÔM NAY XEM GÌ
    public function getMoviesAirToday(){
        $title = 'Hôm nay xem gì';
        $description = "";

        // $moviesAirToday = $this->moviesWithNoTrailer
        // ->whereBetween('modified_time', [$this->week, $this->tomorrow])
        // ->whereHas('movie_detail', function ($query){
        //     $query->orderBy('view', 'asc');
        // });

        $moviesAirToday = $this->moviesWithNoTrailer
        ->whereBetween('modified_time', [$this->week, $this->tomorrow])
        ->orderBy('view', 'asc');
        return $this->getMoviesByFilter($moviesAirToday, 10, $title, $description);
    }

    /**
     * @OA\Get(
     *     path="/tim-kiem",
     *     tags={"search"},
     *     summary="Tìm kiếm phim",
     *     @OA\Parameter(
     *         name="keyword",
     *         description="Tên phim",
     *         in="query",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful response"),
     * ),
     */
    
    //TÌM KIẾM PHIM
    public function searchMovie(Request $request){
        try {
            $keyword = $request->keyword;
        $title = "Phim $keyword | $keyword vietsub | Phim $keyword hay | Tuyển tập $keyword mới nhất \$year";
        $description = "Phim $keyword hay tuyển tập, phim $keyword mới nhất, tổng hợp phim $keyword, $keyword full HD, $keyword vietsub, xem $keyword online";

        $words = explode(' ', $keyword);
    $searchedMovies = $this->moviesWithNoTrailer
    ->where(function ($query) use ($keyword, $words) {
        $query->orWhere(function ($innerQuery) use ($keyword) {
            $innerQuery->where('name', 'LIKE', $keyword . '%')
                ->orWhere('origin_name', 'LIKE', $keyword . '%')
                ->orWhere('slug', 'LIKE', $keyword . '%');
        });

        foreach ($words as $word) {
            $query->orWhere('name', 'LIKE', '%' . $word . '%');
        }
    })
    ->orderBy(function ($query) use ($keyword) {
        return $query->selectRaw("CASE WHEN name LIKE '{$keyword}%' THEN 0 ELSE 1 END");
    });

        return $this->getMoviesByFilter($searchedMovies, 24, $title, $description);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        
    } 


   
    public function get18sMovies(){
        try {
            $movies = $this->moviesWithNoTrailer->take(10)->get();
        foreach($movies as $movie){
            $categories = json_decode($movie->category);
            if(count($categories) == 3 && $categories[0]->slug == 'hanh-dong'){
                return $movie;
        }
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }    

}
