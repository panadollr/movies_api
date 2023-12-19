<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\MovieDetails;
use App\Http\Resources\MovieDetailsResource;
use App\Http\Controllers\User\MovieController;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
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
            
            $episodes = $this->getEpisodes($slug);
            
                $data = [
                'movie' => $movieDetails,
                'episodes' => $episodes,
                ];
                return response()->json(new MovieDetailsResource($data), 200);

            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
    }


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
