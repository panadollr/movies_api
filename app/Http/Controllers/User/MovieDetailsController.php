<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\MovieDetails;
use App\Http\Resources\MovieDetailsResource;
use App\Http\Controllers\User\MovieController;

use GuzzleHttp\Client;
use Carbon\Carbon;
use DB;

class MovieDetailsController
{
    protected $client;
    protected $movieDetailWithMovieQuery;
    protected $movieController;

    public function __construct(Client $client, MovieController $movieController)
    {
        $this->client = $client;
        $this->movieDetailWithMovieQuery = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id');
        $this->movieController = $movieController;
    }

    public function getEpisodes($slug){
        $url = "https://ophim1.com/phim/$slug";
    try {
        $response = $this->client->get($url);
        $episodes = json_decode($response->getBody()->getContents())->episodes;
        return $episodes;
    } catch (\Throwable $th) {
        // return null;
    }
    }

    public function getMovieDetails($slug){
        $cacheKey = 'movie_details_' . $slug;
        
        // return Cache::remember($cacheKey, 1800, function () use ($slug) {
        try {
            $movieDetails = $this->movieDetailWithMovieQuery
                            ->where('slug', $slug)->first();
            if(!$movieDetails){
                $data = [
                    'movie' => [],
                    'episodes' => [],
                    ];
                return response()->json(new MovieDetailsResource($data), 200);
            }
            if($movieDetails->episode_current != 'Trailer'){
                $episodes = $this->getEpisodes($slug);
            }else {
                $episodes = [];
            }
            
            
                $data = [
                'movie' => $movieDetails,
                'episodes' => $episodes,
                ];
                return response()->json(new MovieDetailsResource($data), 200);

            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
        // });
    }

    // public function getResponseData($slug){
    //     $url = "https://ophim1.com/phim/$slug";
    // try {
    //     $response = $this->client->get($url);
    //     $response_data = json_decode($response->getBody()->getContents());
    //     $movie = $response_data->movie;
    //     return $response_data = [
    //        'trailer_url' => $movie->trailer_url,
    //        'time' => $movie->time,
    //        'episode_current' => $movie->episode_current,
    //        'episode_total' => $movie->episode_total,
    //        'showtimes' => $movie->showtimes,
    //        'view' => $movie->view,
    //        'episodes' => $response_data->episodes,
    //     ];
    // } catch (\Throwable $th) {
    //     // return null;
    // }
    // }

    // public function getMovieDetails($slug){
    //     $cacheKey = 'movie_details_' . $slug;
        
    //     return Cache::remember($cacheKey, 1800, function () use ($slug) {
    //     try {
    //         $movieDetail = $this->movieDetailWithMovieQuery->where('slug', $slug)->first();
    //         if(!$movieDetail){
    //             $data = [
    //                 'movie' => [],
    //                 'episodes' => [],
    //                 ];
    //             return response()->json(new MovieDetailsResource($data), 200);
    //         }
            
    //         $response_data = $this->getResponseData($slug);
    //         $this->updateMovieDetail($movieDetail, $response_data);
            
    //             $data = [
    //             'movie' => $movieDetail,
    //             'episodes' => $response_data['episodes'],
    //             ];
    //             return response()->json(new MovieDetailsResource($data), 200);

    //         } catch (\Throwable $th) {
    //             return response()->json(['error' => $th->getMessage()], 500);
    //         }
    //     });
    // }

    // public function updateMovieDetail($movieDetail, $response_data){
    //     $attributes = [
    //         'trailer_url', 'time', 'episode_current', 'episode_total','showtimes', 'view'
    //     ];
    //     $updates = [];

    //     foreach ($attributes as $attribute) {
    //         if ($movieDetail->$attribute !== $response_data[$attribute]) {
    //             $updates[$attribute] = $response_data[$attribute];
    //         }
    //     }

    //     if (!empty($updates)) {
    //         MovieDetails::where('_id', $movieDetail->_id)->update($updates);
    //     }
    // }

    //CÁC PHIM TƯƠNG TỰ
    public function getSimilarMovies($slug){
        $title = "Các phim tương tự";
        $description = "";
        $movieDetail = $this->movieDetailWithMovieQuery
                            ->where('slug', $slug)->select('type')
                            ->first();
        $similarMovies =  $this->movieController->moviesWithNoTrailer
        ->where('type', $movieDetail->type)->select($this->movieController->selectedColumnsV2);           
        return $this->movieController->getMoviesByFilter($similarMovies, 10, $title, $description);
    }

}
