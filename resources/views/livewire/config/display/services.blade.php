<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll.5000>

    <div class="container p-0 w-100 d-flex justify-content-between align-items-center">
        
        <a href="/config/add-service" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>

        @include('inc.searchbar',['route' => 'config-services'])

    </div>

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Host</th>
                <th>Service</th>
                <th>Description</th>
                <th>Check Interval</th>
                <th>Retry Interval</th>
                <th>Max Check Attempts</th>
                <th>Check</th>
                <th>Notif</th>
                <th>Edit</th>
            </tr>
        </thead>

        <?php $i=0?>

        @forelse ($services as $service)

            <?php $i++ ?>

            <tr>
            
                <td>
                    <a href="{{ route('mh-details', ['id' => $service->host_object_id]) }}">{{ $service->host_name }}</a>
                </td>

                <td>
                    <a href="{{ route('ms-details', ['id' => $service->service_object_id]) }}">{{ $service->service_name }}</a>
                </td>

                <td>{{ $service->output }}</td>

                <td>{{ $service->normal_check_interval }}</td>
                
                <td>{{ $service->retry_check_interval }}s</td>
                
                <td>{{ $service->max_check_attempts }}</td>

                @if ($service->has_been_checked)
                    <td>Yes</td>
                @else
                    <td class="text-danger">No</td>
                @endif
                
                @if ($service->notifications_enabled)
                    <td>Yes</td>
                @else
                    <td class="text-danger">No</td>
                @endif

                <td>
                    <span class="w-100 d-flex justify-content-around align-items-center">

                        {{-- Edit Service --}}
                        <a href="{{ route('edit-service', ['id' => $service->service_id]) }}" class="btn text-info" style="border: 0; @if($service->host_object_id == 1) pointer-events: none; opacity:0.5 @endif"><i class="fas fa-pen"></i></a>
                        
                        {{-- Delete Service --}}
                        <a href="{{ route('delete-service', ['id' => $service->service_object_id]) }}" class="btn text-danger" style="@if($service->host_object_id == 1) pointer-events: none; opacity:0.5 @endif">
                            <i class="far fa-trash-alt"></i>
                        </a>

                    </span>
                </td>
            
            </tr>

        @empty
            <tr>
                <td colspan="10">No result found <strong>{{ request()->query('search') }}</strong></td>
            </tr>
        @endforelse
        
    </table>

    {{-- {{$services->appends(['search' => request()->query('search')])->links('vendor.pagination.bootstrap-4')}} --}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-services').classList.toggle("active-link");
});
    
</script>
