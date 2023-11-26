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
use App\Models\Episodes;

class SendMoviesCrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startPage;
    protected $endPage;

    public function __construct($startPage, $endPage)
    {
        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }

    public function handle()
    {
        // $client = new Client();
        // $url = $this->base_url . "danh-sach/phim-moi-cap-nhat?page=" . $this->page;
        // $response = $client->request('GET', $url);
        // $movies_data = json_decode($response->getBody());
        $client = new Client();
        $base_url = "https://ophim1.com/";

        for ($page = $this->startPage; $page <= $this->endPage; $page++) {
            $url = $base_url . "danh-sach/phim-moi-cap-nhat?page=$page";
            $movies_response = $client->request('GET', $url);
            $movies_data = json_decode($movies_response->getBody());

            $this->processMovies($movies_data);
        }
    }

    protected function processMovies($movies_data)
    {
        //  foreach ($movies_data->items as $result) {
        //     $existingMovie = Movie::where('_id', $result->_id)->first();
        //     if (!$existingMovie) {
        //         $newMovie = new Movie();
        //         $newMovie->modified_time = $result->modified->time;
        //         $newMovie->_id = $result->_id;
        //         $newMovie->name = $result->name;
        //         $newMovie->origin_name = $result->origin_name;
        //         $newMovie->thumb_url = $result->thumb_url;
        //         $newMovie->slug = $result->slug;
        //         $newMovie->year = $result->year;
        //         $newMovie->poster_url = $result->poster_url;
        //         $newMovie->save();
        //         $this->processMovieDetails($newMovie->slug);
        //     }
        // }
        $attributes = [
            'modified_time', '_id', 'name', 'origin_name', 'thumb_url', 'slug', 'year', 'poster_url'
        ];
    
        foreach ($movies_data->items as $result) {
            $existingMovie = Movie::where('_id', $result->_id)->first();
            //nếu chưa tồn tại, thêm vào db
            if (!$existingMovie) {
                $newMovie = new Movie();
                foreach ($attributes as $attribute) {
                    $newMovie->$attribute = $attribute === 'modified_time' ? $result->modified->time : $result->$attribute;
                }
                $newMovie->save();
                $this->processMovieDetails($newMovie->slug);
            } else {
                //nếu phim đã tồn tại thì kiểm tra nếu giá trị khác nhau và cập nhật vào db 
                // $this->updateMovieAttributes($existingMovie, $result, $attributes);
            }
        }
    }


    public function processMovieDetails($slug) {
        $client = new Client();
        $base_url = "https://ophim1.com/";
        $movie_details_response = $client->request('GET', $base_url . 'phim/' . $slug);
        $movie_details_data = json_decode($movie_details_response->getBody(), true);
        $movie_data = $movie_details_data['movie'];
        $episodes_data = $movie_details_data['episodes'];
    
        if (isset($movie_data)) {
        $attributes = [
            '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
            'trailer_url', 'time', 'episode_current', 'episode_total',
            'quality', 'lang', 'notify', 'showtimes', 'view'
        ];
    
        $newMovieDetails = new MovieDetails();
        foreach ($attributes as $attribute) {
            $newMovieDetails->$attribute = $movie_data[$attribute];
        }
    
        $arraysToJSON = ['actor', 'director', 'category', 'country'];
        foreach ($arraysToJSON as $arrayAttribute) {
            $newMovieDetails->$arrayAttribute = json_encode($movie_data[$arrayAttribute]);
        }
    
        $newMovieDetails->save();
        $this->processMovieEpisodes($movie_data['_id'], $episodes_data);
    }
    }

    public function processMovieEpisodes($movie_id, $episodes_data){
        $newEpisodes = new Episodes();
        foreach ($episodes_data as $episodes) {
        $newEpisodes->server_name = $episodes['server_name'];
        $newEpisodes->movie_id = $movie_id;
        $newEpisodes->server_data = json_encode($episodes['server_data']);
        }
        $newEpisodes->save();
    }



}
