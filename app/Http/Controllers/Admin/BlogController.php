<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Blog;
use DateTime;

class BlogController 
{
    public function createBlog(Request $request){
        $title = $request->title;
        $slug = Str::slug($title, '-');
        $poster_url = $request->poster_url;
        $content = $request->content;
        $movie_type = $request->movie_type;
        $date = new DateTime();
        try {
            if (!Blog::where('slug', $slug)->exists()) {
                    Blog::create([
                        'title' => $title,
                        'slug' => Str::slug($title, '-'),
                        'poster_url' => $poster_url,
                        'thumb_url' => $poster_url,
                        'content' => $content,
                        'movie_type' => $movie_type,
                        'date' => new DateTime()
                    ]);
            } else {
                return response()->json('Tiêu đề đã tồn tại !', 500);
            }
    
        return response()->json('Thêm bài viết thành công !', 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    private function limitTitleLength($title, $maxLength) {
        // Kiểm tra độ dài của chuỗi title
        if (mb_strlen($title, 'UTF-8') <= $maxLength) {
            return $title; // Trả về nguyên chuỗi nếu không vượt quá chiều dài tối đa
        } else {
            // Cắt chuỗi và thêm dấu "..."
            return mb_substr($title, 0, $maxLength, 'UTF-8') . '...';
        }
    }

    public function getBlogDetail($slug)
{
    try {
        $blog = Blog::where('slug', $slug)->first();

        if ($blog) {
            return view('admin.blog.edit_blog', compact('blog'));
        } else {
            return response()->json('Không tìm thấy bài viết để cập nhật!', 500);
        }
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}

    public function updateBlog(Request $request, $slug)
{
    try {
        $existingBlog = Blog::where('slug', $slug)->first();

        if ($existingBlog) {
            $existingBlog->title = $request->title;
            $existingBlog->slug = Str::slug($request->title, '-');
            // $existingBlog->poster_url = $request->poster_url;
            // $existingBlog->thumb_url = $request->thumb_url;
            $existingBlog->content = $request->content;
            // $existingBlog->movie_type = $request->movie_type;
            // $existingBlog->date = new DateTime();

            $existingBlog->save();

            return response()->json('Cập nhật bài viết thành công!', 200);
        } else {
            return response()->json('Không tìm thấy bài viết để cập nhật!', 500);
        }
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}
}
