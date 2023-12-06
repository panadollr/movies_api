<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BlogResource extends JsonResource
{
    public function toArray($request)
    {
        $imageDomain = config('api_settings.image_domain');

        return [
            'title' => $this->title,
            'slug' => Str::slug($this->title, '-'),
            'poster_url' => $imageDomain. $this->poster_url,
            'content' => $this->content,
            'movie_type' => $this->movie_type,
            'date' => $this->date,
        ];
    }
}
