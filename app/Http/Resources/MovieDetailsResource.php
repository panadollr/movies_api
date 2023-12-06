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
                'category' => $this->formattedArray($movie, 'category'),
                'country' => $this->formattedArray($movie, 'country'),
            ],
            'episodes' => $this['episodes']
        ];
    }

    protected function formattedArray($movie, $propertyName)
    {
        $propertyValue = $movie[$propertyName];

        return array_map(function ($item) {
                return ['name' => $item['name']];
            }, json_decode($propertyValue, true));
    }

}
