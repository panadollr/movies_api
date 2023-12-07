<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('crawl:movies')->withoutOverlapping();
        // $schedule->command('crawl:movies')->withoutOverlapping()->runInBackground();
        // $schedule->command('crawl:movie_details')->withoutOverlapping()->runInBackground();
        $schedule->command('crawl:movies')->withoutOverlapping()->runInBackground()->then(function () {
            $this->call('crawl:movie_details');
        });
    }


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
