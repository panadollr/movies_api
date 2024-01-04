<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogDetailResource extends JsonResource
{
    protected $imageDomain;
    protected $cloudinaryThumbDomain;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
        $this->cloudinaryThumbDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_320/uploads/movies/";
    }

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumb_url' => $this->formatImageWithCloudinaryUrl('thumb'),
            'content' => $this->content,
            'movie_type' => $this->movie_type,
            'date' => '12/2023',
            'seoOnPage' => [
                'seo_title' => $this->title,
                'seo_description' => strip_tags(str_replace(["\r", "\n"], '', "$this->content")), 
                'og_image' => $this->formatImageWithCloudinaryUrl('thumb'),
                'og_url' => $request->path(),
            ]
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
