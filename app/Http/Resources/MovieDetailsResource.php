<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieDetailsResource extends JsonResource
{
    protected $imageDomain;
    protected $cloudinaryDomain;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
        $this->cloudinaryDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_350/uploads/movies/";
    }

    public function toArray($request)
{
    $movie = $this['movie'];

    $movieArray = !empty($movie) ? [
        'modified_time' => $movie['modified_time'],
        'id' => $movie['_id'],
        'name' => $movie['name'],
        'slug' => $movie['slug'],
        'origin_name' => $movie['origin_name'],
        'content' => $movie['content'],
        'type' => $movie['type'],
        'status' => $movie['status'],
        'thumb_url' => $this->imageDomain . $movie['poster_url'],
        'poster_url' => $this->imageDomain . $movie['thumb_url'],
        // 'thumb_url' => url($movie['slug'].'-thumb.webp'),
        // 'poster_url' => url($movie['slug'].'-poster.webp?w=320'),
        // 'poster_url' => $this->formatImageUrlv2($movie),
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
        'actor' => json_decode($movie['actor']),
        'director' => json_decode($movie['director']),
        'category' => $this->formattedCategoriesArray($movie, 'category'),
    ] : [];

    return [
        'movie' => $movieArray,
        'episodes' => $this['episodes'] ?? [],
        'seoOnPage' => !empty($movie) ? [
            'seo_title' => $movie['name'] ." - ". $movie['origin_name'] ." (". $movie['year'] .") [". $movie['quality'] ."-". $movie['lang'] ."]",
            'seo_description' => strip_tags($movie['content']), 
            // 'og_image' => $this->imageDomain . $movie['poster_url'],
            // 'og_image' => $this->formatSeoImage($movie),
            'og_image' => url($movie['slug'].'-thumb.webp'),
            'og_url' => $request->path(),
        ] : [],
    ];
}

    protected function formatImageUrlv2($movie)
    {
        $slug = $movie['slug'];
        return ($movie['year'] >= 2022)
        ? $this->cloudinaryDomain . $slug . '-thumb.webp'
        : $this->imageDomain . $slug . '-thumb.jpg'; 
    }

    protected function formatSeoImage($movie)
    {
        $slug = $movie['slug'];
        return $this->imageDomain . $slug . '-poster.jpg';
    }

    protected function formattedArray($movie, $propertyName)
    {
        $propertyValue = $movie[$propertyName];

        return array_map(function ($item) {
                return ['name' => $item['name']];
            }, json_decode($propertyValue, true));
    }


    protected function formattedCategoriesArray($movie, $propertyName)
    {
        $categories = config('api_settings.categories');
        $propertyValue = collect(json_decode($movie[$propertyName], true))->pluck('slug')->toArray();

        $filteredCategories = [];
        foreach ($propertyValue as $categorySlug) {
            if (array_key_exists($categorySlug, $categories)) {
                $filteredCategories[] = ['name' => $categories[$categorySlug]];
            }
        }

        return array_values($filteredCategories);
    }

}
