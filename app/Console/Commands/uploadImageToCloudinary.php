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
        // $this->uploadThumb();
        $this->info('uploaded images to cloudinary successfully');

        // $slug = "nhanh-hon-nua-di-anh";
        // $posterUrl = "https://img.ophim9.cc/uploads/movies/$slug-thumb.jpg";
        // $posterTransformation = ['width' => 450];
        // $this->uploadImageToCloudinary($slug, 'poster', $posterUrl, $posterTransformation);
    }

     // ... Cloudinary upload logic...
protected function uploadImageToCloudinary($slug, $format, $url, $transformation = []) {
    try {
        $publicId = "uploads/movies/$slug-$format";
        return Cloudinary::upload($url, [
            'format' => 'webp',
            'public_id' => $publicId,
            'options' => [
                'format' => 'webp',
                'quality' => 'auto',
                'overwrite' => false,
            ],
            'transformation' => $transformation,
        ]);
    } catch (\Throwable $th) {
      
    }
 }

public function uploadPoster()
{
    $batchSize = 100;
    $offset = Movie::count() - $batchSize; // lưu ý 

        // $batchMovieThumbUrls = Movie::where('year', 2023)
        // ->pluck('thumb_url')
        // // ->skip(1240)
        // ->toArray();

        // $batchMovieThumbUrls = Movie::skip($offset)
        //     ->where('year', 2023)
        //     ->take($batchSize)
        //     ->pluck('thumb_url')
        //     ->toArray();
        
        // $batchMovieThumbUrls = Movie::where('year', 2023)
        // ->orderByDesc('modified_time')
        // ->take($batchSize)
        // ->pluck('thumb_url')
        // ->toArray();

        $batchMovieThumbUrls = Movie::pluck('thumb_url')
        ->skip(10650)
        ->toArray();

        // ... Cloudinary upload...
        foreach ($batchMovieThumbUrls as $thumbUrl) {
        $posterUrl = "https://img.ophim9.cc/uploads/movies/{$thumbUrl}";
        if (preg_match('/\/movies\/([^\/]+)-thumb\.jpg$/', $posterUrl, $matches)) {
            $slug = $matches[1];
            $posterTransformation = ['width' => 450];
            $this->uploadImageToCloudinary($slug, 'poster', $posterUrl, $posterTransformation);
            print_r('uploaded 1 poster !');
        }
        }

}

public function uploadThumb()
{
    $batchSize = 100;
    $offset = Movie::count() - $batchSize; // lưu ý 

        // $batchMoviePosterUrls = Movie::where('year', 2023)
        // ->pluck('poster_url')
        // // ->skip(1240)
        // ->toArray();

        // $batchMoviePosterUrls = Movie::skip($offset)
        //     ->where('year', 2023)
        //     ->take($batchSize)
        //     ->pluck('poster_url')
        //     ->toArray();

        $batchMoviePosterUrls = Movie::where('year', 2023)
            ->orderByDesc('modified_time')
            ->take($batchSize)
            ->pluck('poster_url')
            ->toArray();

        foreach ($batchMoviePosterUrls as $posterUrl) {
            $thumbUrl = "https://img.ophim9.cc/uploads/movies/{$posterUrl}";
            $slug = preg_replace('/-poster\.jpg$/', '', $posterUrl);
            $thumbTransformation = ['width' => 1920, 'height' => 1080];
            $this->uploadImageToCloudinary($slug, 'thumb', $thumbUrl, $thumbTransformation);
            print_r('uploaded 1 thumb !');
        }
}


}


