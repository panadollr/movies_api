<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduledTasksController extends Controller
{

    public function runScheduledTasks()
    {
         try {
            \Artisan::call('schedule:run');
            return "Scheduled tasks executed successfully.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

}
