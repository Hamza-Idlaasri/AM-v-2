<div id="hosts-notifs">

    <table class="table table-bordered text-center table-hover">
        <thead class="bg-light text-dark">
            <th>Hosts</th>
            <th>State</th>
            <th>Time</th>
            <th>Notification Reason</th>
            <th>Escaleted</th>
            <th>Information</th>
        </thead>

        <tbody>

            @forelse ($hosts as $host)
                <tr>
                    <td>{{ $host->host_name }}</td>

                    @switch($host->state)

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

                    <td>{{ $host->start_time }}</td>
                    
                    @switch($host->notification_reason)
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

                    @switch($host->escalated)
                        @case(0)
                            <td>No</td>
                            @break
                        @case(1)
                            <td>Yes</td>
                            @break
                        @default
                            
                    @endswitch

                    <td>{{ $host->long_output }}</td>

                </tr>
            @empty
                <tr>
                    <td colspan="6">No notifications</td>
                </tr>
            @endforelse
        </tbody>

    </table>

</div>
