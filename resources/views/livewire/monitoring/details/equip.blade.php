<div class="container text-center w-100" style="padding: 20px 0px" wire:poll>
    
    <h4>Equiepement : <strong>{{ $equip->equip_name }}</strong></h4>
    
    <h6>On Box : <strong>{{ $equip->box_name }}</strong></h6>
    
    <br>
    
    <table class="table table-bordered table-details table-hover">

        <tr>
            <td class="left-coll font-weight-bolder ">Current State</td>
            @switch($equip->current_state)

                @case(0)
                    <td><span class="badge badge-success">Ok</span></td>
                    @break
                @case(1)
                    <td><span class="badge badge-warning">Warning</span></td>
                    @break
                @case(2)
                    <td><span class="badge badge-danger">Critical</span></td>
                    @break
                @case(3)
                    <td><span class="badge badge-unknown">Ureachable</span></td>
                    @break
                @default
                    
            @endswitch
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">State Information</td>
            <td>{{ $equip->output }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Input Number</td>
            <td>{{ $equip->check_command }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Current Attempt</td>
            <td>{{ $equip->current_check_attempt }} / {{$equip->max_check_attempts}} 

                @if ($equip->state_type)
                    ( SOFT state )
                @else
                    ( HARD state ) 
                @endif

            </td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Execution Time</td>
            <td>{{  $equip->execution_time }} s</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Check Interval</td>
            <td>{{  $equip->check_interval }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Retry Check every </td>
            <td>{{  $equip->retry_interval }} sec</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Max Check Attempts</td>
            <td>{{  $equip->max_check_attempts }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">On Check</td>

            @if ($equip->has_been_checked)
                <td>Yes</td>
            @else
                <td class="text-danger">No</td>
            @endif
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Last Check</td>
            <td>{{ $equip->last_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Next Check</td>
            <td>{{ $equip->next_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Last Update</td>
            <td>{{ $equip->status_update_time }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Flapping</td>

            @switch($equip->is_flapping)
                @case(0)
                    <td>NO</td>
                    @break
                @case(1)
                    <td>YES</td>
                    @break
                @default
                    
            @endswitch
            
        </tr>

        <tr>
            
            <td class="left-coll font-weight-bolder">Send Notifications</td>
            
            @if ($equip->notifications_enabled)
                <td>Yes</td>
            @else
                <td>No</td>
            @endif
            
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Check Type</td>

            @switch($equip->check_type)
                @case(0)
                    <td>ACTIVE</td>
                    @break
                @case(1)
                    <td>PASSIVE</td>
                    @break
                @default
                    
            @endswitch
        </tr>

    </table>
</div>