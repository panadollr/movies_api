<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Console\Commands\CrawlMovieDetails;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
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
        $this->info("\nMovies data crawled successfully !");

        // $this->info("\nCrawling movie details data...");
        // $this->crawlMovieDetails->crawl($this->client, $this->base_url);
        // $this->info("\nMovie details data crawled successfully !");
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $totalExecutionTime += $executionTime;
        $this->info("\nTotal execution time: {$totalExecutionTime} seconds.");
    }

    // protected function getTotalPages()
    // {
    //     $total_movies = Movie::count();
    //     return $pagination_data['pagination']['totalPages'];
    // }

    // protected function crawlAll(){
    //     $total = $this->getTotalPages();
    //     $batchSize = 10;
    
    //     for ($start = 1; $start <= $total; $start += $batchSize) {
    //         $end = min($start + $batchSize - 1, $total);
    //         $batch_movies = [];
    //         $promises = [];
    //         for ($page = $start; $page <= $end; $page++) {
    //             $url = $this->base_url . "danh-sach/phim-moi-cap-nhat?page=$page";
    //             $promises[] = $this->client->getAsync($url);
    //         }
    //         $responses = Promise\settle($promises)->wait();
 
    //         foreach ($responses as $response) {
    //             if ($response['state'] === 'fulfilled') {
    //                 $statusCode = $response['value']->getStatusCode();
    //                 if($statusCode == 200){
    //                     $movies_data = json_decode($response['value']->getBody());
    //                     $batch_movies[] = $movies_data->items;
    //                 }
    //             }
    //         }
    //             $this->processMovies($batch_movies);
    //     }
    //     DB::statement('ALTER TABLE movies ORDER BY modified_time DESC;');
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
            $responses = Promise\settle($promises)->wait();
 
            foreach ($responses as $response) {
                if ($response['state'] === 'fulfilled') {
                    $statusCode = $response['value']->getStatusCode();
                    if($statusCode == 200){
                        $movies_data = json_decode($response['value']->getBody());
                        $batch_movies[] = $movies_data->items;
                    }
                }
            }
                $this->processMovies($batch_movies);
        }
        // DB::statement('ALTER TABLE movies ORDER BY modified_time DESC;');
    }
    

protected function processMovies($movies_data)
{
    $newMovies = [];
    $attributes = [
        'modified_time', '_id', 'name', 'origin_name', 'thumb_url', 'slug', 'year', 'poster_url'
    ];

    foreach ($movies_data as $resultArray) {
        foreach ($resultArray as $result) {
        if($result->year >= 2007){
            $existingMovie = Movie::where('_id', $result->_id)->first();
        if (!$existingMovie) {
            $newMovie = [];
            foreach ($attributes as $attribute) {
                     $newMovie[$attribute] = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
            }
            $newMovies[] = $newMovie;
            
            try {
                $publicIdImage = "uploads/movies/" . pathinfo($newMovie['thumb_url'], PATHINFO_FILENAME);
            Cloudinary::upload($newMovie['thumb_url'], [
                'format' => 'webp',
                'public_id' => $publicIdImage,
                'options' => [
                    'format' => 'webp',
                    'width' => '600',
                    'height' => 'auto',
                    'quality' => 'auto',
                    'overwrite' => false,
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
