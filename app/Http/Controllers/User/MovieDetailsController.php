<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\MovieDetails;
use App\Http\Controllers\User\MovieController;
use App\Http\Resources\MovieDetailResource;
use App\Models\Episode;
use App\Models\EpisodeV2;
use App\Models\Movie;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class MovieDetailsController
{
    protected $client;
    protected $movieDetailWithMovieQuery;
    protected $movieController;

    public function __construct(Client $client, MovieController $movieController)
    {
        $this->client = $client;
        $this->movieDetailWithMovieQuery = MovieDetails::join('movies', 'movies._id', '=', 'movie_details._id');
        // $this->movieDetailWithMovieQuery = MovieDetails::with('movie');
        $this->movieController = $movieController;
    }

    public function getOphimEpisodes($slug)
{
    $url = "https://ophim1.com/phim/$slug";
    try {
        $response = $this->client->get($url);
        $responseContent = json_decode($response->getBody()->getContents())->episodes[0];
        $episodes = $responseContent->server_data;
        $ophimEpisodes = array_map([$this, 'mapEpisode'], $episodes);

        if (count($ophimEpisodes) > 1) {
            usort($ophimEpisodes, function ($a, $b) {
                return $a['name'] - $b['name'];
            });
        }
        return $ophimEpisodes;
    } catch (\Throwable $th) {
        return $this->getDefaultEpisode();
    }
}

private function mapEpisode($episode)
{
    $isFull = strtolower($episode->name) == 'full' || strtolower($episode->slug) == 'full';
    $ophimEpisodeName = $isFull ? strtolower($episode->slug ?: $episode->name) : (int) ($episode->slug ?: $episode->name);
    $ophimEpisodeSlug = "tap-" . $ophimEpisodeName;
    
    return [
        "name" =>  strval($ophimEpisodeName),
        "slug" => $ophimEpisodeSlug,
        "link_m3u8" => $episode->link_m3u8,
        "link_embed" => $episode->link_embed,
    ];
}

private function getDefaultEpisode()
{
    return [
        "name" => "",
        "slug" => "",
        "link_m3u8" => "",
        "link_embed" => "",
    ];
}

    // public function getMovieDetailsV1($slug){
    //     // $cacheKey = 'movie_details_' . $slug;
        
    //     // return Cache::remember($cacheKey, 1800, function () use ($slug) {
    //     try {
    //         $movieDetails = $this->movieDetailWithMovieQuery
    //                         ->where('slug', $slug)->first();
    //         if(!$movieDetails){
    //             $data = [
    //                 'movie' => [],
    //                 'ophim_episodes' => [],
    //                 'db_episodes' => []
    //                 ];
    //             return response()->json(new MovieDetailsResource($data), 200);
    //         }
    //             $ophimEpisodes = $this->getOphimEpisodes($slug);
    //             $db_episodes = Episode::where('_id', $movieDetails->_id)->select('slug', 'server_2')
    //             ->get()->keyBy('slug')->toArray();
            
    //             $data = [
    //             'movie' => $movieDetails,
    //             'ophim_episodes' => $ophimEpisodes,
    //             'db_episodes' => $db_episodes
    //             ];
    //             return response()->json(new MovieDetailsResourceV1($data), 200);

    //         } catch (\Throwable $th) {
    //             return response()->json(['error' => $th->getMessage()], 500);
    //         }
    //     // });
    // }

    public function getMovieDetail($slug, $episodeSlug = null){
        try {
            $movieDetail = $this->movieDetailWithMovieQuery
                            ->where('slug', $slug)->first();
        
            $emptyMovieDetail = [
                'movie' => [],
                'ophimEpisodes' => [],
                'dbEpisodes' => [],
                'episodeSlug' => null
                ];

            if(!$movieDetail){
                return response()->json(new MovieDetailResource($emptyMovieDetail), 200);
            }
            
            if($movieDetail->status == 'trailer' || $movieDetail->episode_current == 'Trailer'){
                $episodeSlug = null;
            }
            
                $ophimEpisodes = $this->getOphimEpisodes($slug);
                $episodeSlug = $episodeSlug ?? $ophimEpisodes[0]['slug'];
                $episodeSlugs = array_column($ophimEpisodes, 'slug');
                $matchEpisodeSlug = in_array($episodeSlug, $episodeSlugs);
                if(!$matchEpisodeSlug){
                    return response()->json(new MovieDetailResource($emptyMovieDetail), 200);
                }

                $dbEpisodes = EpisodeV2::join('episode_servers', 'episode_servers.server_id', '=', 'episodes_v2.server_id')
                ->where('movie_id', $movieDetail->_id)
                ->get();
            
                $data = [
                'movie' => $movieDetail,
                'ophimEpisodes' => $ophimEpisodes,
                'dbEpisodes' => $dbEpisodes,
                'episodeSlug' => $episodeSlug,
                ];

                return response()->json(new MovieDetailResource($data), 200);
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()], 500);
            }
    }

    //CÁC PHIM TƯƠNG TỰ
    public function getSimilarMovies($slug){
        try {
            $title = "Các phim tương tự";
            $description = "";
            $movieDetail = $this->movieDetailWithMovieQuery
                                ->where('slug', $slug)
                                ->select('slug', 'type', 'category', 'country')
                                ->first();
    
            $similarMoviesQuery = $this->movieController->moviesWithNoTrailer
                ->select($this->movieController->selectedColumnsV2);
    
            if ($movieDetail) {
                $similarMoviesQuery->where('movies.slug', '!=', $movieDetail->slug)
                ->where(function ($query) use ($movieDetail) {
                    $query->where('type', $movieDetail->type)
                        ->where('category', $movieDetail->category)
                        ->orWhere('country', $movieDetail->country);
                });
            }
    
            $similarMovies = $similarMoviesQuery;
    
            return $this->movieController->getMoviesByFilter($similarMovies, 10, $title, $description);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

}
