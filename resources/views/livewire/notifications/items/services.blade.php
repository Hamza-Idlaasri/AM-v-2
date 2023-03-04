<div id="services-notifs">

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <th>Hosts</th>
            <th>Service</th>
            <th>State</th>
            <th>Time</th>
            <th>Notification Reason</th>
            <th>Escaleted</th>
            <th>Information</th>
        </thead>

        <tbody>
            @forelse ($services as $service)
                <tr>
                    <td>{{ $service->host_name }}</td>

                    <td>{{ $service->service_name }}</td>

                    @switch($service->state)

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

                    <td>{{ $service->start_time }}</td>
                    
                    @switch($service->notification_reason)
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

                    @switch($service->escalated)
                        @case(0)
                            <td>No</td>
                            @break
                        @case(1)
                            <td>Yes</td>
                            @break
                        @default
                            
                    @endswitch

                    <td>{{ $service->long_output }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No notifications</td>
                </tr>
            @endforelse

        </tbody>

    </table>

</div>

<script>

document.getElementById('services-btn').onclick = () => {
    document.getElementById('services-btn').classList.remove("btn-light");
    document.getElementById('services-btn').classList.add("btn-primary");

    document.getElementById('boxes-btn').classList.remove("btn-primary");
    document.getElementById('hosts-btn').classList.remove("btn-primary");
    document.getElementById('equips-btn').classList.remove("btn-primary");

    document.getElementById('services-notifs').style.display = 'block';
    document.getElementById('hosts-notifs').style.display = 'none';
    document.getElementById('boxes-notifs').style.display = 'none';
    document.getElementById('equips-notifs').style.display = 'none';
}

</script>