<style>
    .table-details td{
        background-color : rgb(240, 240, 240, 0.6); 
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
    <h4>{{ $box[0]->display_name }}</h4>
    <h6>{{ $box[0]->address}}</h6>
    <br>
    <table class="table table-bordered table-hover table-details">

        <tr>
            <td class="left-coll font-weight-bolder">Current State</td>
            @switch($box[0]->current_state)
                
                    @case(0)
                        <td><span class="badge badge-success">Up</span></td>
                        @break

                    @case(1)
                        <td><span class="badge badge-danger">Down</span></td>
                        @break
                            
                    @case(2)
                        <td><span class="badge badge-unknown">Ureachable</span></td>
                        @break

                    @default
                        
            @endswitch
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">State Information</td>
            <td>{{ $box[0]->output }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Current Attempt</td>
            <td>{{ $box[0]->current_check_attempt }} / {{$box[0]->max_check_attempts}} 

                @if ($box[0]->state_type)
                    ( SOFT state )
                @else
                    ( HARD state )
                @endif
                
            </td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Execution Time</td>
            <td>{{  $box[0]->execution_time }} s</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Check Interval</td>
            <td>{{  $box[0]->check_interval }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Retry Check every</td>
            <td>{{  $box[0]->retry_interval }} min</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Max Check Attempts</td>
            <td>{{  $box[0]->max_check_attempts }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">On Check</td>

            @if ($box[0]->has_been_checked)
                <td>Yes</td>
            @else
                <td class="text-danger">No</td>
            @endif

        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Last Check</td>
            <td>{{ $box[0]->last_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Next Check</td>
            <td>{{ $box[0]->next_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Last Update</td>
            <td>{{ $box[0]->status_update_time }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Flapping</td>

            @switch($box[0]->is_flapping)
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
            
            @if ($box[0]->notifications_enabled)
                <td>Yes</td>
            @else
                <td>No</td>
            @endif
            
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Check Type</td>

            @switch($box[0]->check_type)

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