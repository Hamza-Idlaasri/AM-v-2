<style>
    .table-details td{
        background-color : rgba(240, 240, 240, 0.6); 
        font-size: 14px;
        /* font-weight: bold; */
        text-align: left
    }
    .table-details{
        width: 70%;
        margin:auto
    }
    .container{
        padding: 20px 0px
    }
    .left-coll{
        width: 25%;
    }
</style>

<div class="container text-center w-100" wire:poll.5000>
    
    <h4>Equiepement : <strong>{{ $equip[0]->equip_name }}</strong></h4>
    
    <h6>On Box : <strong>{{ $equip[0]->box_name }}</strong></h6>
    
    <br>
    
    <table class="table table-bordered table-details table-hover">

        <tr>
            <td class="left-coll font-weight-bolder ">Current State</td>
            @switch($equip[0]->current_state)

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
            <td>{{ $equip[0]->output }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Current Attempt</td>
            <td>{{ $equip[0]->current_check_attempt }} / {{$equip[0]->max_check_attempts}} 

                @if ($equip[0]->state_type)
                    ( SOFT state )
                @else
                    ( HARD state ) 
                @endif

            </td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Execution Time</td>
            <td>{{  $equip[0]->execution_time }} s</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Check Interval</td>
            <td>{{  $equip[0]->check_interval }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Retry Check every </td>
            <td>{{  $equip[0]->retry_interval }} min</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Max Check Attempts</td>
            <td>{{  $equip[0]->max_check_attempts }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">On Check</td>

            @if ($equip[0]->has_been_checked)
                <td>Yes</td>
            @else
                <td class="text-danger">No</td>
            @endif
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Last Check</td>
            <td>{{ $equip[0]->last_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Next Check</td>
            <td>{{ $equip[0]->next_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Last Update</td>
            <td>{{ $equip[0]->status_update_time }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Flapping</td>

            @switch($equip[0]->is_flapping)
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
            
            @if ($equip[0]->notifications_enabled)
                <td>Yes</td>
            @else
                <td>No</td>
            @endif
            
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder ">Check Type</td>

            @switch($equip[0]->check_type)
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