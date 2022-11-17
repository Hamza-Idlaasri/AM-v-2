<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll.5000>

    <div class="container p-0 w-100 d-flex justify-content-between align-items-center">
        
        <a href="/config/select-box" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>

        @include('inc.searchbar',['route' => 'config-equips'])

    </div>

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Box</th>
                <th>Equipement</th>
                <th>Input Nbr</th>
                <th>Check Interval</th>
                <th>Retry Interval</th>
                <th>Max Check Attempts</th>
                <th>Check</th>
                <th>Notif</th>
                <th>Edit</th>
            </tr>
        </thead>

        <?php $i=0?>

        @forelse ($equips as $equip)

            <?php $i++ ?>

            <tr>
            
                <td>
                    <a href="{{ route('mb-details', ['id' => $equip->host_object_id]) }}">{{ $equip->box_name }}</a>
                </td>

                <td>
                    <a href="{{ route('me-details', ['id' => $equip->service_object_id]) }}">{{ $equip->equip_name }}</a>
                </td>

                <td>{{ $equip->check_command }}</td>

                <td>{{ $equip->normal_check_interval }}</td>
                
                <td>{{ $equip->retry_check_interval }}s</td>
                
                <td>{{ $equip->max_check_attempts }}</td>

                @if ($equip->has_been_checked)
                    <td>Yes</td>
                @else
                    <td class="text-danger">No</td>
                @endif
                
                @if ($equip->notifications_enabled)
                    <td>Yes</td>
                @else
                    <td class="text-danger">No</td>
                @endif

                <td>
                    <span class="w-100 d-flex justify-content-around align-items-center">

                        {{-- Edit Equip --}}
                        <a href="{{ route('edit-equip', ['id' => $equip->service_id]) }}" class="btn text-info" style="border: 0"><i class="fas fa-pen"></i></a>
                        
                        {{-- Delete Equip --}}
                        <a href="{{ route('delete-equip', ['id' => $equip->service_object_id]) }}" class="btn text-danger">
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

    {{-- {{$equips->appends(['search' => request()->query('search')])->links('vendor.pagination.bootstrap-4')}} --}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-equips').classList.toggle("active-link");
});
    
</script>
