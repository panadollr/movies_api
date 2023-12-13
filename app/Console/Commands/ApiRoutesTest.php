<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class ApiRoutesTest extends Command
{
    protected $signature = 'test:api_routes';
    protected $description = 'Test all API routes in api.php file';

    public function handle()
    {
        $this->testAllApiRoutes();
    }

    public function testAllApiRoutes()
    {
        $routes = $this->getRouteList();

        foreach ($routes as $route) {
            $response = $this->sendRequest($route);

            if ($response->getStatusCode() === 200) {
                $this->info("API route {$route} is successful. Status code: {$response->getStatusCode()}");
            } else {
                $this->error("API route {$route} failed. Status code: {$response->getStatusCode()}");
            }
        }
    }

    protected function getRouteList()
    {
        $routes = [
            'phim-le',
            'phim-bo',
            'hoat-hinh',
            'subteam',
            'tv-shows',
            'phim-sap-chieu',
            'the-loai/{category-slug}',
            'quoc-gia/{country-slug}',
            'xu-huong',
            'moi-cap-nhat/phim-bo',
            'moi-cap-nhat/phim-le',
            'hom-nay-xem-gi',
            'tim-kiem',
            'phim/{movie-slug}',
            'phim-tuong-tu/{movie-slug}',
            'tin-tuc',
            'tin-tuc/{tin-tuc-slug}',
            'tin-tuc-tuong-tu/{tin-tuc-slug}',
        ];

        return $routes;
    }

    protected function sendRequest($route)
    {
        $client = new Client();
        $url = $this->generateUrl($route);
        $response = $client->get($url);

        return $response;
    }

    protected function generateUrl($route)
{
    $parameters = [
        'category-slug' => 'hanh-dong',
        'country-slug' => 'nhat-ban',
        'movie-slug' => 'tapie',
        'tin-tuc-slug' => 'review-nhat-niem-quan-son-tap-9-10-nham-nhu-y-het-doi-sinh-con-ninh-vien-chau-tiec-hui-hui',
    ];

    foreach ($parameters as $key => $value) {
        $route = str_replace("{{$key}}", $value, $route);
    }

    return "http://localhost:8081/movie_api/public/{$route}";
}
}
