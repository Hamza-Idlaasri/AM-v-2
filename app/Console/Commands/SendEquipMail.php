<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\EquipMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Notif;

class SendEquipMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:equip';

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
        $last_notif_readed = Notif::orderBy('read_at->equips','DESC')->first()->read_equips_at;

        $equips = DB::table('nagios_notifications')
            ->join('nagios_services','nagios_services.service_object_id','=','nagios_notifications.object_id')
            ->join('nagios_hosts','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->select('nagios_services.display_name as equip_name','nagios_hosts.display_name as box_name','nagios_notifications.*')
            ->where('nagios_notifications.start_time','>=',$last_notif_readed)
            ->get();

               
        $users = User::all();

        foreach ($users as $user) {
         
            if ($user->notified) {

                Mail::to($user->email)->send(new EquipMail($equips));
                $send = new EquipMail($equips);
            }
        }

        return 0;
    }
}
