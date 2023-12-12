<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeoResource extends JsonResource
{
    public function toArray($request)
    {
        $seo = $this['seo'];

        return [
            'seo_title' => $seo['seo_title'] ?? '',
            'seo_description' => $seo['seo_description'] ?? '',
            'og_image' => $seo['og_image'] ?? '',
            'og_url' => $seo['og_url'] ?? $request->path(),
        ];
    }
}
