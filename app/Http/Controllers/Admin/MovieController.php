<?php

namespace App\Http\Controllers\Admin;

use App\Models\Movie;
use App\Models\MovieDetails;
use App\Models\Episode;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class MovieController
{
    protected function getMovies($query){
        $year = request()->input('year');
        $slug = request()->input('slug');
        try {
            //theo năm
            if ($year) {
                $query->where('movies.year', '=', $year);
            }

            //theo slug
            if($slug){
                $query->where('movies.slug', '=', $slug);
            }

            $columns = ['movies._id', 'name', 'slug', 'year', 'episode_current'];
            $result = $query->select($columns)->paginate(6);
        

            return $result;
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);   
        }
    }

    public function editMovies(){
        try {
            $query = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
            ->where('episode_current', '!=', 'Trailer');
            $movies = $this->getMovies($query);
            $episodes = Episode::all();
            return view('edit_movies', compact('movies', 'episodes'));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        
    }

    public function episodeMovieDetail($slug){
        try {
            $movieDetail = Movie::where('slug', $slug)->select('_id', 'name')->first();
            
            if (!$movieDetail) {
                return response()->json(['error' => 'Movie not found.'], 404);
            }
    
            $db_episodes = Episode::where('_id', $movieDetail->_id)->select('slug', 'server_2', 'server_3')
                    ->get()->keyBy('slug')->toArray();
    
            $cacheKey = "ophim_episodes_{$slug}";
            $ophimEpisodes = Cache::remember($cacheKey, now()->addHours(1), function () use ($slug) {
                $url = "https://ophim1.com/phim/$slug";
                $client = new Client();
                $response = $client->get($url);
                return json_decode($response->getBody()->getContents())->episodes[0]->server_data;
                    });

            $ophimEpisodesFormatted = array_map(function($episode) {
                return [
                    "name" => $episode->name,
                    "slug" => $episode->slug,
                    "link_m3u8" => $episode->link_m3u8,
                    "link_embed" => $episode->link_embed,
                ];
            }, $ophimEpisodes);
            
            $ophimEpisodesV2 = [];
        foreach ($ophimEpisodesFormatted as $ophimEpisode) {
            $ophimEpisodeSlug = "tap-" . ($ophimEpisode['slug'] ?: $ophimEpisode['name']);
            
            $episodeV2 = [
                "slug" => $ophimEpisodeSlug,
                "link_m3u8" => $ophimEpisode['link_m3u8'],
                "server_1" => $ophimEpisode['link_embed'],
                "server_2" => $db_episodes[$ophimEpisodeSlug]['server_2'] ?? "",
                "server_3" => $db_episodes[$ophimEpisodeSlug]['server_3'] ?? "",
            ];
    
            $ophimEpisodesV2[] = $episodeV2;
        }

        if(count($ophimEpisodesV2) > 1){
            usort($ophimEpisodesV2, function ($a, $b) {
            return strnatcmp($a['slug'], $b['slug']);
        });
        }
    
            return view('edit_movie_detail', compact('movieDetail', 'ophimEpisodesV2'));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function updateEpisodes(Request $request, $_id){
        // $episodes = Episode::where('_id', $_id)->get();
        $server2Data = $request->input('server_2');
        foreach($server2Data as $key => $value){
            if ($value !== null) {
            $episode = Episode::where('_id', $_id)->where('slug', $key)->first();
            if(!$episode){
                // nếu episode chưa tồn tại thì insert
                $newEpisode = [
                    '_id' => $_id,
                    'slug' => $key,
                    'server_2' => $value,
                ];
                Episode::insert($newEpisode);
            } else {
                $server_2 = $episode->server_2;
                if($value != $server_2){
                    Episode::where('_id', $_id)->where('slug', $key)->update(['server_2' => $value]);
                }
            }
        }
        }
        return 'success';
    }

}
