<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Blog;
use App\Http\Resources\BlogResource;
use App\Http\Resources\PaginationResource;

class BlogController
{
    // public function addSlug(Request $request){
    //     $title = $request->title;
    //     $poster_url = $request->poster_url;
    //     $content = $request->content;
    //     $movie_type = $request->movie_type;
    //     $date = new DateTime();
    //     try {
    //     $newBlog = Blog::create([
    //         'title' = $request    
    //     ]);
    
    //     return response()->json(new PaginationResource($blog), 200);
    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => $th->getMessage()], 500);
    //     }
    // }

    public function getBlogs(){
        $limit = $request->limit ?? 5;
        try {
        $blog = Blog::paginate($limit);
    
        return response()->json(new PaginationResource(BlogResource::collection($blog)), 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function blogDetails($slug){
        try {
        $blogs = Blog::all();
        $matchingBlog = $blogs->first(function ($blog) use ($slug) {
            $slugV2 = Str::slug($blog->title, '-');
            return $slug == $slugV2;
        });
        
        if ($matchingBlog) {
            return response()->json(new BlogResource($matchingBlog), 200);
        }

        return response()->json(['error' => 'Blog này không tồn tại !'], 404);

        } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
    }

}
