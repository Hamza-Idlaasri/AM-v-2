<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll.5000>

    <div class="container p-0 w-100 d-flex justify-content-between align-items-center">
        
        <a href="/config/select-box" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>

        @include('inc.searchbar',['route' => 'config-pins'])

    </div>

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Pin Description</th>
                <th>Equipment</th>
                <th>Box</th>
                @if ($site_name == 'All')
                <th>City</th>
                @endif
                <th>Input Nbr</th>
                <th>Check Interval</th>
                {{-- <th>Retry Interval</th> --}}
                <th>Max Check Attempts</th>
                <th>Check</th>
                <th>Notif</th>
                <th>Edit</th>
            </tr>
        </thead>

        @forelse ($pins as $pin)

            <tr>
            
                {{-- Pin Name --}}
                <td>
                    <a href="{{ route('me-details', ['id' => $pin->service_object_id]) }}">{{ $pin->pin_name }}</a>
                </td>

                {{-- Equips Name --}}
                <td>
                    {{ $pin->equip_name }}
                </td>

                {{-- Box Name --}}
                <td>
                    {{-- <a href="{{ route('mb-details', ['id' => $pin->host_object_id]) }}">{{ $pin->box_name }}</a> --}}
                    {{ $pin->box_name }}
                </td>

                {{-- Site Name --}}
                @if ($site_name == 'All')
                <td>
                    {{ $pin->site_name }}
                </td>
                @endif

                {{-- Check Time --}}
                <td>{{ $pin->check_command }}</td>

                {{-- Check interval --}}
                <td>{{ $pin->normal_check_interval }}s</td>
                
                {{-- <td>{{ $pin->retry_check_interval }}s</td> --}}
                
                <td>{{ $pin->max_check_attempts }}</td>

                @if ($pin->has_been_checked)
                    <td>Yes</td>
                @else
                    <td class="text-danger">No</td>
                @endif
                
                @if ($pin->notifications_enabled)
                    <td>Yes</td>
                @else
                    <td class="text-danger">No</td>
                @endif

                <td>
                    <span class="w-100 d-flex justify-content-around align-items-center">

                        {{-- Edit Equip --}}
                        <a href="{{ route('edit-pin', ['id' => $pin->service_id]) }}" class="btn text-info" style="border: 0"><i class="fas fa-pen"></i></a>
                        
                        {{-- Delete Equip --}}
                        <a href="{{ route('delete-pin', ['id' => $pin->service_object_id]) }}" class="btn text-danger">
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

    {{-- {{$pins->appends(['search' => request()->query('search')])->links('vendor.pagination.bootstrap-4')}} --}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-pins').classList.toggle("active-link");
});
    
</script>
