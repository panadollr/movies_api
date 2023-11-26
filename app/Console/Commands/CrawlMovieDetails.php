<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

use App\Models\Movie;
use App\Models\MovieDetails;

class CrawlMovieDetails extends Command
{
  
    protected $signature = 'movie_details:crawl';
    protected $description = 'Crawl movie details';

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
        $this->base_url = 'https://ophim1.com/';
    }

    public function handle()
    {
        $this->crawlMovieDetails();
    }

    protected function getTotalMovies()
    {
        $total_movies = Movie::count();
        return $total_movies;
    }

    protected function crawlMovieDetails()
    {
        $batchSize = 100;
        $totalExecutionTime = 0;
        Movie::chunk($batchSize, function ($movies) use (&$totalExecutionTime) {
            $startTime = microtime(true);

            $batch_movie_slugs = $movies->pluck('slug')->toArray();
            if (!empty($batch_movie_slugs)) {
            $this->processMovieDetails($batch_movie_slugs);
            }
        
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            $totalExecutionTime += $executionTime;
        
            $this->info("Processed {$movies->count()} movies in {$executionTime} seconds.");
        });
        
        $this->info("Total execution time: {$totalExecutionTime} seconds.");
    }


protected function processMovieDetails($batch_movie_slugs) {
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
    $responses = Promise\settle($promises)->wait();

    $batch_movie_details = [];
    foreach ($responses as $response) {
            if ($response['state'] === 'fulfilled') {
                $statusCode = $response['value']->getStatusCode();
                if($statusCode == 200){
                    $movie_details_data = json_decode($response['value']->getBody())->movie;
                    $existingMovieDetails = MovieDetails::where('_id', $movie_details_data->_id)->first();
                    if (!$existingMovieDetails) {
                        $newMovieDetails = [];
                        foreach ($attributes as $attribute) {
                            $newMovieDetails[$attribute] = $movie_details_data->$attribute;
                        }
    
                        foreach ($arraysToJSON as $arrayAttribute) {
                            $newMovieDetails[$arrayAttribute] = json_encode($movie_details_data->$arrayAttribute);
                        }
    
                        $batch_movie_details[] = $newMovieDetails;
                    }
                }
            }
        }
        
        if (!empty($batch_movie_details)) {
                MovieDetails::insert($batch_movie_details);
            }
}


}



 // $movie_details_response = $this->client->request('GET', $this->base_url . 'phim/' . $slug);
    // $movie_details_data = json_decode($movie_details_response->getBody(), true);
    // $movie_data = $movie_details_data['movie'];
    // $episodes_data = $movie_details_data['episodes'];

    // $newMovieDetailsArr = [];

    // $attributes = [
    //     '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
    //     'trailer_url', 'time', 'episode_current', 'episode_total',
    //     'quality', 'lang', 'notify', 'showtimes', 'view'
    // ];

    //     $existingMovieDetails = MovieDetails::where('_id', $movie_data['_id'])->first();

    //     if(!$existingMovieDetails){
    //         $newMovieDetails = [];
    //         foreach ($attributes as $attribute) {
    //             $newMovieDetails[$attribute] = $movie_data[$attribute];
    //         }
        
    //         $arraysToJSON = ['actor', 'director', 'category', 'country'];
    //         foreach ($arraysToJSON as $arrayAttribute) {
    //             $newMovieDetails[$arrayAttribute] = json_encode($movie_data[$arrayAttribute]);
    //         }
    //         $newMovieDetailsArr[] = $newMovieDetails;
    //         // $this->processEpisodes($movie_data['_id'], $episodes_data);
    //     } 

    //     // Chèn nhiều bản ghi một lúc
    // if (!empty($newMovieDetailsArr)) {
    //     MovieDetails::insert($newMovieDetailsArr);

    //     // Sau khi chèn, bạn có thể xử lý các episodes phim
    // }
