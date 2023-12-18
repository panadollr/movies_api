<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use GuzzleHttp\Client;

use App\Models\Movie;


class uploadImageToCloudinary extends Command
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

        $allMovies = Movie::all();
        $client = new Client();
        
        // foreach($allMovies as $movie){

        //     $poster_url = "https://img.ophim9.cc/uploads/movies/{$movie->poster_url}";
        //     $response = $client->get($poster_url);
        //     if($response->getStatusCode() == 200){
        //         $publicIdImage = "uploads/movies/" . pathinfo($movie->poster_url, PATHINFO_FILENAME);
        //         $cloudinaryUrl = Cloudinary::getUrl($publicIdImage);
        
        //         if (!$cloudinaryUrl) {
        //             Cloudinary::upload($poster_url, [
        //                 'format' => 'webp',
        //                 'public_id' => $publicIdImage,
        //                 'w' => 800,
        //                 'h' => 'auto',
        //                 'q' => 'auto'
        //             ]);
        //         }
        //     }
            
        // }

        foreach ($allMovies as $movie) {
            $poster_url = "https://img.ophim9.cc/uploads/movies/{$movie->poster_url}";
        
            try {
                // Attempt to get the image content
                $response = $client->get($poster_url);
        
                // If the request is successful, proceed with the upload
                $publicIdImage = "uploads/movies/" . pathinfo($movie->poster_url, PATHINFO_FILENAME);
                $cloudinaryUrl = Cloudinary::getUrl($publicIdImage);
        
                if (!$cloudinaryUrl) {
                    Cloudinary::upload($poster_url, [
                        'format' => 'webp',
                        'public_id' => $publicIdImage,
                        'w' => 800,
                        'h' => 'auto',
                        'q' => 'auto'
                    ]);
                }
            } catch (\Exception $e) {
                // Handle the exception (e.g., log or ignore)
                // You can log the exception or simply ignore it, depending on your requirements
            }
        }

}
}
