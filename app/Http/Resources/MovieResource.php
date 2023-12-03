<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

use App\Models\MovieDetails;

class MovieResource extends JsonResource
{
    public function toArray($request)
    {
        $formattedModifiedTime = (new DateTime($this->modified_time))->format('m/Y');
        $imageDomain = config('api_settings.image_domain');

        $data = [
            'modified_time' => $formattedModifiedTime,
            'id' => $this->_id,
            'name' => $this->name,
            'origin_name' => $this->origin_name,
            'poster_url' => ($this->thumb_url) ? $imageDomain . $this->thumb_url : null,
            'thumb_url' =>  ($this->poster_url) ? $imageDomain . $this->poster_url : null,
            'slug' => $this->slug,
            'year' => $this->year,
            'category' => $this->formattedCategory(),
            'content' => $this->content,
            'type' => $this->type,
            'status' => $this->status,
            'sub_docquyen' => (bool) $this->sub_docquyen ? $this->sub_docquyen : null ,
            'time' => $this->time,
            'episode_current' => $this->episode_current,
            'episode_total' => $this->episode_total,
            'quality' => $this->quality,
            'lang' => $this->lang,
            'showtimes' => $this->showtimes,
            'view' => $this->view,
        ];

        return array_filter($data);
    }


    protected function formattedCategory()
{
    return ($this->category !== null)
        ? array_map(function ($c) {
            return ['name' => $c['name'], 'slug' => $c['slug']];
        }, json_decode($this->category, true))
        : null;
}


}
