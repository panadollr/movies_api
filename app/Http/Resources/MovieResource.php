<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

use App\Models\MovieDetails;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $formattedModifiedTime = (new DateTime($this->modified_time))->format('m/Y');
        $formattedCategory = array_map(function ($c) {
            return ['name' => $c['name']];
        }, json_decode($this->category, true));
        $imageDomain = config('app_settings.image_domain');

        return [
            'id' => $this->_id,
            'name' => $this->name,
            'origin_name' => $this->origin_name,
            'thumb_url' => $imageDomain. $this->thumb_url,
            'poster_url' => $imageDomain. $this->poster_url,
            'slug' => $this->slug,
            'year' => $this->year,
            'category' => $formattedCategory,
            'modified_time' => $formattedModifiedTime,
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'sub_docquyen' => (bool) $this->sub_docquyen ,
            'time' => $this->time,
            'quality' => $this->quality,
            'lang' => $this->lang,
            'showtimes' => $this->showtimes,
        ];
    }
}
