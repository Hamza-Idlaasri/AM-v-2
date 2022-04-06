<div id="equips-notifs">

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <th>Boxes</th>
            <th>Equipements</th>
            <th>State</th>
            <th>Time</th>
            <th>Notification Reason</th>
            <th>Escaleted</th>
            <th>Information</th>
        </thead>

        <tbody>
            @forelse ($equips as $equip)
                <tr>
                    <td>{{ $equip->box_name }}</td>

                    <td>{{ $equip->equip_name }}</td>

                    @switch($equip->state)

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

                    @endswitch

                    <td>{{ $equip->start_time }}</td>
                    
                    @switch($equip->notification_reason)
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

                    @switch($equip->escalated)
                        @case(0)
                            <td>No</td>
                            @break
                        @case(1)
                            <td>Yes</td>
                            @break
                        @default
                            
                    @endswitch

                    <td>{{ $equip->long_output }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No notifications</td>
                </tr>
            @endforelse

        </tbody>

    </table>

</div>