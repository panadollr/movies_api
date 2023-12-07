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
use App\Models\Episode;

class SendMoviesCrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $base_url;

    public function __construct()
    {
        $this->base_url = 'https://ophim1.com/';
    }

    public function handle()
    {
        // $totalExecutionTime = 0;
        // $startTime = microtime(true);
        // $this->info('Crawling movie details data...');
        $this->crawl();
        // $this->info('Movie details data crawled successfully.');
        // $endTime = microtime(true);
        // $executionTime = $endTime - $startTime;
        // $totalExecutionTime += $executionTime;
        // $this->info("\nTotal execution time: {$totalExecutionTime} seconds.");
    }

    // protected function processMovies($movies_data)
    // {
    //     $attributes = [
    //         'modified_time', '_id', 'name', 'origin_name', 'thumb_url', 'slug', 'year', 'poster_url'
    //     ];
    
    //     foreach ($movies_data->items as $result) {
    //         $existingMovie = Movie::where('_id', $result->_id)->first();
    //         //nếu chưa tồn tại, thêm vào db
    //         if (!$existingMovie) {
    //             $newMovie = new Movie();
    //             foreach ($attributes as $attribute) {
    //                 $newMovie->$attribute = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
    //             }
    //             $newMovie->save();
    //             $this->processMovieDetails($newMovie->slug);
    //         } else {
              
    //         }
    //     }
    // }


    // public function processMovieDetails($slug) {
    //     $client = new Client();
    //     $base_url = "https://ophim1.com/";
    //     $movie_details_response = $client->request('GET', $base_url . 'phim/' . $slug);
    //     $movie_details_data = json_decode($movie_details_response->getBody(), true);
    //     $movie_data = $movie_details_data['movie'];
    //     $episodes_data = $movie_details_data['episodes'];
    
    //     if (isset($movie_data)) {
    //     $attributes = [
    //         '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
    //         'trailer_url', 'time', 'episode_current', 'episode_total',
    //         'quality', 'lang', 'notify', 'showtimes', 'view'
    //     ];
    
    //     $newMovieDetails = new MovieDetails();
    //     foreach ($attributes as $attribute) {
    //         $newMovieDetails->$attribute = $movie_data[$attribute];
    //     }
    
    //     $arraysToJSON = ['actor', 'director', 'category', 'country'];
    //     foreach ($arraysToJSON as $arrayAttribute) {
    //         $newMovieDetails->$arrayAttribute = json_encode($movie_data[$arrayAttribute]);
    //     }
    
    //     $newMovieDetails->save();
    //     $this->processMovieEpisodes($movie_data['_id'], $episodes_data);
    // }
    // }

    // public function processMovieEpisodes($movie_id, $episodes_data){
    //     $newEpisodes = new Episodes();
    //     foreach ($episodes_data as $episodes) {
    //     $newEpisodes->server_name = $episodes['server_name'];
    //     $newEpisodes->movie_id = $movie_id;
    //     $newEpisodes->server_data = json_encode($episodes['server_data']);
    //     }
    //     $newEpisodes->save();
    // }


    public function crawl()
{
    $client = new Client();
    $batchSize = 120;
    $batchMovieSlugs = Movie::take($batchSize)->pluck('slug')->toArray();
    
    if (!empty($batchMovieSlugs)) {
        foreach ($batchMovieSlugs as $slug) {
            $url = $this->base_url . "phim/$slug";
            $response = $client->get($url);

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
