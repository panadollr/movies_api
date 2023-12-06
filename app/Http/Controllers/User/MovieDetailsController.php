<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episodes;
use App\Http\Resources\MovieDetailsResource;
use App\Http\Resources\PaginationResource;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class MovieDetailsController
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
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
            $movieDetails = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id')
                            ->where('slug', $slug)
                            ->select('movies.*', 'movie_details.*')->first();
            if(!$movieDetails){
                return response()->json(['error' => 'Phim không tồn tại!'], 404);
            }
            $episodes = $this->getEpisodes($slug);
            $data = [
               'movie' => $movieDetails,
               'episodes' => $episodes ?? null
            ];
           
            return response()->json(new MovieDetailsResource($data), 200);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
    }
}
