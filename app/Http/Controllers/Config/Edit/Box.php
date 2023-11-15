<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

class Box extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function editBox(Request $request, $box_object_id)
    {
        $site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        // validation
        $this->validate($request, [

            // 'boxName' => 'required|min:2|max:20|unique:nagios_hosts,display_name|regex:/^[a-zA-Z0-9-_+ ]/',
            'boxName' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/',
            'addressIP' => 'required|min:7|max:15',
            'check_interval' => 'required|min:1|max:10000',
            'retry_interval' => 'required|min:1|max:10000',
            'max_attempts' => 'required|min:1|max:100',
            'notif_interval' => 'required|min:1|max:1000',
            'site' => 'required_unless:site,!=,specific'

        ], [
            'addressIP.required' => 'the IP address field is empty',
            'site.required_unless' => 'Please choose a site'
        ]);

        $old_box_details = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $box_object_id)
            ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname', 'BOXTYPE')
            ->select('nagios_hosts.*', 'nagios_customvariables.varvalue as box_type')
            ->first();

        $equips = DB::table('nagios_hosts')
            ->where('nagios_hosts.host_object_id', $box_object_id)
            ->join('nagios_services', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
            ->select('nagios_services.display_name as equip_name')
            ->get();

        // Parent relationship
        if ($request->input('boxes'))
            $define_host = "define host {\n\tuse\t\t\t\t\tbox-server\n\thost_name\t\t" . $request->boxName . "\n\talias\t\t\tbox\n\taddress\t\t\t" . $request->addressIP . "\n\t_site\t\t\t" . $request->site == 'specific' ? $site_name : $request->site . "\n\t_boxType\t\t\t" . $old_box_details->box_type . "\n\tparents\t\t\t" . $request->input('boxes');
        else
            $define_host = "define host {\n\tuse\t\t\t\t\tbox-server\n\thost_name\t\t\t\t" . $request->boxName . "\n\talias\t\t\t\t\tbox\n\taddress\t\t\t\t\t" . $request->addressIP . "\n\t_site\t\t\t" . $request->site == 'specific' ? $site_name : $request->site . "\n\t_boxType\t\t\t" . $old_box_details->box_type;

        // Normal Check Interval
        // Convert Time
        $request->check_interval = floatval(round($request->check_interval / 60, 2));
        // Normal Check Interval
        // if($old_box_details->check_interval != $request->check_interval)
        $define_host = $define_host . "\n\tcheck_interval\t\t\t\t" . $request->check_interval;

        // Retry Check Interval
        // Convert Time
        $request->retry_interval = floatval(round($request->retry_interval / 60, 2));
        // Check Time
        // if($old_box_details->retry_interval != $request->retry_interval)
        $define_host = $define_host . "\n\tretry_interval\t\t\t\t" . $request->retry_interval;

        // Max Check Attempts
        // if($old_box_details->max_check_attempts != $request->max_attempts)
        $define_host = $define_host . "\n\tmax_check_attempts\t\t\t" . $request->max_attempts;

        // Notification Interval
        // if($old_box_details->notification_interval != $request->notif_interval)
        $define_host = $define_host . "\n\tnotification_interval\t\t\t" . $request->notif_interval;

        // Check this host
        if ($request->query('check'))
            $define_host = $define_host . "\n\tactive_checks_enabled\t\t\t" . $request->query('check');

        // Enable notifications
        if ($request->query('active_notif'))
            $define_host = $define_host . "\n\tnotifications_enabled\t\t\t" . $request->query('active_notif');

        $define_host = $define_host . "\n}\n\n";

        if ($old_box_details->display_name == $request->boxName) {

            $path = "/usr/local/nagios/etc/objects/boxes/" . $request->boxName . "/" . $request->boxName . ".cfg";

            file_put_contents($path, $define_host);
        } else {

            $path = "/usr/local/nagios/etc/objects/boxes/" . $old_box_details->display_name . "/" . $old_box_details->display_name . ".cfg";

            file_put_contents($path, $define_host);

            rename("/usr/local/nagios/etc/objects/boxes/" . $old_box_details->display_name . "/" . $old_box_details->display_name . ".cfg", "/usr/local/nagios/etc/objects/boxes/" . $old_box_details->display_name . "/" . $request->boxName . ".cfg");

            rename("/usr/local/nagios/etc/objects/boxes/" . $old_box_details->display_name, "/usr/local/nagios/etc/objects/boxes/" . $request->boxName);

            foreach ($equips as $equip) {

                $content = file_get_contents("/usr/local/nagios/etc/objects/boxes/" . $request->boxName . "/" . $equip->equip_name . ".cfg");
                $content = str_replace($old_box_details->display_name, $request->boxName, $content);
                file_put_contents("/usr/local/nagios/etc/objects/boxes/" . $request->boxName . "/" . $equip->equip_name . ".cfg", $content);

                // Editing in nagios.cfg file
                $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
                $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/boxes/" . $old_box_details->display_name . "/" . $equip->equip_name . ".cfg", "/usr/local/nagios/etc/objects/boxes/" . $request->boxName . "/" . $equip->equip_name . ".cfg", $nagios_file_content);
                file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
            }

            // Editing in nagios.cfg file
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("/usr/local/nagios/etc/objects/boxes/" . $old_box_details->display_name . "/" . $old_box_details->display_name . ".cfg", "/usr/local/nagios/etc/objects/boxes/" . $request->boxName . "/" . $request->boxName . ".cfg", $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
        }

        //-------------------------------- Edit name of the box in equips_names & equips_details tables -------------------------//

        EquipsDetail::where(['box_name' => $old_box_details->display_name])->update(['box_name' => $request->boxName]);
        EquipsNames::where(['box_name' => $old_box_details->display_name])->update(['box_name' => $request->boxName]);

        //-------------------------------- Edit box_name on boxgroups ----------------------------------------------//

        $boxgroup_member_on = DB::table('nagios_hostgroup_members')
            ->where('nagios_hostgroup_members.host_object_id', $box_object_id)
            ->join('nagios_hosts', 'nagios_hostgroup_members.host_object_id', '=', 'nagios_hosts.host_object_id')
            ->join('nagios_hostgroups', 'nagios_hostgroup_members.hostgroup_id', '=', 'nagios_hostgroups.hostgroup_id')
            ->select('nagios_hostgroups.alias as boxgroup_name', 'nagios_hostgroups.hostgroup_object_id', 'nagios_hosts.display_name as box_name')
            ->get();

        $groups = [];

        foreach ($boxgroup_member_on as $boxgroup) {

            $boxgroup_members = DB::table('nagios_hostgroup_members')
                ->join('nagios_hosts', 'nagios_hostgroup_members.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->join('nagios_hostgroups', 'nagios_hostgroup_members.hostgroup_id', '=', 'nagios_hostgroups.hostgroup_id')
                ->select('nagios_hostgroups.alias as boxgroup_name', 'nagios_hostgroups.hostgroup_object_id', 'nagios_hosts.display_name as box_name')
                ->where('nagios_hostgroups.hostgroup_object_id', $boxgroup->hostgroup_object_id)
                ->get();

            $members = [];

            foreach ($boxgroup_members as $member) {
                array_push($members, $member->box_name);
            }

            array_push($groups, ['boxgroup_name' => $boxgroup->boxgroup_name, 'members' => $members]);
        }

        $old_groups = $groups;

        for ($i = 0; $i < sizeof($groups); $i++) {

            $groups[$i]['members'] = str_replace($old_box_details->display_name, $request->boxName, $groups[$i]['members']);

            if (sizeof($groups[$i]['members'])) {

                // Editing in boxgroups file
                $path = "/usr/local/nagios/etc/objects/boxgroups/" . $groups[$i]['boxgroup_name'] . ".cfg";

                $define_boxgroup = "\ndefine hostgroup {\n\thostgroup_name\t\t" . $groups[$i]['boxgroup_name'] . "\n\talias\t\t\t\t" . $groups[$i]['boxgroup_name'] . "\n\tmembers\t\t\t\t" . implode(',', $groups[$i]['members']) . "\n}\n";

                $file = fopen($path, 'w');

                fwrite($file, $define_boxgroup);

                fclose($file);
            }
        }

        //--------------------------------- Edit box_name on equipgroups -----------------------------------------//

        $equipgroups = DB::table('nagios_servicegroup_members')
            ->join('nagios_services', 'nagios_servicegroup_members.service_object_id', '=', 'nagios_services.service_object_id')
            ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
            ->join('nagios_servicegroups', 'nagios_servicegroup_members.servicegroup_id', '=', 'nagios_servicegroups.servicegroup_id')
            ->where('nagios_hosts.host_object_id', $box_object_id)
            ->select('nagios_servicegroups.alias as equipgroup_name', 'nagios_servicegroups.servicegroup_object_id', 'nagios_services.display_name as equip_name', 'nagios_hosts.display_name as box_name')
            ->get();

        $groups = [];

        foreach ($equipgroups as $group) {

            $equipgroup_members = DB::table('nagios_servicegroup_members')
                ->join('nagios_services', 'nagios_servicegroup_members.service_object_id', '=', 'nagios_services.service_object_id')
                ->join('nagios_hosts', 'nagios_services.host_object_id', '=', 'nagios_hosts.host_object_id')
                ->join('nagios_servicegroups', 'nagios_servicegroup_members.servicegroup_id', '=', 'nagios_servicegroups.servicegroup_id')
                ->select('nagios_servicegroups.alias as equipgroup_name', 'nagios_servicegroups.servicegroup_object_id', 'nagios_services.display_name as equip_name', 'nagios_hosts.display_name as box_name')
                ->where('nagios_servicegroups.servicegroup_object_id', $group->servicegroup_object_id)
                ->get();

            $members = [];

            foreach ($equipgroup_members as $member) {
                array_push($members, $member->box_name . ',' . $member->equip_name);
            }

            array_push($groups, ['equipgroup_name' => $group->equipgroup_name, 'members' => $members]);
        }

        $groups = array_values(array_unique($groups, SORT_REGULAR));

        for ($i = 0; $i < sizeof($groups); $i++) {

            foreach ($equipgroups as $equipgroup) {
                $groups[$i]['members'] = str_replace($old_box_details->display_name . ',', $request->boxName . ',', $groups[$i]['members']);
            }

            if (sizeof($groups[$i]['members'])) {

                // Editing in equipgroup file
                $path = "/usr/local/nagios/etc/objects/equipgroups/" . $groups[$i]['equipgroup_name'] . ".cfg";

                $define_equipgroup = "\ndefine servicegroup {\n\tservicegroup_name\t\t" . $groups[$i]['equipgroup_name'] . "\n\talias\t\t\t\t" . $groups[$i]['equipgroup_name'] . "\n\tmembers\t\t\t\t" . implode(',', $groups[$i]['members']) . "\n}\n";

                $file = fopen($path, 'w');

                fwrite($file, $define_equipgroup);

                fclose($file);
            }
        }

        //---------------------------------- Edit the Host as parrent of another Host ------------------------------//

        $parent_host = DB::table('nagios_host_parenthosts')
            ->where('nagios_host_parenthosts.parent_host_object_id', $box_object_id)
            ->join('nagios_hosts', 'nagios_host_parenthosts.host_id', '=', 'nagios_hosts.host_id')
            ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.alias as host_type')
            ->get();

        foreach ($parent_host as $host) {

            if ($host->host_type == 'host') {
                $directory = "hosts";
            }

            if ($host->host_type == 'box') {
                $directory = "boxes";
            }

            $myFile = "/usr/local/nagios/etc/objects/" . $directory . "/" . $host->box_name . "/" . $host->box_name . ".cfg";
            $lines = file($myFile);
            $parents_line = $lines[5];

            // Editing in host .cfg file
            $host_file_content = file_get_contents("/usr/local/nagios/etc/objects/" . $directory . "/" . $host->box_name . "/" . $host->box_name . ".cfg");
            $host_file_content = str_replace($lines[5], "\tparents\t\t\t" . $request->boxName . "\n", $host_file_content);
            file_put_contents("/usr/local/nagios/etc/objects/" . $directory . "/" . $host->box_name . "/" . $host->box_name . ".cfg", $host_file_content);
        }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return redirect()->route('config-boxes');
    }
}
