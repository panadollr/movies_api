<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\MovieDetails;
use Illuminate\Console\Command;

class UpdateMoviesData extends Command
{
    protected $signature = 'update:movies-data';

    protected $description = 'Update movies data from movie_details table.';

    public function handle()
    {
        $this->updateMoviesDataFromMovieDetails();
    }

    private function updateMoviesDataFromMovieDetails()
    {
        $attributes = [
            '_id', 'content', 'type', 'status', 'is_copyright', 'sub_docquyen',
            'trailer_url', 'time', 'episode_current', 'episode_total',
            'quality', 'lang', 'notify', 'showtimes', 'view', 'actor', 'director', 'category', 'country'
        ];
    
        Movie::orderBy('_id')->where('content', null)->chunk(300, function ($movies) use ($attributes) {
            foreach ($movies as $movie) {
                $movieDetail = MovieDetails::where('_id', $movie->_id)->first();
    
                if ($movieDetail) {
                    $updates = [];
    
                    foreach ($attributes as $attribute) {
                        $newValue = $movieDetail->$attribute;
                        $updates[$attribute] = $newValue;
                    }
    
                    if (!empty($updates)) {
                        Movie::where('_id', $movie->_id)->update($updates);
                    }
                    print($movieDetail->_id);
                }
                // print($movie->_id);
            }
        });
    
        $this->info('Movies data updated successfully.');
    }    
}
