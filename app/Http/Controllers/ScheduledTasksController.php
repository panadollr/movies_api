<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ScheduledTasksController
{


    public function runCrawlMoviesCommand()
{
    try {
        set_time_limit(300);
        Artisan::call('crawl:movies');
        return response()->json(['message' => 'Crawl Movies command executed.'], 200);
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
}


}
