<div id="boxes-notifs">

    <table class="table table-bordered text-center table-hover">
        <thead class="bg-light text-dark">
            <th>Boxes</th>
            <th>State</th>
            <th>Time</th>
            <th>Notification Reason</th>
            <th>Escaleted</th>
            <th>Information</th>
        </thead>

        <tbody>

            @forelse ($boxes as $box)
                <tr>
                    <td>{{ $box->box_name }}</td>

                    @switch($box->state)

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

                    <td>{{ $box->start_time }}</td>
                    
                    @switch($box->notification_reason)
                        @case(0)
                            <td>Normal notification</td>
                            @break
                        @case(1)
                            <td>Problem acknowledgement</td>
                            @break
                        @case(2)
                            <td>Flapping started</td>
                            @break
                        @case(3)
                            <td>Flapping stopped</td>
                            @break
                        @case(4)
                            <td>Flapping was disabled</td>
                            @break
                        @case(5)
                            <td>Downtime started</td>
                            @break
                        @case(6)
                            <td>Downtime ended</td>
                            @break
                        @case(7)
                            <td>Downtime was cancelled</td>
                            @break
                                
                    @endswitch

                    @switch($box->escalated)
                        @case(0)
                            <td>No</td>
                            @break
                        @case(1)
                            <td>Yes</td>
                            @break
                        @default
                            
                    @endswitch

                    <td>{{ $box->long_output }}</td>

                </tr>
            @empty
                <tr>
                    <td colspan="6">No notifications</td>
                </tr>
            @endforelse
        </tbody>

    </table>

</div>
