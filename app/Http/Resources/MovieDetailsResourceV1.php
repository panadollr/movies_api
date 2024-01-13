<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Controllers\User\MovieController;

class MovieDetailsResourceV1 extends JsonResource
{
    protected $imageDomain;
    protected $cloudinaryPosterDomain;
    protected $cloudinaryThumbDomain;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
        $this->cloudinaryPosterDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_300/uploads/movies/";
        $this->cloudinaryThumbDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_320/uploads/movies/";
    }

    public function toArray($request)
{
    $movie = $this['movie'];
    $ophimEpisodes = $this['ophim_episodes'];
    $db_episodes = $this['db_episodes'];
    $categoryConfig = config('api_settings.categories');

    $movieArray = !empty($movie) ? [
        'modified_time' => $movie['modified_time'],
        'id' => $movie['_id'],
        'name' => $movie['name'],
        'slug' => $movie['slug'],
        'origin_name' => $movie['origin_name'],
        'content' => $movie['content'],
        'type' => $movie['type'],
        'status' => $movie['status'],
        // 'thumb_url' => $this->imageDomain . $movie['poster_url'],
        // 'poster_url' => $this->imageDomain . $movie['thumb_url'],
        'thumb_url' => $this->formatOphimImageUrl($movie['poster_url']),
        'poster_url' => $this->formatOphimImageUrl($movie['thumb_url']),
        // 'thumb_url' => $this->formatImageWithCloudinaryUrl($movie, 'thumb'),
        // 'poster_url' => $this->formatImageWithCloudinaryUrl($movie, 'poster'),
        'is_copyright' => $movie['is_copyright'],
        'sub_docquyen' => (bool) $movie['sub_docquyen'],
        'trailer_url' => $movie['trailer_url'],
        'time' => $movie['time'],
        'episode_current' => $movie['episode_current'] === 'Tập 0'? 'Trailer' : $movie['episode_current'],
        'episode_total' => $movie['episode_total'],
        'quality' => $movie['quality'],
        'lang' => $movie['lang'],
        'notify' => $movie['notify'],
        'showtimes' => $movie['showtimes'],
        'year' => $movie['year'],
        'view' => $movie['view'],
        'category' => $this->formattedJsonWithConfig($movie['category'], $categoryConfig),
    ] : [];

    $formattedEpisodes = $this->formattedEpisodes($ophimEpisodes, $db_episodes, $movie);

    return [
        'movie' => $movieArray,
        // 'episodes' => $this['episodes'] ?? [],
        'episodes' => $formattedEpisodes,
        'seoOnPage' => !empty($movie) ? [
            // 'seo_title2' => $this->formattedSeoTitle($movie, $formattedCurrenEpisode),
            'seo_title' => $movie['name'] ." - ". $movie['origin_name'] ." (". $movie['year'] .") [". $movie['quality'] ."-". $movie['lang'] ."]" ." - ". count($ophimEpisodes) . ' tập',
            'seo_description' => strip_tags($movie['content']), 
            'thumb_url' => $this->formatOphimImageUrl($movie['poster_url']),
            // 'og_image' => $this->formatImageWithCloudinaryUrl($movie, 'thumb'),
            'og_url' => $request->path(),
        ] : [],
    ];
}

//poster va thumbnail ophim
protected function formatOphimImageUrl($url)
{
        return $url ? "https://ophim9.cc/_next/image?url=http%3A%2F%2Fimg.ophim1.com%2Fuploads%2Fmovies%2F$url&w=256&q=75" : null;
}

    protected function formatImageWithCloudinaryUrl($movie, $type)
    {
        $slug = $movie['slug'];
        $cloudinaryFormat = "-$type.webp";
        $ophimFormat = "-$type.jpg";
        if ($type == 'thumb') {
            if($movie['year'] == 2023){
                $imageUrl = $this->cloudinaryThumbDomain . $slug . $cloudinaryFormat;
            }else {
                $imageUrl = $this->imageDomain . $slug . $ophimFormat;
            }
        } else {
            $imageUrl = $this->cloudinaryPosterDomain . $slug . $cloudinaryFormat;
        }
        
        return $imageUrl;
    }

protected function formattedJsonWithConfig($jsonData, $arrayConfig)
{
    $propertyValue = collect(json_decode($jsonData, true))->pluck('slug')->toArray();
    $filteredArrayConfig = array_intersect_key($arrayConfig, array_flip($propertyValue));
    $formattedJsonData = array_map(function ($name) {
        return ['name' => $name];
    }, $filteredArrayConfig);

    return array_values($formattedJsonData);
}


protected function formattedEpisodes($ophimEpisodes, $db_episodes)
{
    $ophimEpisodesV2 = [];
    foreach ($ophimEpisodes as $ophimEpisode) {

        $episodeV2 = [
            "name" => $ophimEpisode['name'],
            "slug" => $ophimEpisode['slug'],
            "link_m3u8" => $ophimEpisode['link_m3u8'],
            "server_1" => $ophimEpisode['link_embed'],
            "server_2" => $db_episodes[$ophimEpisode['slug']]['server_2'] ?? "",
        ];

        $ophimEpisodesV2[] = $episodeV2;
    }

    if (count($ophimEpisodesV2) > 1) {
        usort($ophimEpisodesV2, function ($a, $b) {
            return $a['name'] - $b['name'];
        });
    }
    
    return $ophimEpisodesV2;
}


}
