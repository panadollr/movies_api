<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' =>$this->resource->items(),
            'pagination' => [
                'totalItems' => $this->resource->total(),
                'totalItemsPerPage' => $this->resource->perPage(),
                'currentPage' => $this->resource->currentPage(),
                'totalPages' => $this->resource->lastPage(),
            ],
        ];
    }
}
