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
    // protected $base_url;
    // protected $client;

    // public function __construct(Client $client)
    // {
    //     parent::__construct();
    //     $this->client = $client;
    //     $this->base_url = 'https://ophim1.com/';
    // }

    // public function handle()
    // {
    //     $this->info('Crawling movie details data...');
    //     $this->crawl();
    //     $this->info('Movie details data crawled successfully.');
    // }

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

    public function crawl($client, $base_url)
    {
        $batchSize = 120;
        $totalExecutionTime = 0;
        $batch_movie_slugs = Movie::select('slug')->take($batchSize)->pluck('slug')->toArray();
        $startTime = microtime(true);
        if (!empty($batch_movie_slugs)) {
            $this->processMovieDetails($batch_movie_slugs, $client, $base_url);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $totalExecutionTime += $executionTime;
    }


public function processMovieDetails($batch_movie_slugs, $client, $base_url) {
    $attributes = [
        '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
        'trailer_url', 'time', 'episode_current', 'episode_total',
        'quality', 'lang', 'notify', 'showtimes', 'view'
    ];
    $arraysToJSON = ['actor', 'director', 'category', 'country'];
   
    $promises = [];
    foreach ($batch_movie_slugs as $slug) {
        $url = $base_url . "phim/$slug";
        $promises[] = $client->getAsync($url);
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
                    } else {
                        if($existingMovieDetails->status == 'ongoing'){
                            $this->updateMovieDetailsAttributes($attributes, $arraysToJSON, $existingMovieDetails, $movie_details_data);
                        }
                    }
                }
            }
        }
        
        if (!empty($batch_movie_details)) {
                MovieDetails::insert($batch_movie_details);
            }
}


protected function updateMovieDetailsAttributes($attributes, $arraysToJSON, $existingMovieDetails, $newMovieData)
{
    $updateRequired = false;

    foreach ($attributes as $attribute) {
        if ($existingMovieDetails->$attribute !== $newMovieData->$attribute) {
            $existingMovieDetails->$attribute = $newMovieData->$attribute;
            $updateRequired = true;
        }
    }

    foreach ($arraysToJSON as $arrayAttribute) {
        $existingValue = json_encode($existingMovieDetails->$arrayAttribute);
        $newValue = json_encode($newMovieData->$arrayAttribute);

        if ($existingValue !== $newValue) {
            $existingMovieDetails->$arrayAttribute = $newValue;
            $updateRequired = true;
        }
    }

    if ($updateRequired) {
        $existingMovieDetails->save();
    }
}


}
