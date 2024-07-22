<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Models\Movie;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MovieController
{

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
        $this->moviesWithNoTrailer = Movie::where('status', '!=', 'trailer')
            ->where('episode_current', '!=', 'Trailer');
    }

    protected function generateSeoData($title, $description, $year)
    {
        $yearString = $year ? "năm $year" : '';
        $replacePairs = [
            '$year' => $yearString,
            '  ' => ' ',
            ' ,' => ',',
            ',' => ' |'
        ];

        $seoTitle = strtr(trim($title), $replacePairs);
        $seoDescription = strtr(trim($description), $replacePairs);

        return [
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'og_url' => request()->url(),
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

     /**
     * @OA\Get(
     *     path="/the-loai",
     *     tags={"Categories"},
     *     summary="Danh sách thể loại",
    *     @OA\Response(response="200", description="Successful response"),
     * )
     */   
    public function getCategories(){
        $categories = config('api_settings.categories');
        $data = ['data'=> [
            'items' => []
        ]];
        foreach($categories as $category_slug => $category_name){
            $data['data']['items'][] = [
                    'slug' => $category_slug,
                    'name' => $category_name
            ];
        }
        return $data;
    }

    //LẤY TÊN THỂ LOẠI
    protected function getCategoryNameBySlug($slug){
        $categories = config('api_settings.categories');
        return $categories[$slug] ?? null;
    }
    
     /**
     * @OA\Get(
     *     path="/the-loai/{category_slug}",
     *     tags={"Categories"},
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

        $moviesByCategory = $this->moviesWithNoTrailer->whereJsonContains('category', ['slug' => $category]);
        return $this->getMoviesByFilter($moviesByCategory, 24, $title, $description);
    } 
    
    //PHIM THEO LOẠI
    public function getMoviesByType($type, $title, $description){
        $movies = $this->moviesWithNoTrailer->where('type', $type);
        return $this->getMoviesByFilter($movies, 24, $title, $description);
    }

    /**
     * @OA\Get(
     *     path="/phim-bo",
     *     tags={"Types"},
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
     *     tags={"Types"},
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
     *     tags={"Types"},
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
     *     path="/phim-sap-chieu",
     *     tags={"Types"},
     *     summary="Danh sách phim sắp chiếu",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM SẮP CHIẾU
    public function getUpcomingMovies()
    {
        $title = "Phim sắp chiếu \$year";
        $description = "$title mới nhất, tuyển chọn chất lượng cao, $title mới nhất, chọn lọc cập nhật nhanh nhất. $title phụ đề hay nhất.";
    
        // Fetch upcoming movies
        $upcomingMovies = Movie::where('episode_current', 'Trailer')
            ->where('trailer_url', '!=', '')
            ->orderByDesc('year')
            ->select(['id', 'name', 'views', 'slug'])
            ->take(10)->get(); 
    
        // Fetch external ratings
        $externalRatings = [];
        foreach ($upcomingMovies as $movie) {
            $response = Http::get('https://ophim1.com/phim/' . $movie->slug);
            $tdmb = $response->json('movie.tmdb');
            $externalRatings[$movie->id] = $tdmb['vote_average'] ?? 0; // Default to 0 if rating is not available
        }
    
        // Add ratings to movies and sort
        $upcomingMovies = $upcomingMovies->map(function ($movie) use ($externalRatings) {
            $movie->rating = $externalRatings[$movie->id] ?? 0; // Default to 0 if rating is not available
            return $movie;
        })->sortByDesc('rating')->sortByDesc('views'); // Sort first by rating, then by views
    
        // Paginate results
        $limit = request()->input('limit', 24);
        $result = $limit === 'all' ? $upcomingMovies : $upcomingMovies->slice(0, $limit); // Use slice for pagination
    
        // Prepare response
        $responseData = [
            'data' => MovieResource::collection($result),
            'seoOnPage' => $this->generateSeoData($title, $description, null),
            'count' => $result->count()
        ];
    
        $response = ($limit === 'all') ? $responseData : new PaginationResource($responseData);
    
        return response()->json($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/thinh-hanh",
     *     tags={"Types"},
     *     summary="Danh sách phim thịnh hành",
     *     @OA\Response(response="200", description="Successful response"),
     * )
     */
    //PHIM ĐANG THỊNH HÀNH
    public function getTrendingMovies(Request $request)
    {
        $title = "Xem phim mới, Phim đang thịnh hành";
        $description = "";
        // $time_window = $request->time_window ?? 'week';

        // $topTrendingMovies = Movie::
        //     ->when($time_window == 'week', function ($query) {
        //         $query->whereBetween('modified_time', [$this->week, $this->tomorrow]);
        //     })
        //     ->when($time_window == 'day', function ($query) {
        //         $query->whereBetween('modified_time', [$this->today, $this->tomorrow]);
        //     })
        //     ->orderBy('view', 'desc');

        $topTrendingMovies = Movie::where('trailer_url', '!=', 'null')->orderBy('view', 'desc');

        return $this->getMoviesByFilter($topTrendingMovies, 24, $title, $description);
    }

    //PHIM MỚI CẬP NHẬT THEO LOẠI 
    protected function getNewUpdatedMoviesByType($type, $title, $description){
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
     *     tags={"Types"},
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
     *     tags={"Search"},
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
    // public function searchMovie(Request $request){
    //     try {
    //         $keyword = $request->keyword;
    //     $title = "Phim $keyword | $keyword vietsub | Phim $keyword hay | Tuyển tập $keyword mới nhất \$year";
    //     $description = "Phim $keyword hay tuyển tập, phim $keyword mới nhất, tổng hợp phim $keyword, $keyword full HD, $keyword vietsub, xem $keyword online";

    //     $words = explode(' ', $keyword);
    // $searchedMovies = $this->moviesWithNoTrailer
    // ->where(function ($query) use ($keyword, $words) {
    //     $query->orWhere(function ($innerQuery) use ($keyword) {
    //         $innerQuery->where('name', 'LIKE', $keyword . '%')
    //             ->orWhere('origin_name', 'LIKE', $keyword . '%')
    //             ->orWhere('slug', 'LIKE', $keyword . '%');
    //     });

    //     foreach ($words as $word) {
    //         $query->orWhere('name', 'LIKE', '%' . $word . '%');
    //     }
    // })
    // ->orderBy(function ($query) use ($keyword) {
    //     return $query->selectRaw("CASE WHEN name LIKE '{$keyword}%' THEN 0 ELSE 1 END");
    // });

    //     return $this->getMoviesByFilter($searchedMovies, 24, $title, $description);
    //     } catch (\Throwable $th) {
    //         return $th->getMessage();
    //     }
        
    // }    

        public function searchMovie(Request $request)
    {
        try {
            $keyword = $request->keyword;
            $title = "Phim $keyword | $keyword vietsub | Phim $keyword hay";
            $description = "Phim $keyword hay tuyển tập, phim $keyword mới nhất.";

            $searchedMovies = $this->moviesWithNoTrailer
                ->where(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%$keyword%")
                        ->orWhere('origin_name', 'LIKE', "%$keyword%")
                        ->orWhere('slug', 'LIKE', "%$keyword%");
                })
                ->orderByRaw("CASE WHEN name LIKE '{$keyword}%' THEN 0 ELSE 1 END");

            return $this->getMoviesByFilter($searchedMovies, 24, $title, $description);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

}
