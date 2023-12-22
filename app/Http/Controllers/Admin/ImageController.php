<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Stream;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class ImageController
{
   
public function getThumb($slug)
{
    // try {
    // $imageName = $slug . '-poster.jpg';
    // $imageUrl = config('api_settings.image_domain') . $imageName;
    // $client = new Client();

    // $response = $client->get($imageUrl);

    //     // Process the image
    //     $image = Image::make($response->getBody()->detach());
    //     $webpImage = $image->encode('webp', 90);
    //     $stream = $webpImage->stream();

    //     // Return the stream as a response
    //     return response()->stream(
    //         function () use ($stream) {
    //             echo $stream;
    //         },
    //         200,
    //         [
    //             'Content-Type' => 'image/webp',
    //             'Cache-Control' => 'public, max-age=86400',
    //         ]
    //     );
  
    // } catch (\Throwable $th) {
    //     return response()->json(['msg' => 'Error fetching image'], 500);
    // }

    return $slug;
}

public function getPoster($slug)
{
    try {
        $width = request()->input('w', 280);
        $imageName = $slug . '-thumb.jpg';
        $imageUrl = config('api_settings.image_domain') . $imageName;
        $client = new Client();
        $response = $client->get($imageUrl);
        
        // Download the image
        // $image = Image::make($imageUrl);
        $image = Image::make($response->getBody()->detach());

        // Resize the image to a specific width (e.g., 300px)
        $image->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Convert the image to WebP format
        $webpImage = $image->encode('webp', 90);

        // Convert the WebP image to a stream
        $stream = $webpImage->stream();

        $response = response()->stream(
            function () use ($stream) {
                echo $stream;
            },
            200,
            ['Content-Type' => 'image/webp']
        );

        return $response;
    } catch (\Throwable $th) {
        return response()->json(['msg' => 'Không tìm thấy ảnh'], 404);
    }
}
}
