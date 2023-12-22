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
    try {
    $imageName = $slug . '-poster.jpg';
    $imageUrl = config('api_settings.image_domain') . $imageName;
    $client = new Client();

    $response = $client->get($imageUrl);

        // Process the image
        $image = Image::make($response->getBody()->detach());
        $webpImage = $image->encode('webp', 90);
        $stream = $webpImage->stream();

        // Return the stream as a response
        return response()->stream(
            function () use ($stream) {
                echo $stream;
            },
            200,
            [
                'Content-Type' => 'image/webp',
                'Cache-Control' => 'public, max-age=86400',
            ]
        );
  
    } catch (\Throwable $th) {
        return response()->json(['msg' => 'Error fetching image'], 500);
    }
}

public function getPoster($slug)
{
    try {
        $imageName = $slug . '-thumb.jpg';
        $imageUrl = config('api_settings.image_domain') . $imageName;
        $client = new Client();
        $response = $client->get($imageUrl);
        
        $imageContents = $response->getBody()->getContents();
        $contentType = $response->getHeaderLine('Content-Type');

        $response = response($imageContents, 200, ['Content-Type' => $contentType]);

        return $response;
    } catch (\Throwable $th) {
        return response()->json(['msg' => 'Error processing image', 'error' => $th->getMessage()], 500);
    }
}

}
