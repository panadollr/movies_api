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
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'poster_url' => $imageDomain. $this->poster_url,
            'thumb_url' => $imageDomain. $this->thumb_url,
            'content' => $this->content,
            'movie_type' => $this->movie_type,
            // 'date' => $this->date,
            'date' => '12/2023',
            'seoOnPage' => [
                'seo_title' => $this->title,
                'seo_description' => strip_tags(str_replace(["\r", "\n"], '', "$this->content")), 
                'og_image' => $imageDomain. $this->thumb_url,
                'og_url' => $request->path(),
            ]
        ];
    }
}
