<div class="container bg-white shadow rounded my-4 px-4 py-2" style="width: 95%" wire:poll.5000>
    
    <div class="container p-0 w-100 d-flex justify-content-between align-items-center">
        
        <a href="/config/add-host" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>

        @include('inc.searchbar',['route' => 'config-hosts'])

    </div>

    <table class="table table-bordered text-center table-hover">
        <thead class="bg-light text-dark">
            <tr>
                <th>Hosts</th>
                <th>Address IP</th>
                <th>Description</th>
                <th>Retry Interval</th>
                <th>Max Check Attempts</th>
                <th>Check</th>
                <th>Notif</th>
                <th style="width: 10%">Edit</th>
            </tr>
        </thead>
        
        <tbody>
            @forelse ($hosts as $host)
                <tr>
                    <td>
                        <a href="{{ route('mh-details', ['id' => $host->host_object_id]) }}">{{ $host->display_name }}</a>
                    </td>

                    <td>{{ $host->address }}</td>
                    
                    <td>{{ $host->output }}</td>
                    
                    <td>{{ $host->retry_interval }}s</td>
                    
                    <td>{{ $host->max_check_attempts }}</td>
                    
                    @if ($host->has_been_checked)
                        <td>Yes</td>
                    @else
                        <td class="text-danger">No</td>
                    @endif

                    @if ($host->notifications_enabled)
                        <td>Yes</td>
                    @else
                        <td>No</td>
                    @endif

                    <td>
                        <span class="w-100 d-flex justify-content-around align-items-center">

                            {{-- Edit Host --}}
                            <a href="{{ route('edit-host', ['id' => $host->host_id]) }}" class="btn text-info" style="border: 0"><i class="fas fa-pen"></i></a>
                            
                            {{-- Delete Host --}}
                            <a href="{{ route('delete-host', ['id' => $host->host_object_id]) }}" class="btn text-danger">
                                <i class="far fa-trash-alt"></i>
                            </a>

                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No result found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<div class="text-center mx-auto position-absolute" id="flash-message" style="width:30%;bottom: 5px;left:50%;transform: translate(-50%, -10%);">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-hosts').classList.toggle("active-link");
});
    
</script>