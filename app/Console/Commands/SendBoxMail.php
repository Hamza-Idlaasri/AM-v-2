<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\BoxMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Notif;

class SendBoxMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:box';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $last_notif_readed = Notif::orderBy('read_at->boxes','DESC')->first()->read_boxes_at;

        $boxes = DB::table('nagios_notifications')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_notifications.object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.*','nagios_notifications.*')
            ->where('nagios_notifications.start_time','>=',$last_notif_readed)
            ->get();

               
        $users = User::all();

        foreach ($users as $user) {
         
            if ($user->notified) {

                Mail::to($user->email)->send(new BoxMail($boxes));
                $send = new BoxMail($boxes);
            }
        }

        return 0;
    }
}
