<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieDetailResourceV2 extends JsonResource
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
    $ophimEpisodes = $this['ophimEpisodes'];
    $dbEpisodes = $this['dbEpisodes'];
    $episodeSlug = $this['episodeSlug'];
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
        'thumb_url' => $this->formatOphimImageUrl($movie['poster_url']),
        'poster_url' => $this->formatOphimImageUrl($movie['thumb_url']),
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

    $episodes = $this->formattedEpisodes($ophimEpisodes, $dbEpisodes);
    $episodeCurrent = $this->getEpisodeCurrent($episodes, $episodeSlug);
    $episodeCurrentName = $episodeCurrent['name'];

    return [
        'episodeCurrent' => $episodeCurrent,
        'movie' => $movieArray,
        'episodes' => $episodes,
        'seoOnPage' => !empty($movie) ? [
            'seo_title2' => $this->formattedSeoTitle($movie, $episodeCurrentName),
            'seo_title' => $movie['name'] ." - ". $movie['origin_name'] ." (". $movie['year'] .") [". $movie['quality'] ."-". $movie['lang'] ."]" ." - tập",
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

protected function formattedEpisodes($formattedOphimEpisodes, $dbEpisodes){
    $episodes = [];
    foreach ($formattedOphimEpisodes as $formattedOphimEpisode) {
        $episode = [
            'name' => $formattedOphimEpisode['name'],
            'slug' => $formattedOphimEpisode['slug'],
            'link_m3u8' => $formattedOphimEpisode['link_m3u8'],
            'links' => [],
        ];

        $defaultLink = [
            "server_name" => "Vietsub #1",
            "link_embed" => $formattedOphimEpisode['link_embed'],
        ];

        $episode['links'][] = $defaultLink;
        $servers = []; 

        foreach ($dbEpisodes as $dbEpisode) {
            $serverName = $dbEpisode['server_name'];
            $slug = $dbEpisode['slug'];
            $link_embed = $dbEpisode['link'];

            if (!isset($servers[$serverName]) && $formattedOphimEpisode['slug'] === $slug) {
                $link = [
                    "server_name" => $serverName,
                    "link_embed" =>  $link_embed,
                ];

                $episode['links'][] = $link;
                $servers[$serverName] = true;
            }
        }

        $episodes[] = $episode;
    }

    return $episodes;
}

protected function getEpisodeCurrent($episodes, $episodeSlug) {
    foreach ($episodes as $episode) {
        if (isset($episode['slug']) && $episode['slug'] === $episodeSlug) {
            return $episode;
        }
    }
    return null;
}

protected function formattedSeoTitle($movie, $episodeCurrentName){
    return "{$movie['name']} - {$movie['origin_name']} - ({$movie['year']}) [{$movie['quality']} - {$movie['lang']}] - Tập $episodeCurrentName";
}


}
