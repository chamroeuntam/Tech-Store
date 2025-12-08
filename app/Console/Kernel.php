<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\Admin\SalesReportController;
use Illuminate\Http\Request;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $today = now()->toDateString();
            app(SalesReportController::class)
                ->generate(new Request([
                    'start_date' => $today,
                    'end_date' => $today,
                    'type' => 'daily'
                ]));
        })->daily();
    }
}

