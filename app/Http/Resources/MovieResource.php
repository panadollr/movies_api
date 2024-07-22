<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    protected $imageDomain;
    protected $imageController;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imageDomain = config('api_settings.image_domain');
    }
    
    public function toArray($request)
    {

        $categoryConfig = config('api_settings.categories');
        $countryConfig = config('api_settings.countries');

        return array_filter([
            'id' => $this->_id,
            'name' => $this->name,
            'poster_url' => $this->formatImageUrl($this->thumb_url),
            'thumb_url' => $this->formatImageUrl($this->poster_url),
            'slug' => $this->slug,
            'year' => $this->year,
            'content' => $this->content,
            'trailer_url' => $this->trailer_url,
            'type' => $this->type,
            'status' => $this->status,
            'view' => $this->view,
            'sub_docquyen' => (bool) $this->sub_docquyen,
            'time' => $this->time,
            'episode_current' => $this->episode_current === 'Tập 0'? 'Trailer' : $this->episode_current,
            'quality' => $this->quality,
            'lang' => $this->lang,
            'category' => $this->formattedJsonWithConfig($this->category, $categoryConfig),
            // 'country' => $this->formattedJsonWithConfig($this->country, $countryConfig),
        ]);
    }

    //poster ophim
    protected function formatImageUrl($url)
    {
        return "https://img.ophim.live/uploads/movies/{$this->slug}-thumb.jpg";
        
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

