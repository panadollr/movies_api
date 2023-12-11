<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Http\Resources\MovieDetailsResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationResource;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class MovieDetailsController
{
    protected $client;
    protected $movieDetailWithMovieQuery; 

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->movieDetailWithMovieQuery = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id');
    }

    protected function getEpisodes($slug){
        $url = "https://ophim1.com/phim/$slug";
    try {
        $response = $this->client->get($url);
        $episodes = json_decode($response->getBody()->getContents())->episodes;
        return $episodes ?? null;
    } catch (\Throwable $th) {
        return null;
    }
    }

    public function getMovieDetails($slug){
        try {
            $movieDetails = $this->movieDetailWithMovieQuery
                            ->where('slug', $slug)
                            ->select('movies.*', 'movie_details.*')->first();
            if(!$movieDetails){
                return response()->json(['error' => 'Phim không tồn tại!'], 404);
            }
            $episodes = $this->getEpisodes($slug);

            $data = [
               'movie' => $movieDetails,
               'episodes' => $episodes
            ];
            
            return response()->json(new MovieDetailsResource($data), 200);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
    }


    //CÁC PHIM TƯƠNG TỰ
    public function getSimilarMovies($slug){
        $movieDetail = $this->movieDetailWithMovieQuery
                            ->select('movies.slug', 'movie_details.type')
                            ->where('slug', $slug)
                            ->first();
        $similarMovies = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id')
        ->select('movies._id', 'movies.name', 'movies.slug', 'movies.year', 'movies.thumb_url', 'movie_details.status', 'movie_details.episode_current')
        ->where('movies.slug', '!=', $movieDetail->slug)->where('movie_details.status', '!=', 'trailer')
        ->where('type', $movieDetail->type)->paginate(10);          
        return new PaginationResource(MovieResource::collection($similarMovies)); 
    }

}
