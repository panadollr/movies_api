<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Episode;
use App\Models\Movie;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class CrawlEpisodes extends Command
{
    protected $signature = 'episodes:crawl';
    protected $description = 'Command description';
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
        $this->info('Crawling episodes data...');
        // $this->crawl();
        $this->crawlAll();
        $this->info('Episodes data crawled successfully.');
    }


    public function crawlAll(){
        $batchSize = 500;
        $totalExecutionTime = 0;
        Movie::chunk($batchSize, function ($movies) use (&$totalExecutionTime) {
            $startTime = microtime(true);

            $batch_movie_slugs = $movies->pluck('slug')->toArray();
            if (!empty($batch_movie_slugs)) {
            $this->processEpisodes($batch_movie_slugs);
            }
        
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            $totalExecutionTime += $executionTime;
        
            $this->info("Processed {$movies->count()} episodes in {$executionTime} seconds.");
        });
        
        $this->info("Total execution time: {$totalExecutionTime} seconds.");
    }


    // protected function crawl()
    // {
    //     $batchSize = 500;
    //     $totalExecutionTime = 0;
    //     Movie::chunk($batchSize, function ($movies) use (&$totalExecutionTime) {
    //         $startTime = microtime(true);

    //         $batch_movie_slugs = $movies->pluck('slug')->toArray();
    //         if (!empty($batch_movie_slugs)) {
    //         $this->processEpisodes($batch_movie_slugs);
    //         }
        
    //         $endTime = microtime(true);
    //         $executionTime = $endTime - $startTime;
    //         $totalExecutionTime += $executionTime;
        
    //         $this->info("Processed {$movies->count()} movies in {$executionTime} seconds.");
    //     });
        
    //     $this->info("Total execution time: {$totalExecutionTime} seconds.");
    // }


    protected function processEpisodes($batch_movie_slugs)
{
    try {
        $promises = [];
        foreach ($batch_movie_slugs as $slug) {
            $url = $this->base_url . "phim/$slug";
            $promises[] = $this->client->getAsync($url);
        }
        $responses = Promise\settle($promises)->wait();
        $batch_episodes = [];
        foreach ($responses as $response) {
            if ($response['state'] === 'fulfilled' && $response['value']->getStatusCode() === 200) {
                $decodedResponse = json_decode($response['value']->getBody());
                $episodes_data = $decodedResponse->episodes;
                $movie_id = $decodedResponse->movie->_id;
                    foreach ($episodes_data as $data) {
                            $newEpisodes = [
                                'movie_id' => $movie_id,
                                'server_name' => $data->server_name,
                                'server_data' => json_encode($data->server_data),
                            ];
    
                            $batch_episodes[] = $newEpisodes;
                        }
        }
        }
    
        if (!empty($batch_episodes)) {
            Episode::insert($batch_episodes);
        }   
    } catch (\Throwable $th) {
        $this->info($th->getMessage());
    }
}
    

    // protected function processEpisodes($movie_id, $episodes_data){
    //     $newEpisodes = new Episodes();
    //     $existingEpisodes = Episodes::where('movie_id', $movie_id)->first();
    //     if(!$existingEpisodes){
    //         foreach ($episodes_data as $episodes) {
    //             $newEpisodes->server_name = $episodes['server_name'];
    //             $newEpisodes->movie_id = $movie_id;
    //             $newEpisodes->server_data = json_encode($episodes['server_data']);
    //             }
    //             $newEpisodes->save();
    //     } else {
            
    //     //nếu episodes phim đã tồn tại thì kiểm tra nếu giá trị khác nhau và cập nhật vào db
    //     $this->updateEpisodesAttributes($existingEpisodes, $episodes_data); 
    //     }
    // }


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
