<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Models\Blog;
use App\Http\Resources\PaginationResource;

class BlogController
{
    public function getBlogs(){
        $limit = $request->limit ?? 5;
        try {
        $blog = Blog::paginate($limit);
    
        return response()->json(new PaginationResource($blog), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
