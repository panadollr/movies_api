<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Movie;

class RefreshData extends Command
{
    protected $signature = 'refresh:data';

    protected $description = 'Command description';

    public function handle()
    {
        $this->refreshMovies();
    }

    protected function refreshMovies(){
        $affectedRows = Movie::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('movie_details as md')
                ->whereColumn('md._id', 'movies._id');
        })->count();
    
        print_r($affectedRows);
    }
}
