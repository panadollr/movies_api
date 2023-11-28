<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

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

        return [
            'id' => $this->_id,
            'name' => $this->name,
            'thumb_url' => $this->thumb_url,
            'slug' => $this->slug,
            'year' => $this->year,
            'category' => $formattedCategory,
            'modified_time' => $formattedModifiedTime,
            'content' => $this->content,
        ];
    }
}
