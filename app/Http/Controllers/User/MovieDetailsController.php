<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\MovieDetails;
use App\Http\Resources\MovieDetailsResource;
use App\Http\Controllers\User\MovieController;
use App\Models\Episode;

use GuzzleHttp\Client;

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


    public function getOphimEpisodes($slug){
        $url = "https://ophim1.com/phim/$slug";
    try {
        $response = $this->client->get($url);
        $ophimEpisodes = json_decode($response->getBody()->getContents())->episodes[0]->server_data;

        return array_map(function($episode) {
            return [
                "name" => $episode->name,
                "slug" => $episode->slug,
                "link_m3u8" => $episode->link_m3u8,
                "link_embed" => $episode->link_embed,
            ];
        }, $ophimEpisodes);

    } catch (\Throwable $th) {
        return [
            "slug" => "",
            "link_m3u8" => "",
            "link_embed" => "",
        ];
            }
    }

    public function getMovieDetails($slug){
        // $cacheKey = 'movie_details_' . $slug;
        
        // return Cache::remember($cacheKey, 1800, function () use ($slug) {
        try {
            $movieDetails = $this->movieDetailWithMovieQuery
                            ->where('slug', $slug)->first();
            if(!$movieDetails){
                $data = [
                    'movie' => [],
                    'ophim_episodes' => [],
                    'db_episodes' => []
                    ];
                return response()->json(new MovieDetailsResource($data), 200);
            }
                $ophimEpisodes = $this->getOphimEpisodes($slug);
                $db_episodes = Episode::where('_id', $movieDetails->_id)->select('slug', 'server_2', 'server_3')
                ->get()->keyBy('slug')->toArray();
            
                $data = [
                'movie' => $movieDetails,
                'ophim_episodes' => $ophimEpisodes,
                'db_episodes' => $db_episodes
                ];
                return response()->json(new MovieDetailsResource($data), 200);

            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
        // });
    }


    //CÁC PHIM TƯƠNG TỰ
    public function getSimilarMovies($slug){
        try {
            $title = "Các phim tương tự";
            $description = "";
            $movieDetail = $this->movieDetailWithMovieQuery
                                ->where('slug', $slug)
                                ->select('type')
                                ->first();
    
            $similarMoviesQuery = $this->movieController->moviesWithNoTrailer
                ->select($this->movieController->selectedColumnsV2);
    
            if ($movieDetail) {
                $similarMoviesQuery->where('type', $movieDetail->type);
            }
    
            $similarMovies = $similarMoviesQuery;
    
            return $this->movieController->getMoviesByFilter($similarMovies, 10, $title, $description);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getTotalMovieDetails(){
        return MovieDetails::count();
    }

}
