<div class="container text-center w-100" style="padding: 20px 0px" wire:poll>
    <h4>{{ $host->display_name }}</h4>
    <h6>{{ $host->address}}</h6>
    <br>
    <table class="table table-bordered table-hover table-details">

        <tr>
            <td class="left-coll font-weight-bolder">Current State</td>
            @switch($host->current_state)
                
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
            <td>{{ $host->output }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Current Attempt</td>
            <td>{{ $host->current_check_attempt }} / {{$host->max_check_attempts}} 

                @if ($host->state_type)
                    ( SOFT state )
                @else
                    ( HARD state )
                @endif
                
            </td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Execution Time</td>
            <td>{{  $host->execution_time }} s</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Check Interval</td>
            <td>{{  $host->check_interval }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Retry Check every</td>
            <td>{{  $host->retry_interval }} sec</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">Max Check Attempts</td>
            <td>{{  $host->max_check_attempts }}</td>
        </tr>
        
        <tr>
            <td class="left-coll font-weight-bolder">On Check</td>

            @if ($host->has_been_checked)
                <td>Yes</td>
            @else
                <td class="text-danger">No</td>
            @endif
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Last Check</td>
            <td>{{ $host->last_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Next Check</td>
            <td>{{ $host->next_check}}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Last Update</td>
            <td>{{ $host->status_update_time }}</td>
        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Flapping</td>

            @switch($host->is_flapping)
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
            
            @if ($host->notifications_enabled)
                <td>Yes</td>
            @else
                <td>No</td>
            @endif

        </tr>

        <tr>
            <td class="left-coll font-weight-bolder">Check Type</td>

            @switch($host->check_type)

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