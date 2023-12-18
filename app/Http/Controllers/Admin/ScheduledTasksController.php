<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use App\Jobs\CrawlMovieDetailsJob;
use App\Jobs\SendMoviesCrawlJob;
use App\Models\Movie;
use App\Models\MovieDetails;

class ScheduledTasksController
{

    public function runScheduledCommands()
{
    try {
        Artisan::call('schedule:run');
        return response()->json(['message' => 'command executed.'], 200);
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
}


    public function runCrawlMoviesCommand()
{
    try {
        Artisan::call('crawl:movies');
        return response()->json(['message' => 'Crawl movies command executed.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function runCrawlMovieDetailsCommand()
{
    try {
        Artisan::call('crawl:movie_details');
        return response()->json(['message' => 'Crawl movie details command executed.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function deleteOldMovies()
{
    try {
        $oldMovies = Movie::where('year', '<', 2005)->get();

        $movieIdsToDelete = $oldMovies->pluck('_id')->toArray();

        $movieDetailsIdsToDelete = MovieDetails::whereIn('_id', $movieIdsToDelete)->pluck('_id')->toArray();

        Movie::destroy($movieIdsToDelete);
        MovieDetails::destroy($movieDetailsIdsToDelete);

        return 'success';
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
}

}
