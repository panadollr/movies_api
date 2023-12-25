<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;

use App\Jobs\ImageUploadJob;

class ImageController
{
    protected $imageDomain;
    protected $cloudinaryDomain;

    public function __construct()
    {
        $this->imageDomain = config('api_settings.image_domain');
        $this->cloudinaryDomain = "https://res.cloudinary.com/dtilp1gei/image/upload/w_280/uploads/movies/";
    }
   
// public function getPoster($slug)
// {
//     try {
//         $imageName = $slug . '-thumb.jpg';
//         $imageUrl =  $this->cloudinaryDomain . $imageName;
//         // $publicIdImage = "uploads/movies/" . pathinfo($imageUrl, PATHINFO_FILENAME);
//         $publicIdImage = "uploads/movies/{$slug}-poster";
//         $cloudinaryImageUrl = Cloudinary::getUrl($publicIdImage);
        
//         if ($cloudinaryImageUrl) {
//             return $cloudinaryImageUrl;
//         } 
        
//             $posterUrl = "https://img.ophim9.cc/uploads/movies/{$imageName}";   
        
//             Cloudinary::upload($posterUrl, [
//                 'format' => 'webp',
//                 'public_id' => $publicIdImage,
//                 'options' => [
//                     'format' => 'webp',
//                     'quality' => 'auto',
//                     'overwrite' => false,
//                 ],
//                     'transformation' => [
//                         'width' => 500,
//                     ],
//                             ]);

//         // $cloudinaryImageUrl = Cloudinary::getUrl($publicIdImage);
//         $cloudinaryImageUrl = $this->cloudinaryDomain . $slug . '-poster.webp';

//         // return redirect($cloudinaryImageUrl);
//         return $cloudinaryImageUrl;
//     } catch (\Throwable $th) {
//         // return  $this->imageDomain . $imageName;
//         return  $th->getMessage();
//     }
// }


}
