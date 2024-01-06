<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Console\Commands\CrawlMovieDetails;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CrawlMovies extends Command
{
    protected $signature = 'crawl:movies';
    protected $description = 'Crawl movies data';
    protected $client;
    protected $base_url;
    // protected $crawlMovieDetails;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
        $this->base_url = 'https://ophim1.com/';
        // $this->crawlMovieDetails = new CrawlMovieDetails();
    }

    public function handle()
    {
        $totalExecutionTime = 0;
        $startTime = microtime(true);

        $this->info('Crawling movies data...');
        $this->crawl();
        // $this->crawlAll();
        $this->info("\nMovies data crawled successfully !");
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $totalExecutionTime += $executionTime;
        $this->info("\nTotal execution time: {$totalExecutionTime} seconds.");
    }

    // protected function getTotalPages()
    // {
    //     return $pagination_data['pagination']['totalPages'];
    // }

    // protected function crawlAll(){
    //     $total = 1043;
    //     $batchSize = 10;
    
    //     for ($start = 1; $start <= $total; $start += $batchSize) {
    //         $end = min($start + $batchSize - 1, $total);
    //         $batch_movies = [];
    //         $promises = [];
    //         for ($page = $start; $page <= $end; $page++) {
    //             $url = $this->base_url . "danh-sach/phim-moi-cap-nhat?page=$page";
    //             $promises[] = $this->client->getAsync($url);
    //         }
    //         $responses = Utils::all($promises)->wait();
 
    //         foreach ($responses as $response) {
    //             $statusCode = $response->getStatusCode();
    //             if($statusCode == 200){
    //                     $movies_data = json_decode($response->getBody());
    //                     $batch_movies[] = $movies_data->items;
    //                 }
    //         }
    //             $this->processMovies($batch_movies);
    //     }
    // }

   
    protected function crawl(){
        $total = 1045;
        $batchSize = 20;

        // $total = 50;
        // $batchSize = 5;
    
        for ($start = 1; $start <= $total; $start += $batchSize) {
            $end = min($start + $batchSize - 1, $total);
            $batch_movies = [];
            $promises = [];
            for ($page = $start; $page <= $end; $page++) {
                $url = $this->base_url . "danh-sach/phim-moi-cap-nhat?page=$page";
                $promises[] = $this->client->getAsync($url);
            }
            $responses = Utils::all($promises)->wait();
 
            foreach ($responses as $response) {
                    $statusCode = $response->getStatusCode();
                    if($statusCode == 200){
                        $movies_data = json_decode($response->getBody());
                        $batch_movies[] = $movies_data->items;
                    }
            }
                $this->processMovies($batch_movies);
        }
    }
    

// protected function processMovies($movies_data)
// {
//     $newMovies = [];
//     $existingIds = [];
//     $attributes = [
//         'modified_time', '_id', 'name', 'origin_name', 'thumb_url', 'slug', 'year', 'poster_url'
//     ];

//     foreach ($movies_data as $resultArray) {
//         foreach ($resultArray as $result) {
//             if ($result->year > 2007) {
//                 $existingIds[] = $result->_id;
//             }
//         }
//     }

//     $existingMovies = Movie::whereIn('_id', $existingIds)->pluck('_id')->toArray();

//     foreach ($movies_data as $resultArray) {
//         foreach ($resultArray as $result) {
//             if ($result->year > 2007) {
//                 if (!in_array($result->_id, $existingMovies)) {
//                     $newMovie = [];
//                     foreach ($attributes as $attribute) {
//                         $newMovie[$attribute] = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
//                     }
//                     $newMovies[] = $newMovie;
                    
//                     if ($result->year === 2023) {
//                      // ... Cloudinary upload...
//                     $posterUrl = "https://img.ophim9.cc/uploads/movies/{$result->thumb_url}";
//                     $posterTransformation = ['width' => 450];
//                     $this->uploadImageToCloudinary($posterUrl, $posterTransformation, 'uploads/movies/');

//                     $thumbUrl = "https://img.ophim9.cc/uploads/movies/{$result->poster_url}";
//                     $thumbTransformation = ['width' => 1920, 'height' => 1080];
//                     $this->uploadImageToCloudinary($thumbUrl, $thumbTransformation, 'uploads/movies/');
//                     }

//                 } else {
//                     $existingMovie = Movie::where('_id', $result->_id)->first();
//                     $this->updateMovieAttributes($existingMovie, $result, $attributes);
//                 }
//             }
//         }
//     }

//     if (!empty($newMovies)) {
//         Movie::insert($newMovies);
//         print_r('New movies inserted!');
//     }
// }


//TESTING...
protected function processMovies($movies_data)
{
    $newMovies = [];
    $existingIds = [];
    $attributes = [
        'modified_time', '_id', 'name', 'origin_name', 'thumb_url', 'slug', 'year', 'poster_url'
    ];

    foreach ($movies_data as $resultArray) {
        foreach ($resultArray as $result) {
           
                $existingIds[] = $result->_id;
           
        }
    }

    $existingMovies = Movie::whereIn('_id', $existingIds)->pluck('_id')->toArray();
    $countries = config('api_settings.countries');

    foreach ($movies_data as $resultArray) {
        foreach ($resultArray as $result) {
                if (!in_array($result->_id, $existingMovies)) {
                    if ($result->year > 2007) {
                        //truy cập vào chi tiết phim theo slug 
                    $movie_detail_url = $this->base_url . "phim/$result->slug";
                    $get_movie_detail = $this->client->getAsync($movie_detail_url);
                    // Đợi kết quả và lấy response
                    $response = $get_movie_detail->wait();
                    if($response->getStatusCode() == 200){
                        $movie_details_data = json_decode($response->getBody())->movie;
                        //không phải là tv-shows
                        if ($movie_details_data->type != 'tvshows') {
                            $countriesArray = $movie_details_data->country;
                        if (!empty($countriesArray)) {
                            $inValidCountries = 0;
                            foreach ($countriesArray as $country) {
                                //nếu không tồn tại quốc gia hợp lệ
                                if (!isset($countries[$country->slug])) {
                                    $inValidCountries ++;
                                } 
                            }
                            //tổng số phim có quốc gia không hợp lệ bằng 0
                            if ($inValidCountries === 0) {
                            $newMovie = [];
                            foreach ($attributes as $attribute) {
                            $newMovie[$attribute] = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
                            }
                            $newMovies[] = $newMovie;
                    
                            // ... Cloudinary upload...
                            $posterUrl = "https://img.ophim9.cc/uploads/movies/{$result->thumb_url}";
                            if (preg_match('/\/movies\/([^\/]+)-thumb\.jpg$/', $posterUrl, $matches)) {
                                $slug = $matches[1];
                                $posterTransformation = ['width' => 450];
                                $this->uploadImageToCloudinary($slug, 'poster', $posterUrl, $posterTransformation);
                            }

                            $thumbUrl = "https://img.ophim9.cc/uploads/movies/{$result->poster_url}";
                            if (preg_match('/\/movies\/([^\/]+)-thumb\.jpg$/', $posterUrl, $matches)) {
                                $slug = $matches[1];
                                $thumbTransformation = ['width' => 1920, 'height' => 1080];
                                $this->uploadImageToCloudinary($slug, 'thumb', $thumbUrl, $thumbTransformation);
                            }
                            } 

                        }
                        }
                    }
                }

                } else {
                    $existingMovie = Movie::where('_id', $result->_id)->first();
                    $this->updateMovieAttributes($existingMovie, $result, $attributes);
                }
        }
    }

    print_r($newMovies);

    if (!empty($newMovies)) {
        Movie::insert($newMovies);
        print_r('New movies inserted!');
    }
    
}

 // ... Cloudinary upload logic...
protected function uploadImageToCloudinary($slug, $format, $url, $transformation = []) {
    try {
        $publicId = "uploads/movies/$slug-$format";
        return Cloudinary::upload($url, [
            'format' => 'webp',
            'public_id' => $publicId,
            'options' => [
                'format' => 'webp',
                'quality' => 'auto',
                'overwrite' => false,
            ],
            'transformation' => $transformation,
        ]);
    } catch (\Throwable $th) {
      
    }
 }


protected function updateMovieAttributes($existingMovie, $result, $attributes)
{
    $updates = [];

    foreach ($attributes as $attribute) {
        $newValue = ($attribute === 'modified_time') ? $result->modified->time : $result->$attribute;

        if ($existingMovie->$attribute != $newValue) {
            $updates[$attribute] = $newValue;
        }
    }

    if (!empty($updates)) {
        Movie::where('_id', $existingMovie->_id)->update($updates);
    }
}



}
