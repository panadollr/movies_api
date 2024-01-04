<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BlogResource extends JsonResource
{
    protected $imageDomain;
    protected $cloudinaryThumbDomain;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
        $this->cloudinaryThumbDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,h_230/uploads/movies/";
    }

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumb_url' => $this->formatImageWithCloudinaryUrl('thumb'),
            'movie_type' => $this->movie_type,
            'date' => '12/2023',
        ];
    }

    //anh cloudinary
    protected function formatImageWithCloudinaryUrl($type)
    {
        $cloudinaryFormat = "-$type.webp";
        $ophimFormat = "-$type.jpg";
        if (preg_match('/([^\/]+)-poster\.jpg$/', $this->thumb_url, $matches)) {
            $slug = $matches[1];
            if ($type == 'thumb') {
                $imageUrl = $this->cloudinaryThumbDomain . $slug . $cloudinaryFormat;
            } else {
                $imageUrl = $this->cloudinaryPosterDomain . $slug . $cloudinaryFormat;
            }
    
        return $imageUrl;
    }
    }
}
