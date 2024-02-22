<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="3.0.0",
 *      title="Flashmov API Document",
 *      description="flashmov.xyz Website cung cấp phim miễn phí nhanh chất lượng cao. Nguồn phim chất lượng cao cập nhật nhanh nhất.",
 *      @OA\Contact(
 *          email="lanvkuk2@gmail.com"
 *      )
 * )
 * @OA\Server(
 *      url="https://movies-api-amber-chi.vercel.app/api",
 *      description="Flashmov API Server",
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}