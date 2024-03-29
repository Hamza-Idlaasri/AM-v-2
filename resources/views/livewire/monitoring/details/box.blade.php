<div class="container text-center w-100" style="padding: 20px 0px" wire:poll>
    <h4>{{ $box->display_name }}</h4>
    <h6>{{ $box->address}}</h6>
    <br>
    <table class="table table-bordered table-hover table-details">

        <tr>
            <td class="left-coll font-weight-bolder">Current State</td>
            @switch($box->current_state)
                
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
            <td>{{ $box->output }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Current Attempt</td>
            <td>{{ $box->current_check_attempt }} / {{$box->max_check_attempts}} 

                @if ($box->state_type)
                    ( SOFT state )
                @else
                    ( HARD state )
                @endif
                
            </td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Execution Time</td>
            <td>{{  $box->execution_time }} s</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Check Interval</td>
            <td>{{  $box->check_interval }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Retry Check every</td>
            <td>{{  $box->retry_interval }} sec</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Max Check Attempts</td>
            <td>{{  $box->max_check_attempts }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">On Check</td>

            @if ($box->has_been_checked)
                <td>Yes</td>
            @else
                <td class="text-danger">No</td>
            @endif

        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Last Check</td>
            <td>{{ $box->last_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Next Check</td>
            <td>{{ $box->next_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Last Update</td>
            <td>{{ $box->status_update_time }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Flapping</td>

            @switch($box->is_flapping)
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
            
            @if ($box->notifications_enabled)
                <td>Yes</td>
            @else
                <td>No</td>
            @endif
            
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Check Type</td>

            @switch($box->check_type)

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