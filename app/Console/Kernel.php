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
    protected $commands = [
        'App\Console\Commands\SendHostMail',
        'App\Console\Commands\SendServiceMail',
        'App\Console\Commands\SendBoxMail',
        'App\Console\Commands\SendEquipMail'
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('notif:box')
            ->timezone('Africa/Casablanca')
            ->everyFiveMinutes();

        $schedule->command('notif:host')
            ->timezone('Africa/Casablanca')
            ->everyFiveMinutes();

        $schedule->command('notif:service')
            ->timezone('Africa/Casablanca')
            ->everyFiveMinutes();
    
        $schedule->command('notif:equip')
            ->timezone('Africa/Casablanca')
            ->everyFiveMinutes();
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
