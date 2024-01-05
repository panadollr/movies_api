<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Blog;
use DateTime;

class BlogController 
{

    public function getBlogDetail($slug)
{
    try {
        $blog = Blog::where('slug', $slug)->first();

        if ($blog) {
            return view('admin.edit_blog', compact('blog'));
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
