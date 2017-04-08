<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
   protected $commands = [
        // \App\Console\Commands\Inspire::class,
    ];


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
  protected function schedule(Schedule $schedule)
    {
        // BOT 1
        $schedule->call(function () {
            $collector_id = 1;
            $location = \App\Collectors::find($collector_id)->location;
            $range = range(2000, 10000);
            $saveData = array_merge([
                'collector_id' => $collector_id, 
                'ppm' => $range[array_rand($range)]
            ], 
            [
                'location' => $location
            ]);
            \App\Measurements::create($saveData);
        })->everyMinute();

        //BOT 2
        $schedule->call(function () {
            $collector_id = 2;
            $location = \App\Collectors::find($collector_id)->location;
            $range = range(200, 1000);
            $saveData = array_merge([
                'collector_id' => $collector_id, 
                'ppm' =>$range[array_rand($range)]
            ], 
            [
                'location' => $location
            ]);
            \App\Measurements::create($saveData);
        })->everyMinute();
        
        // //BOT 3
        // $schedule->call(function () {
        //     $collector_id = 2;
        //     $location = \App\Collectors::find($collector_id)->location;
        //     $range = range(200, 1000);
        //     $saveData = array_merge([
        //         'collector_id' => $collector_id, 
        //         'ppm' =>$range[array_rand($range)]
        //     ], 
        //     [
        //         'location' => $location
        //     ]);
        //     \App\Measurements::create($saveData);
        // })->everyMinute();
    }
}
