<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(
 *      title="Flashmov API Document",
 *      description="flashmov.xyz Website cung cấp phim miễn phí nhanh chất lượng cao. Nguồn phim chất lượng cao cập nhật nhanh nhất.<br>
 *      Developed by: lanvkuk2@gmail.com",
 *      version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     public function welcome()
    {
        $profile = DB::connection('mysql2')->table('my_profiles')->first();
        $projects = DB::connection('mysql2')->table('projects')->get();
        $project_categories = DB::connection('mysql2')->table('project_categories')->get();
        $blogs = DB::connection('mysql2')->table('blogs')->get();
    
        return view('welcome', compact('profile', 'projects', 'project_categories', 'blogs'));
    }
}
