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
            // 'poster_url' => $this->formatImageWithCloudinaryUrl('thumb'),
            // 'thumb_url' => $this->formatImageWithCloudinaryUrl('thumb'),
            'poster_url' => $this->formatImageUrl($this->thumb_url),
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

    //anh ophim
    protected function formatImageUrl($url)
    {
            return $url ? "https://ophim10.cc/_next/image?url=http%3A%2F%2Fimg.ophim1.com%2Fuploads%2Fmovies%2F$url&w=192&q=75" : null;
    }
}
