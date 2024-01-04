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

    protected function generateSeoData()
    {
        $title = "Tổng hợp các review, đánh giá, thông tin phim mới trên Flashmov - flashmov.xyz";
        $description = "Xem các review, đánh giá, thông tin mới nhất về phim trên Flashmov. Flashmov cung cấp những thông tin chi tiết, đánh giá chân thực để bạn có sự lựa chọn tốt nhất.";
        return [
            'seo_title' =>  $title,
            'seo_description' => $description, 
            'og_image' => "https://res.cloudinary.com/dtilp1gei/image/upload/v1704197910/thumb_blogs_seo.jpg",
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
