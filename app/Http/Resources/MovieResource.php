<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use GuzzleHttp\Client;


class MovieResource extends JsonResource
{
    protected $imageDomain;
    protected $cloudinaryDomain;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
        if(request()->path() == 'xu-huong'){
            $this->cloudinaryDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_300/uploads/movies/";
        } else {
            $this->cloudinaryDomain = "https://res.cloudinary.com/dtilp1gei/uploads/movies/";
        }
    }
    
    public function toArray($request)
    {

        return array_filter([
            'modified_time' => $this->modified_time,
            'id' => $this->_id,
            'name' => $this->name,
            'origin_name' => $this->origin_name,
            'thumb_url' => $this->formatImageUrl($this->poster_url),
            'slug' => $this->slug,
            'year' => $this->year,
            // 'poster_url' => $this->formatImageUrl($this->thumb_url),
            'poster_url' => $this->formatImageUrlv2(),
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'sub_docquyen' => (bool) $this->sub_docquyen,
            'time' => $this->time,
            'episode_current' => $this->episode_current,
            'quality' => $this->quality,
            'lang' => $this->lang,
            'category' => $this->formattedCategoriesArray('category'),
            // 'country' => $this->formattedArray('country'),
        ]);
    }

    protected function formatImageUrl($url)
    {
        return $url ? $this->imageDomain . $url : null;
    }

    protected function formatImageUrlv2()
    {
        $slug = $this->slug;
        return ($this->year >= 2023)
        ? $this->cloudinaryDomain . $slug . '-thumb.webp'
        : $this->imageDomain . $slug . '-thumb.jpg';
    }

    protected function formattedArray($propertyName)
    {
        $propertyValue = $this->$propertyName;

        return $propertyValue !== null
            ? array_map(function ($item) {
                return ['name' => $item['name'], 'slug' => $item['slug']];
            }, json_decode($propertyValue, true))
            : null;
    }

    protected function formattedCategoriesArray($propertyName)
    {
        $propertyValue = $this->$propertyName;
        $categories = config('api_settings.categories');
        $propertyValue = collect(json_decode($this->$propertyName, true))->pluck('slug')->toArray();
    
        $filteredCategories = [];
        foreach ($propertyValue as $categorySlug) {
            if (array_key_exists($categorySlug, $categories)) {
                    $filteredCategories[] = ['name' => $categories[$categorySlug]];
            }
        }
    
        return array_values($filteredCategories);
    }

}

