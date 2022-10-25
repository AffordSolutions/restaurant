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

    // For schedules to run locally, run the artisan command: 'php artisan schedule:work' 
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function(){
            $controller = new \App\Http\Controllers\PollController();
            $controller->poll();
        })->everyMinute(); // The task scheduler will run every minute.

        $schedule->call(function(){
            $controller = new \App\Http\Controllers\DeliveryController();
            $controller->getUpdateOnDeliveries();
        })->everyMinute(); // The task scheduler will run every minute.
// Was trying to learn to integrate Sendgrid Mail Send API:
        /*$schedule->call(function(){
            $controller = new \App\Http\Controllers\tempEmailController();
            $controller->sendViaSendgrid();
        })->everyMinute(); // The task scheduler will run every minute.
    */

        /*$schedule->call(function(){
            $controller = new \App\Http\Controllers\DeliveryController();
            $controller->sendViaSendgrid();
        })->everyMinute(); // The task scheduler will run every minute.
        */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
