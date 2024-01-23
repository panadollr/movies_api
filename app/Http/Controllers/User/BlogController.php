<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Blog;
use App\Http\Resources\BlogResource;
use App\Http\Resources\BlogDetailResource;
use App\Http\Resources\PaginationResource;

use DateTime;

class BlogController
{

    protected function generateSeoData()
    {
        $title = "Tổng hợp các tin tức, đánh giá phim mới nhất - Flashmov";
        $description = "Xem các tin tức, đánh giá mới nhất về phim trên Flashmov. Flashmov cung cấp những thông tin chi tiết, đánh giá chân thực.";
        return [
            'seo_title' =>  $title,
            'seo_description' => $description, 
            'og_image' => "https://firebasestorage.googleapis.com/v0/b/hired-dacs4.appspot.com/o/thumb_blogs_seo.jpg?alt=media&token=cd81f0c6-e822-4565-bdcc-1d2e119ce00d",
            'og_url' => request()->path(),
        ];
    }

    public function getBlogs(){
        $limit = request()->input('limit', 5);
        try {
        $query = Blog::select(['id', 'title', 'slug', 'thumb_url', 'movie_type', 'date']);
        
        if($limit == 'all'){
            $blogs = $query->get();
        } else {
            $blogs = $query->paginate($limit);
        }
        $data = [
            'data' => BlogResource::collection($blogs),
            'seoOnPage' => $this->generateSeoData()
         ];

        return response()->json(new PaginationResource($data), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function blogDetail($slug){
        try {
        $blogDetail = Blog::where('slug', $slug)->first();
        
        if (!$blogDetail) {
            return response()->json(['error' => 'Blog này không tồn tại !'], 404);
        }
        
        return response()->json(new BlogDetailResource($blogDetail), 200);
        } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
    }


    public function similarBlogs($slug){
        $limit = request()->input('limit', 5);
        try {
        $blogDetail = Blog::where('slug', $slug)->first();
        $blog = Blog::select(['id', 'title', 'slug', 'poster_url', 'thumb_url', 'movie_type', 'date'])
        ->where('slug', '!=', $slug)
        ->where('movie_type', $blogDetail->movie_type)
        ->paginate($limit);

        $data = [
            'data' => BlogResource::collection($blog),
            'seoOnPage' => ''
         ];
    
        return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

}
