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
        $this->convertToWebP();
        $this->info('uploaded images to cloudinary successfully');
    }

    public function convertToWebP()
    {
        // $batch_movie_thumb_url = Movie::orderByDesc('year')->take(2000)->pluck('thumb_url')->toArray();
        $batch_movie_thumb_url = Movie::where('year', 2023)->pluck('thumb_url')->toArray();

        // // $allMovies = Movie::all();
        foreach ($batch_movie_thumb_url as $thumb_url) {
        //     $publicIdImage = "uploads/movies/" . pathinfo($thumb_url, PATHINFO_FILENAME);
        //     // $cloudinaryUrl = Cloudinary::getUrl($publicIdImage);
        
        //     // if (!$cloudinaryUrl) {
                $posterUrl = "https://img.ophim9.cc/uploads/movies/{$thumb_url}";
                try {    
                    $publicIdImage = "uploads/movies/" . pathinfo($thumb_url, PATHINFO_FILENAME);

                        Cloudinary::upload($posterUrl, [
                            'format' => 'webp',
                            'public_id' => $publicIdImage,
                            'options' => [
                                'format' => 'webp',
                                'quality' => 'auto',
                                'overwrite' => false,
                            ],
                                'transformation' => [
                                    'width' => 280,
                                ],
                        ]);

                } catch (\Exception $e) {
                    // Handle the exception (e.g., log or ignore)
                    // You can log the exception or simply ignore it, depending on your requirements
                }
            // }
        }

}



}


