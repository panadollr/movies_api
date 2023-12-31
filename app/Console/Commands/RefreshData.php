<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Movie;
use App\Models\MovieDetails;

class RefreshData extends Command
{
    protected $signature = 'refresh:data';

    protected $description = 'Command description';

    public function handle()
    {
        $this->refreshMovies();
        // $this->deleteInValidCountryMovies();
    }

    protected function refreshMovies(){
        $affectedRows = Movie::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('movie_details as md')
                ->whereColumn('md._id', 'movies._id');
        })->count();
    
        print_r($affectedRows);
    }

    //xóa các phim có quốc gia không hợp lệ và có thể loại là tv-shows
    protected function deleteInValidCountryMovies(){
        try {
            $countries = config('api_settings.countries');
            $movieIds = [];
            $movie_details = MovieDetails::select('_id', 'country', 'type')->get();
            $count = 0;
            $tvshowsCount = 0;
            
            foreach ($movie_details as $movie_detail) {
                $type = $movie_detail->type;
                if($type != 'tvshows'){

                $countriesArray = json_decode($movie_detail->country, true);
                if (is_array($countriesArray)) {
                    $inValidCountries = 0;
                    
                    foreach ($countriesArray as $country) {
                        if (isset($country['slug'])) {
                            $slug = $country['slug'];

                            if (!isset($countries[$slug])) {
                                $inValidCountries ++;
                            }
                        }
                    }
    
                    if (!empty($inValidCountries) && $inValidCountries === count($countriesArray)) {
                        $movieIds[] = [
                            '_id' => $movie_detail->_id,
                        ];
                        $count ++;
                    }
                }

            } else {
                $movieIds[] = [
                    '_id' => $movie_detail->_id,
                ];
                $tvshowsCount ++;
            }
            
            }

            if (!empty($movieIds)) {
            Movie::whereIn('_id', array_column($movieIds, '_id'))
                ->delete();
            MovieDetails::whereIn('_id', array_column($movieIds, '_id'))
                                        ->delete();
            }

            print_r("Đã xóa $count phim có quốc gia không hợp lệ và $tvshowsCount tv-shows !");
    
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
