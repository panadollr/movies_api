<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


use App\Models\Movie;


class UploadImageToCloudinary extends Command
{
    protected $signature = 'upload:image';
    protected $description = 'Command description';

    public function handle()
    {
        $this->info('uploading images to cloudinary...');
        $this->uploadPoster();
        $this->uploadThumb();
        $this->info('uploaded images to cloudinary successfully');
    }

//     public function convertToWebP()
//     {
//         // $batch_movie_thumb_url = Movie::orderByDesc('year')->take(2000)->pluck('thumb_url')->toArray();
//         $batch_movie_thumb_url = Movie::pluck('thumb_url')->toArray();

//         // // $allMovies = Movie::all();
//         foreach ($batch_movie_thumb_url as $thumb_url) {
//         //     $publicIdImage = "uploads/movies/" . pathinfo($thumb_url, PATHINFO_FILENAME);
//         //     // $cloudinaryUrl = Cloudinary::getUrl($publicIdImage);
        
//         //     // if (!$cloudinaryUrl) {
//                 $posterUrl = "https://img.ophim9.cc/uploads/movies/{$thumb_url}";
//                 try {    
//                     $publicIdImage = "uploads/movies/" . pathinfo($thumb_url, PATHINFO_FILENAME);

//                         Cloudinary::upload($posterUrl, [
//                             'format' => 'webp',
//                             'public_id' => $publicIdImage,
//                             'options' => [
//                                 'format' => 'webp',
//                                 'quality' => 'auto',
//                                 'overwrite' => true,
//                             ],
//                                 'transformation' => [
//                                     'width' => 280,
//                                 ],
//                         ]);

//                 } catch (\Exception $e) {
//                 }
//             // }
//         }

// }

public function uploadPoster()
{
    $batchSize = 100;
    $offset = 978; // lưu ý 

        // $batchMovieThumbUrls = Movie::orderByDesc('year')
        //     ->skip($offset)
        //     ->take($batchSize)
        //     ->pluck('thumb_url')
        //     ->toArray();

        // $batchMovieThumbUrls = Movie::where('year', 2023)
        // ->pluck('thumb_url')
        // // ->skip(1240)
        // ->toArray();

        $batchMovieThumbUrls = Movie::where('_id', '6589394befed8b7739ded498')
        ->pluck('thumb_url')
        // ->skip(1240)
        ->toArray();

        foreach ($batchMovieThumbUrls as $thumbUrl) {
            $posterUrl = "https://img.ophim9.cc/uploads/movies/{$thumbUrl}";
            $posterName = str_replace("-thumb.jpg", "-poster", $thumbUrl);
            try {    
                $publicIdImage = "uploads/movies/" . $posterName;

                Cloudinary::upload($posterUrl, [
                    'format' => 'webp',
                    'public_id' => $publicIdImage,
                    'options' => [
                        'format' => 'webp',
                        'quality' => 'auto',
                        'overwrite' => false,
                    ],
                    'transformation' => [
                        'width' => 450,
                    ],
                ]);

            } catch (\Exception $e) {
                // Xử lý ngoại lệ nếu cần
            }
        }
}

public function uploadThumb()
{
    $batchSize = 100;
    $offset = 978; // lưu ý 

        // $batchMovieThumbUrls = Movie::orderByDesc('year')
        //     ->skip($offset)
        //     ->take($batchSize)
        //     ->pluck('thumb_url')
        //     ->toArray();

        // $batchMoviePosterUrls = Movie::where('year', 2023)
        // ->pluck('poster_url')
        // // ->skip(1240)
        // ->toArray();

        $batchMoviePosterUrls = Movie::where('_id', '6589394befed8b7739ded498')
        ->pluck('poster_url')
        // ->skip(1240)
        ->toArray();

        foreach ($batchMoviePosterUrls as $posterUrl) {
            $thumbUrl = "https://img.ophim9.cc/uploads/movies/{$posterUrl}";
            $thumbName = str_replace("-poster.jpg", "-thumb", $posterUrl);
            try {    
                $publicIdImage = "uploads/movies/" . $thumbName;

                Cloudinary::upload($thumbUrl, [
                    'format' => 'webp',
                    'public_id' => $publicIdImage,
                    'options' => [
                        'format' => 'webp',
                        'quality' => 'auto',
                        'overwrite' => false,
                    ],
                    'transformation' => [
                        'width' => 1920,
                        'height' => 1080
                    ],
                ]);

            } catch (\Exception $e) {
                // Xử lý ngoại lệ nếu cần
            }
        }
}




}


