<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Jobs\SendMoviesCrawlJob;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class CrawlMovies extends Command
{
    protected $signature = 'movies:crawl';
    protected $description = 'Crawl movies data';
    protected $client;
    protected $base_url;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
        $this->base_url = 'https://ophim1.com/';
    }

    public function handle()
    {
        $this->info('Crawling movies data...');
        $this->crawlMovies();
        $this->info('Movies data crawled successfully.');
    }

    protected function getTotalPages()
    {
        $total_movies = Movie::count();
        return $pagination_data['pagination']['totalPages'];
    }

   
    public function crawlMovies(){
        $total = 10;
        $batchSize = 5;
        $totalExecutionTime = 0;
    
        for ($start = 1; $start <= $total; $start += $batchSize) {
            $end = min($start + $batchSize - 1, $total);
            $batch_movies = [];
            $startTime = microtime(true);
    
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

                //xử lý phim
                $this->processMovies($batch_movies);
                //xử lý chi tiết phim
                // $this->processMovieDetails($batch_movies);
                
                $endTime = microtime(true);
                $executionTime = $endTime - $startTime;
                $totalExecutionTime += $executionTime;
                $this->info("Processed from page $start to page $end in {$executionTime} seconds.");
        }
        $this->info("Total execution time: {$totalExecutionTime} seconds.");
    }
    

    function processMovies($movies_data)
{
    $newMovies = [];
    $attributes = [
        'modified_time', '_id', 'name', 'origin_name', 'thumb_url', 'slug', 'year', 'poster_url'
    ];

    for($i = 0; $i < count($movies_data); $i++){
    foreach ($movies_data[$i] as $result) {
        $existingMovie = Movie::where('_id', $result->_id)->first();
        if (!$existingMovie) {
            $newMovie = [];
            foreach ($attributes as $attribute) {
                $newMovie[$attribute] = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
            }
            $newMovies[] = $newMovie;
        } else {
            $this->updateMovieAttributes($existingMovie, $result, $attributes);
        }
    }
}
    // Chèn nhiều bản ghi một lúc
    if (!empty($newMovies)) {
        Movie::insert($newMovies);
    }
}


protected function updateMovieAttributes($existingMovie, $result, $attributes)
{
    $updateRequired = false;
    foreach ($attributes as $attribute) {
        $newValue = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
        if ($existingMovie->$attribute != $newValue) {
            print_r($existingMovie->$attribute);
            $existingMovie->$attribute = $newValue;
            $updateRequired = true;
        }
    }

    if ($updateRequired) {
        $existingMovie->save();
        // $this->info("Updated movies has _id : {$existingMovie->_id}");
    }
}


protected function processMovieDetails($movies) {
    $attributes = [
        '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
        'trailer_url', 'time', 'episode_current', 'episode_total',
        'quality', 'lang', 'notify', 'showtimes', 'view'
    ];
    $arraysToJSON = ['actor', 'director', 'category', 'country'];
   
        $batch_movie_details = [];
        $flattened_movies = array_reduce($movies, 'array_merge', []);

        foreach($flattened_movies as $movie){
            $_id = $movie->_id;
            $url = $this->base_url . "phim/id/$_id";
            $movie_details_response = $this->client->request('GET', $url);
            if($movie_details_response->getStatusCode() == 200){
            $movie_details_data = json_decode($movie_details_response->getBody())->movie;
            $existingMovieDetails = MovieDetails::where('_id', $movie_details_data->_id)->first();
            if(!$existingMovieDetails){
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
        
        if (!empty($batch_movie_details)) {
                MovieDetails::insert($batch_movie_details);
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
}


//    protected function processMovieDetails($slug) {
//         $movie_details_response = $this->client->request('GET', $this->base_url . 'phim/' . $slug);
//         $movie_details_data = json_decode($movie_details_response->getBody(), true);
//         $movie_data = $movie_details_data['movie'];
//         $episodes_data = $movie_details_data['episodes'];
    
//         if (isset($movie_data)) {
//             $existingMovieDetails = MovieDetails::where('_id', $movie_data['_id'])->first();

//             if(!$existingMovieDetails){
//                 $attributes = [
//                     '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
//                     'trailer_url', 'time', 'episode_current', 'episode_total',
//                     'quality', 'lang', 'notify', 'showtimes', 'view'
//                 ];
            
//                 $newMovieDetails = new MovieDetails();
//                 foreach ($attributes as $attribute) {
//                     $newMovieDetails->$attribute = $movie_data[$attribute];
//                 }
            
//                 $arraysToJSON = ['actor', 'director', 'category', 'country'];
//                 foreach ($arraysToJSON as $arrayAttribute) {
//                     $newMovieDetails->$arrayAttribute = json_encode($movie_data[$arrayAttribute]);
//                 }
//                 $newMovieDetails->save();
//                 $this->processEpisodes($movie_data['_id'], $episodes_data);
//             } else {
//             //nếu chi tiết phim đã tồn tại thì kiểm tra nếu giá trị khác nhau và cập nhật vào db 
//             $this->updateMovieDetailsAttributes($existingMovieDetails, $movie_data, $episodes_data);
//             }
//     }
//     }



    protected function updateMovieDetailsAttributes($existingMovieDetails, $newMovieData, $episodes_data)
{
    $attributes = [
        'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
        'trailer_url', 'time', 'episode_current', 'episode_total',
        'quality', 'lang', 'notify', 'showtimes', 'view'
    ];

    $arraysToJSON = ['actor', 'director', 'category', 'country'];

    $updateRequired = false;

    foreach ($attributes as $attribute) {
        if ($existingMovieDetails->$attribute !== $newMovieData[$attribute]) {
            $existingMovieDetails->$attribute = $newMovieData[$attribute];
            $updateRequired = true;
        }
    }

    foreach ($arraysToJSON as $arrayAttribute) {
        $existingValue = json_encode($existingMovieDetails->$arrayAttribute);
        $newValue = json_encode($newMovieData[$arrayAttribute]);

        if ($existingValue !== $newValue) {
            $existingMovieDetails->$arrayAttribute = $newValue;
            $updateRequired = true;
        }
    }

    if ($updateRequired) {
        $existingMovieDetails->save();
        // $this->info("Updated movie_details has id : {$existingMovieDetails->_id}");
        $this->processEpisodes($newMovieData['_id'], $episodes_data);
    }
}


    protected function processEpisodes($movie_id, $episodes_data){
        $newEpisodes = new Episodes();
        $existingEpisodes = Episodes::where('movie_id', $movie_id)->first();
        if(!$existingEpisodes){
            foreach ($episodes_data as $episodes) {
                $newEpisodes->server_name = $episodes['server_name'];
                $newEpisodes->movie_id = $movie_id;
                $newEpisodes->server_data = json_encode($episodes['server_data']);
                }
                $newEpisodes->save();
        } else {
            
        //nếu episodes phim đã tồn tại thì kiểm tra nếu giá trị khác nhau và cập nhật vào db
        $this->updateEpisodesAttributes($existingEpisodes, $episodes_data); 
        }
    }


    protected function updateEpisodesAttributes($existingEpisodes, $episodes_data){
        $updateRequired = false;
                // Check if server_data is different
                foreach ($episodes_data as $episodes) {
                $serverName = $episodes['server_name'];
                $serverData = json_encode($episodes['server_data']);
                if ($existingEpisodes->server_data !== $serverData) {
                    $existingEpisodes->server_data = $serverData;
                    $existingEpisodes->save();
                    $updateRequired = true;
        } else if($existingEpisodes->server_name !== $serverName){
            $existingEpisodes->server_name = $serverName;
            $existingEpisodes->save();
            $updateRequired = true;
        }
    }

        if ($updateRequired) {
            // $this->info("Updated episodes has movie_id : {$existingEpisodes->movie_id}");
        }
    }


}
