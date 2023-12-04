<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

use App\Models\MovieDetails;


class MovieResource extends JsonResource
{
    public function toArray($request)
    {
        $imageDomain = config('api_settings.image_domain');

        return array_filter([
            'modified_time' => (new DateTime($this->modified_time))->format('m/Y'),
            'id' => $this->_id,
            'name' => $this->name,
            'origin_name' => $this->origin_name,
            'poster_url' => $this->formatImageUrl($this->thumb_url, $imageDomain),
            'thumb_url' => $this->formatImageUrl($this->poster_url, $imageDomain),
            'slug' => $this->slug,
            'year' => $this->year,
            'category' => $this->formattedArray('category'),
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'sub_docquyen' => $this->sub_docquyen ? $this->sub_docquyen : null,
            'time' => $this->time,
            'episode_current' => $this->episode_current,
            'episode_total' => $this->episode_total,
            'quality' => $this->quality,
            'lang' => $this->lang,
            'showtimes' => $this->showtimes,
            'view' => $this->view,
            'country' => $this->formattedArray('country'),
        ]);
    }

    protected function formatImageUrl($url, $domain)
    {
        return $url ? $domain . $url : null;
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
}

