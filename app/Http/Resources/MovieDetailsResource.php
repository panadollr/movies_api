<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        $imageDomain = config('api_settings.image_domain');
        $movie = $this['movie'];

        return [
            'movie' => [
                'modified_time' => $movie['modified_time'],
                'id' => $movie['_id'],
                'name' => $movie['name'],
                'slug' => $movie['slug'],
                'origin_name' => $movie['origin_name'],
                'content' => $movie['content'],
                'type' => $movie['type'],
                'status' => $movie['status'],
                'thumb_url' => $imageDomain . $movie['poster_url'],
                'poster_url' => $imageDomain . $movie['thumb_url'],
                'is_copyright' => $movie['is_copyright'],
                'sub_docquyen' => (bool) $movie['sub_docquyen'],
                'trailer_url' => $movie['trailer_url'],
                'time' => $movie['time'],
                'episode_current' => $movie['episode_current'],
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
                // 'country' => $this->formattedArray($movie, 'country'),
            ],
            'episodes' => $this['episodes'],
            'seoOnPage' => [
                'seo_title' => $movie['name'] ." - ". $movie['origin_name'] ." (". $movie['year'] .") [". $movie['quality'] ."-". $movie['lang'] ."]",
                'seo_description' => strip_tags($movie['content']), 
                'og_image' => $imageDomain . $movie['poster_url'],
                'og_url' => $request->path(),
            ]
        ];
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
