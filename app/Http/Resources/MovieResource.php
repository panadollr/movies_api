<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use GuzzleHttp\Client;


use App\Models\MovieDetails;


class MovieResource extends JsonResource
{
    protected $imageDomain;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
    }
    
    public function toArray($request)
    {

        return [
            // 'modified_time' => (new DateTime($this->modified_time))->format('m/Y'),
            'modified_time' => $this->modified_time,
            'id' => $this->_id,
            'name' => $this->name,
            'origin_name' => $this->origin_name,
            'thumb_url' => $this->formatImageUrl($this->poster_url),
            'slug' => $this->slug,
            'year' => $this->year,
            'poster_url' => $this->formatImageUrl($this->thumb_url),
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            // 'is_copyright' => $this->is_copyright,
            'sub_docquyen' => (bool) $this->sub_docquyen,
            // 'trailer_url' => $this->trailer_url,
            'time' => $this->time,
            'episode_current' => $this->episode_current,
            // 'episode_total' => $this->episode_total,
            'quality' => $this->quality,
            'lang' => $this->lang,
            // 'notify' => $this->notify,
            // 'showtimes' => $this->showtimes,
            // 'view' => $this->view,
            // 'actor' => json_decode($this->actor),
            // 'director' => json_decode($this->director),
            'category' => $this->formattedCategoriesArray('category'),
            // 'country' => $this->formattedArray('country'),
        ];
    }

    protected function formatImageUrl($url)
    {
        return $url ? $this->imageDomain . $url : null;
    }

//     protected function formatImageUrl($url, $domain)
// {
//     try {
//         $imageCloudinaryDomain = "https://res.cloudinary.com/dtilp1gei/";
//         $publicIdImage = "uploads/movies/" . pathinfo($url, PATHINFO_FILENAME);
//         $cloudinaryUrl = Cloudinary::getUrl($publicIdImage);
        
//         if ($cloudinaryUrl) {
//             return $cloudinaryUrl;
//         } else {
//             Cloudinary::upload("https://img.ophim9.cc/uploads/movies/{$url}", [
//                 'format' => 'webp',
//                 'public_id' => $publicIdImage,
//             ]);
//             return $domain . $url;
//         }

//     } catch (\Exception $e) {
//         // Handle the exception if necessary
//         return null;
//     }
// }



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

