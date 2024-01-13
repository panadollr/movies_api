<?php

namespace App\Console\Commands;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Console\Command;

class testCommand extends Command
{
    protected $signature = 'test:command';
    protected $description = 'Command description';

    public function handle()
    {
        //upload thumb customize
        // $publicId = "thumbs/than-an-poster";
        // Cloudinary::upload('https://img.ophim9.cc/uploads/movies/than-an-poster.jpg', [
        //     'format' => 'webp',
        //     'public_id' => $publicId,
        //     'quality' => '55',
        //     'overwrite' => false,
        //     'transformation' => [
        //         'width' => 1000,
        //         'crop' => 'scale'
        //     ],
        // ]);

        //upload poster customize
        $publicId = "posters/chang-quy-cua-toi-poster";
        Cloudinary::upload('https://img.ophim9.cc/uploads/movies/chang-quy-cua-toi-thumb.jpg', [
            'format' => 'webp',
            'public_id' => $publicId,
            'quality' => '45',
            'overwrite' => false,
            'transformation' => [
                'width' => 300,
                'crop' => 'scale'
            ],
        ]);
    }
}
