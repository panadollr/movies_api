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
        // $total = 20;
        // $batchSize = 5;

        $total = 1;
        $batchSize = 1;
    
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
    

protected function processMovies($movies_data)
{
    $newMovies = [];
    $attributes = [
        'modified_time', '_id', 'name', 'origin_name', 'thumb_url', 'slug', 'year', 'poster_url'
    ];

    foreach ($movies_data as $resultArray) {
        foreach ($resultArray as $result) {
        if($result->year > 2007){
            $existingMovie = Movie::where('_id', $result->_id)->first();
        if (!$existingMovie) {
            $newMovie = [];
            foreach ($attributes as $attribute) {
                     $newMovie[$attribute] = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
            }
            $newMovies[] = $newMovie;
            
            try {
            $posterUrl = "https://img.ophim9.cc/uploads/movies/{$result->thumb_url}";
            $posterName = str_replace("-thumb.jpg", "-poster", $result->thumb_url);
                $publicIdImage = "uploads/movies/" . $posterName;
            Cloudinary::upload($posterUrl, [
                'format' => 'webp',
                'public_id' => $publicIdImage,
                'options' => [
                    'format' => 'webp',
                    'quality' => 'auto',
                    'overwrite' => false,
                ],
                'transformation' => [
                    'width' => 450,
                ],
            ]);

            $thumbUrl = "https://img.ophim9.cc/uploads/movies/{$result->poster_url}";
            $thumbName = str_replace("-poster.jpg", "-thumb", $result->poster_url);
                $publicIdImage = "uploads/movies/" . $thumbName;
            Cloudinary::upload($thumbUrl, [
                'format' => 'webp',
                'public_id' => $publicIdImage,
                'options' => [
                    'format' => 'webp',
                    'quality' => 'auto',
                    'overwrite' => false,
                ],
                'transformation' => [
                    'width' => 1920,
                    'height' => 1080
                ],
            ]);
            
            } catch (\Throwable $th) {
                //throw $th;
            }
                 

        } else {
            $this->updateMovieAttributes($existingMovie, $result, $attributes);
        }
        }     
    }
}
    if (!empty($newMovies)) {
        Movie::insert($newMovies);
        print_r('new movie is inserted !');
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
