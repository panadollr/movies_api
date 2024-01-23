<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Movie;

class MovieResource extends JsonResource
{
    protected $imageDomain;
    protected $cloudinaryPosterDomain;
    protected $cloudinaryThumbDomain;
    protected $imageController;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
        if(request()->path() == 'xu-huong'){
            $this->cloudinaryThumbDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_1100/uploads/movies/";
            $this->cloudinaryPosterDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/ar_1:1,c_fill,g_auto/uploads/movies/";
        } else {
            $this->cloudinaryThumbDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_280/uploads/movies/";
            $this->cloudinaryPosterDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_280/uploads/movies/";
        }
    }
    
    public function toArray($request)
    {

        // return array_filter([
        //     'modified_time' => $this->modified_time,
        //     'id' => $this->_id,
        //     'name' => $this->name,
        //     'origin_name' => $this->origin_name,
        //     'poster_url' => $this->formatImageUrl($this->thumb_url),
        //     'thumb_url' => $this->formatImageUrl($this->poster_url),
        //     // 'poster_url' => $this->formatImageWithCloudinaryUrl('poster'),
        //     // 'thumb_url' => $this->formatImageWithCloudinaryUrl('thumb'),
        //     'slug' => $this->slug,
        //     'year' => $this->year,
        //     'content' => $this->content,
        //     'type' => $this->type,
        //     'status' => $this->status,
        //     'sub_docquyen' => (bool) $this->sub_docquyen,
        //     'time' => $this->time,
        //     'episode_current' => $this->episode_current === 'Tập 0'? 'Trailer' : $this->episode_current,
        //     'quality' => $this->quality,
        //     'lang' => $this->lang,
        //     'category' => $this->formattedCategoriesArray('category'),
        //     // 'country' => $this->formattedArray('country'),
        // ]);

        $categoryConfig = config('api_settings.categories');
        $countryConfig = config('api_settings.countries');

        return array_filter([
            'id' => $this->_id,
            'name' => $this->name,
            'poster_url' => $this->formatImageUrl($this->thumb_url),
            'thumb_url' => $this->formatImageUrl($this->poster_url),
            // 'poster_url' => $this->formatImageWithCloudinaryUrl('poster'),
            // 'thumb_url' => $this->formatImageWithCloudinaryUrl('thumb'),
            'slug' => $this->slug,
            'year' => $this->year,
            'content' => $this->movie_detail->content,
            'type' => $this->movie_detail->type,
            'status' => $this->movie_detail->status,
            'view' => $this->movie_detail->view,
            'sub_docquyen' => (bool) $this->movie_detail->sub_docquyen,
            'time' => $this->movie_detail->time,
            'episode_current' => $this->movie_detail->episode_current === 'Tập 0'? 'Trailer' : $this->movie_detail->episode_current,
            'quality' => $this->movie_detail->quality,
            'lang' => $this->movie_detail->lang,
            'category' => $this->formattedJsonWithConfig($this->movie_detail->category, $categoryConfig),
            'country' => $this->formattedJsonWithConfig($this->movie_detail->country, $countryConfig),
        ]);
    }

    //poster ophim
    protected function formatImageUrl($url)
    {
        // return $url ? $this->imageDomain . $url : null;
        if(request()->path() == 'xu-huong'){
            return $url ? "https://ophim10.cc/_next/image?url=http%3A%2F%2Fimg.ophim1.com%2Fuploads%2Fmovies%2F$url&w=256&q=75" : null;
        } else {
            return $url ? "https://ophim10.cc/_next/image?url=http%3A%2F%2Fimg.ophim1.com%2Fuploads%2Fmovies%2F$url&w=192&q=75" : null;
        }
        
    }

    //poster cloudinary
    protected function formatImageWithCloudinaryUrl($type)
    {
        $slug = $this->slug;
        $cloudinaryFormat = "-$type.webp";
        $ophimFormat = "-$type.jpg";
            if ($type == 'thumb') {
                if($this->year == 2023){
                    $imageUrl = $this->cloudinaryThumbDomain . $slug . $cloudinaryFormat;
                }else {
                    $imageUrl = $this->imageDomain . $slug . $ophimFormat;
                }
            } else {
                $modifiedTimeTimestamp = strtotime($this->modified_time);
                $targetDateTimestamp = strtotime("10/1/2024");
                if($modifiedTimeTimestamp > $targetDateTimestamp){
                    $imageUrl = $this->imageDomain . $slug . $ophimFormat;
                }else {
                    $imageUrl = $this->cloudinaryPosterDomain . $slug . $cloudinaryFormat;
                }
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

    // protected function formattedCategoriesArray($propertyName)
    // {
    //     $propertyValue = $this->$propertyName;
    //     $categories = config('api_settings.categories');
    //     $propertyValue = collect(json_decode($this->$propertyName, true))->pluck('slug')->toArray();
    
    //     $filteredCategories = [];
    //     foreach ($propertyValue as $categorySlug) {
    //         if (array_key_exists($categorySlug, $categories)) {
    //                 $filteredCategories[] = ['name' => $categories[$categorySlug]];
    //         }
    //     }
    
    //     return array_values($filteredCategories);
    // }

    protected function formattedJsonWithConfig($jsonData, $arrayConfig)
{
    $propertyValue = collect(json_decode($jsonData, true))->pluck('slug')->toArray();
    $filteredArrayConfig = array_intersect_key($arrayConfig, array_flip($propertyValue));
    $formattedJsonData = array_map(function ($name) {
        return ['name' => $name];
    }, $filteredArrayConfig);

    return array_values($formattedJsonData);
}

}

