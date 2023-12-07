<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;


use App\Models\Movie;
use App\Models\MovieDetails;

class CrawlMovieDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
    protected $base_url;
    protected $client;

    public function __construct()
    {
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
    $batchSize = 120;
    $batchMovieSlugs = Movie::take($batchSize)->pluck('slug')->toArray();
    
    if (!empty($batchMovieSlugs)) {
        foreach ($batchMovieSlugs as $slug) {
            $url = $this->base_url . "phim/$slug";
            $response = $this->client->get($url);

            if ($response->getStatusCode() == 200) {
                $movieDetailsData = json_decode($response->getBody())->movie;

                $this->processMovieDetails($movieDetailsData);
            }
        }
    }

    
}

    public function processMovieDetails($movieDetailsData) {
    $attributes = [
        '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
        'trailer_url', 'time', 'episode_current', 'episode_total',
        'quality', 'lang', 'notify', 'showtimes', 'view'
    ];
    $arraysToJSON = ['actor', 'director', 'category', 'country'];
   

    $batch_movie_details = [];

                    $existingMovieDetails = MovieDetails::where('_id', $movieDetailsData->_id)->first();
                    if (!$existingMovieDetails) {
                        $newMovieDetails = [];
                        foreach ($attributes as $attribute) {
                            $newMovieDetails[$attribute] = $movieDetailsData->$attribute;
                        }
    
                        foreach ($arraysToJSON as $arrayAttribute) {
                            $newMovieDetails[$arrayAttribute] = json_encode($movieDetailsData->$arrayAttribute);
                        }
    
                        $batch_movie_details[] = $newMovieDetails;
                    } else {
                        if($existingMovieDetails->status == 'ongoing'){
                            $this->updateMovieDetailsAttributes($attributes, $arraysToJSON, $existingMovieDetails, $movieDetailsData);
                        }
                    }
        
        if (!empty($batch_movie_details)) {
                MovieDetails::insert($batch_movie_details);
            }
}


protected function updateMovieDetailsAttributes($attributes, $arraysToJSON, $existingMovieDetails, $newMovieData)
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
