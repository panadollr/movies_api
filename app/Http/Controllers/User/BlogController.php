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
    public function addSlug(Request $request){
        $title = $request->title;
        $poster_url = $request->poster_url;
        $thumb_url = $request->thumb_url;
        $content = $request->content;
        $movie_type = $request->movie_type;
        $date = new DateTime();
        try {
         Blog::create([
            'title' => $title,
            'slug' => Str::slug($title, '-'),
            'poster_url' => $poster_url,
            'thumb_url' => $poster_url,
            'content' => $content,
            'movie_type' => $movie_type,
            'date' => $date
        ]);
    
        return response()->json('Thêm bài viết thành công !', 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    protected function generateSeoData($title, $description)
    {
        return [
            'seo_title' =>  $title,
            'seo_description' => $description, 
            'og_image' => '',
            'og_url' => request()->path(),
        ];
    }

    public function getBlogs(){
        $limit = $request->limit ?? 5;
        try {
        $query = Blog::select(['id', 'title', 'slug', 'poster_url', 'movie_type', 'date']);
        
        if($limit == 'all'){
            $blogs = $query->get();
        } else {
            $blogs = $query->paginate($limit);
        }
        $data = [
            'data' => BlogResource::collection($blogs),
            'seoOnPage' => ''
         ];
    
        return response()->json($data, 200);
        // return response()->json(new PaginationResource(BlogResource::collection($blog)), 200);
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
        return response()->json(new BlogResource($blogDetail), 200);

        } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
    }


    public function similarBlogs($slug){
        $limit = $request->limit ?? 5;
        try {
        $blogDetail = Blog::where('slug', $slug)->first();
        $blog = Blog::select(['id', 'title', 'slug', 'poster_url', 'movie_type', 'date'])
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
