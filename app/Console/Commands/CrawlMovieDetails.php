<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\DB;

use App\Models\Movie;
use App\Models\MovieDetails;

class CrawlMovieDetails extends Command
{
  
    protected $signature = 'crawl:movie_details';
    protected $description = 'Crawl movie details';


    //CRAWL TẤT CẢ
    // public function crawlAll(){
    //     $batchSize = 250;
    //     $totalExecutionTime = 0;
    //     Movie::chunk($batchSize, function ($movies) use (&$totalExecutionTime) {
    //         $startTime = microtime(true);

    //         $batch_movie_slugs = $movies->pluck('slug')->toArray();
    //         if (!empty($batch_movie_slugs)) {
    //         $this->processMovieDetails($batch_movie_slugs);
    //         }
        
    //         $endTime = microtime(true);
    //         $executionTime = $endTime - $startTime;
    //         $totalExecutionTime += $executionTime;
        
    //         $this->info("Processed {$movies->count()} movie details in {$executionTime} seconds.");
    //     });
        
    //     $this->info("Total execution time: {$totalExecutionTime} seconds.");
    // }

//---------------------------------------------------------------//

    protected $base_url;
    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
        $this->base_url = 'https://ophim1.com/';
    }

    public function handle()
    {
        $totalExecutionTime = 0;
        $startTime = microtime(true);
        $this->info('Crawling movie details data...');
        $this->crawl();
        $this->info('Movie details data crawled successfully.');
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $totalExecutionTime += $executionTime;
        $this->info("\nTotal execution time: {$totalExecutionTime} seconds.");
    }

    public function crawl()
    {
        //... Lấy tất cả trong movies...
        // Movie::orderBy('_id')->select('slug')->take(100)->chunk(100, function ($movies) {
        //     $batch_movie_slugs = $movies->pluck('slug')->toArray();
        //     $this->processMovieDetails($batch_movie_slugs);
        // });    
        
        //...Lấy các bản ghi từ bảng "movies" mà không có tương ứng trong bảng "movie_details"...
        $batch_movie_slugs = Movie::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('movie_details as md')
                ->whereColumn('md._id', 'movies._id');
        })->select('slug')->pluck('slug')->toArray();
        if (!empty($batch_movie_slugs)) {
            $this->processMovieDetails($batch_movie_slugs);
        }
    }

    public function processMovieDetails($batch_movie_slugs) {
        try {
    $attributes = [
        '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
        'trailer_url', 'time', 'episode_current', 'episode_total',
        'quality', 'lang', 'notify', 'showtimes', 'view'
    ];
    $arraysToJSON = ['actor', 'director', 'category', 'country'];
   
    $promises = [];
    foreach ($batch_movie_slugs as $slug) {
        $url = $this->base_url . "phim/$slug";
        $promises[] = $this->client->getAsync($url);
    }
    $responses = Utils::all($promises)->wait();

    $batch_movie_details = [];
    foreach ($responses as $response) {
                $statusCode = $response->getStatusCode();
                if($statusCode == 200){
                    $movie_details_data = json_decode($response->getBody())->movie;
                    $existingMovieDetail = MovieDetails::where('_id', $movie_details_data->_id)->first();

                    if (!$existingMovieDetail) {
                            $newMovieDetails = $this->prepareNewMovieDetails($attributes, $arraysToJSON, $movie_details_data);
                            $batch_movie_details[] = $newMovieDetails;
                    } else {
                        $this->handleExistingMovieDetails($attributes, $arraysToJSON, $existingMovieDetail, $movie_details_data);
                    }
                }
        }
        
        if (!empty($batch_movie_details)) {
                MovieDetails::insert($batch_movie_details);
                print_r('New movie detail inserted !');
            }
        } catch (\Throwable $th) {
            print_r($th->getMessage());
        }
}

private function prepareNewMovieDetails($attributes, $arraysToJSON, $movie_details_data)
{
    $newMovieDetails = [];
    foreach ($attributes as $attribute) {
        $newMovieDetails[$attribute] = $movie_details_data->$attribute;
    }

    foreach ($arraysToJSON as $arrayAttribute) {
        $newMovieDetails[$arrayAttribute] = json_encode($movie_details_data->$arrayAttribute);
    }

    return $newMovieDetails;
}


// protected function filterOutAdultMovies($_id, $categories){
//     if(count($categories) == 1 && $categories[0]['slug'] == 'phim-18'){
//        $movie = Movie::where('_id', $_id)->first();
//        if($movie){
//         $movie->delete();
//        }
//         return true;
//     } else {
//         return false;
//     }
// }


//cập nhật các phim đã tồn tại
protected function handleExistingMovieDetails($attributes, $arraysToJSON, $existingMovieDetails, $newMovieData)
{
    $updates = [];

    foreach ($attributes as $attribute) {
        if ($existingMovieDetails->$attribute !== $newMovieData->$attribute) {
            $updates[$attribute] = $newMovieData->$attribute;
        }
    }

    foreach ($arraysToJSON as $arrayAttribute) {
        $existingValue = json_encode($existingMovieDetails->$arrayAttribute);
        $newValue = json_encode($newMovieData->$arrayAttribute);

        if ($existingValue !== $newValue) {
            $updates[$arrayAttribute] = $newValue;
        }
    }

    if (!empty($updates)) {
        MovieDetails::where('_id', $existingMovieDetails->_id)->update($updates);
    }
}




}
