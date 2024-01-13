<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Exception\RequestException;

use App\Models\Movie;

class UploadImageToCloudinary extends Command
{
    protected $signature = 'upload:image';
    protected $description = 'Command description';

    public function handle()
    {
        $this->info('uploading images to cloudinary...');
        $this->uploadPosters();
        // $this->uploadThumbs();
        $this->info('uploaded images to cloudinary successfully');
                    
    }

    // ... Cloudinary upload logic...
protected function uploadImageToCloudinary($saveFolder, $slug, $type, $url, $quality, $transformation = []) {
    try {
        $publicId = "$saveFolder/$slug-$type";
        return Cloudinary::upload($url, [
            'format' => 'webp',
            'public_id' => $publicId,
            'quality' => $quality,
            'overwrite' => false,
            'transformation' => $transformation,
        ]);
    } catch (\Throwable $th) {
      
    }
 }

//     protected function uploadPosters()
// {
//     try {
//     $client = new Client();
//     $promises = [];
//     // $movies = Movie::select('slug', 'thumb_url')->get();
//     $movies = Movie::select('slug', 'thumb_url')->take(1)->get();

//     foreach ($movies as $movie) {
//         if (preg_match('/([^\/]+)-thumb\.jpg$/', $movie->thumb_url, $matches)) {
//             $cloudinaryPosterUrl = "https://res.cloudinary.com/dtilp1gei/image/upload/c_thumb,w_50/uploads/movies/{$matches[1]}-poster.webp";
//         }

//         $promises[] = $client->getAsync($cloudinaryPosterUrl, ['connect_timeout' => 3])->then(
//             function ($response) use ($movie) {
//                 if ($response->getStatusCode() != 200) {
//                     $posterUrl = "https://img.ophim9.cc/uploads/movies/{$movie->thumb_url}";
//                     if (preg_match('/\/movies\/([^\/]+)-thumb\.jpg$/', $posterUrl, $matches)) {
//                         $slug = $matches[1];
//                         $posterTransformation = [
//                             'width' => 300,
//                             'crop' => 'scale'
//                         ];
//                         $quality ='45';
//                         $this->uploadImageToCloudinary('posters', $slug, 'poster', $posterUrl, $quality, $posterTransformation);
//                     }
//                 }
//             }
//         );
//     }

//     Utils::all($promises)->wait();
// } catch (\Throwable $th) {
//     //throw $th;
// }
// }

protected function uploadPosters()
{
    $movies = Movie::select('slug', 'thumb_url')->skip(0)->take(3000)->get();
    // Movie::orderBy('_id')->select('slug', 'thumb_url')->chunk(200, function ($movies) {
    foreach ($movies as $movie) {
                    $posterUrl = "https://img.ophim10.cc/uploads/movies/{$movie->thumb_url}";
                    if (preg_match('/\/movies\/([^\/]+)-thumb\.jpg$/', $posterUrl, $matches)) {
                        $slug = $matches[1];
                        $posterTransformation = [
                            'width' => 500,
                            'crop' => 'scale'
                        ];
                        $quality ='50';
                        $this->uploadImageToCloudinary('posters', $slug, 'poster', $posterUrl, $quality, $posterTransformation);
                    }
    }
// });
}


protected function uploadThumbs()
{
    Movie::orderBy('_id')->select('slug', 'poster_url')->chunk(200, function ($movies) {
        foreach ($movies as $movie) {
                $thumbUrl = "https://img.ophim10.cc/uploads/movies/{$movie->poster_url}";
                if (preg_match('/\/movies\/([^\/]+)-poster\.jpg$/', $thumbUrl, $matches)) {
                    $slug = $matches[1];
                    $thumbTransformation = [
                        'width' => 1000,
                        'crop' => 'scale'
                    ];
                    $quality ='55';
                    $this->uploadImageToCloudinary('thumbs', $slug, 'thumb', $thumbUrl, $quality, $thumbTransformation);
                }
    }
});
}



}


