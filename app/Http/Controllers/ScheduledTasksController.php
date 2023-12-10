<?php

namespace App\Http\Controllers;

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


//     public function runCrawlMoviesCommand()
// {
//     try {
//         set_time_limit(300);
//         Artisan::call('crawl:movies');
//         return response()->json(['message' => 'Crawl Movies command executed.'], 200);
//     } catch (\Exception $e) {
//         return "Error: " . $e->getMessage();
//     }
// }


// public function runCrawlMovieDetailsCommand()
// {
//     try {
//         // Dispatch the job instead of directly calling the Artisan command
//         dispatch(new SendMoviesCrawlJob());

//         return response()->json(['message' => 'Crawl Movie Details command dispatched.'], 200);
//     } catch (\Exception $e) {
//         return "Error: " . $e->getMessage();
//     }
// }


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
