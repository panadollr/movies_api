<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Models\Movie;
use App\Models\Episode;

class EpisodeController 
{
    protected function getFilteredEpisodes($query){
        $searchTerm = request()->input('searchTerm'); 
        $type = request()->input('type');
        $alphabet = request()->input('alphabet');
        try {
            //theo tên hoặc slug
            if ($searchTerm) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('movies.slug', 'LIKE', "%$searchTerm%")
                      ->orWhere('movies.name', 'LIKE', "%$searchTerm%");
                });
            }

            //theo loại
            if($type){
                if($type == 'single'){
                    $query->where('type', '=', $type);
                } else {
                    $query->where('type', '=', $type)->orWhere('type', 'hoathinh');
                }
            }

            //theo chữ cái
            if ($alphabet) {
                $query->whereRaw("SUBSTRING(name, 1, 1) = ?", [$alphabet]);
            }

            $columns = ['movies._id', 'name', 'movies.slug', 'type', 'episode_current'];
            $result = $query->select($columns)->paginate(8);
            $result->appends(request()->query());

            return $result;
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);   
        }
    }

    public function getEpisodes(){
        try {
            $query = Movie::join('movie_details', 'movie_details._id', '=', 'movies._id')
            ->where('episode_current', '!=', 'Trailer');
            $movies = $this->getFilteredEpisodes($query);
            $episodes = Episode::all();
            return view('admin.episode.episodes', compact('movies', 'episodes'));
            // return $movies;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        
    }

    public function episodeDetail($slug){
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
    
            return view('admin.episode.edit_episode', compact('movieDetail', 'ophimEpisodesV2'));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function updateEpisodes(Request $request, $_id){
        $server2Data = $request->input('server_2');
        foreach($server2Data as $key => $value){
            if ($value !== null && $value !== '') {
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
        
        return response()->json(['msg' => 'Đã cập nhật thành công !'], 200);
    }

}
