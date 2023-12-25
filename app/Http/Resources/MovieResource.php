<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use GuzzleHttp\Client;

// use App\Http\Controllers\Admin\ImageController;
use App\Models\Movie;


class MovieResource extends JsonResource
{
    protected $imageDomain;
    protected $cloudinaryDomain;
    protected $imageController;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
        if(request()->path() == 'xu-huong'){
            $this->cloudinaryDomain = "https://res.cloudinary.com/dtilp1gei/uploads/movies/";
        } else {
            $this->cloudinaryDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_280/uploads/movies/";
        }
    }
    
    public function toArray($request)
    {
        // $imageController = new ImageController();

        return array_filter([
            'modified_time' => $this->modified_time,
            'id' => $this->_id,
            'name' => $this->name,
            'origin_name' => $this->origin_name,
            // 'poster_url' => $this->formatImageUrl($this->thumb_url),
            'poster_url' => $this->formatImageWithCloudinaryUrl('poster'),
            // 'thumb_url' => $this->formatImageUrl($this->poster_url),
            'thumb_url' => $this->formatImageWithCloudinaryUrl('thumb'),
            'slug' => $this->slug,
            'year' => $this->year,
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'sub_docquyen' => (bool) $this->sub_docquyen,
            'time' => $this->time,
            'episode_current' => $this->episode_current === 'Tập 0'? 'Trailer' : $this->episode_current,
            'quality' => $this->quality,
            'lang' => $this->lang,
            'category' => $this->formattedCategoriesArray('category'),
            // 'country' => $this->formattedArray('country'),
        ]);
    }

    //poster ophim
    protected function formatImageUrl($url)
    {
        return $url ? $this->imageDomain . $url : null;
    }

    //poster cloudinary
    protected function formatImageWithCloudinaryUrl($type)
    {
        $slug = $this->slug;
        $cloudinaryFormat = "-$type.webp";
        if ($this->year == 2023) {
            $cloudinaryFormat = "-$type.webp";
            $imageUrl = $this->cloudinaryDomain . $slug . $cloudinaryFormat;
        } else {
            if ($type == 'thumb') {
                $type = 'poster';
            } else {
                $type = 'thumb';
            }
        
            $imageUrl = $this->imageDomain . "$slug-$type.jpg";
        }
        
        return $imageUrl;
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

