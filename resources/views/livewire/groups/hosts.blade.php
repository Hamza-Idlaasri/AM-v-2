<div class="container p-4 d-flex justify-content-between flex-wrap" wire:poll>
    
    <div class="container px-4 py-2 w-100 d-flex justify-content-between align-items-center">
        <a href="/config/add-hostgroup" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>
    </div>

    @forelse ($hostgroups as $hostgroup)
        
        <div class="container bg-white rounded shadow my-3" style="width: 45%;height: 10%">
        
            <h4 class="my-4 d-flex justify-content-around align-items-center">
                <a href="{{ route('hg-details', ['id' => $hostgroup->hostgroup_id]) }}" class="text-center" style="width: 85%">{{ $hostgroup->hostgroup_name }}</a>
                <span class="d-flex justify-content-around align-items-center" style="width: 15%">

                    {{-- Edit --}}
                    <a href="{{ route('manageHG', $hostgroup->hostgroup_id) }}" class="text-info hovering" style="text-decoration: none;font-size: 1rem">
                        <i class="fas fa-pen"></i>
                    </a>

                    {{-- Delete --}}
                    <a href="{{ route('deleteHG', $hostgroup->hostgroup_id) }}" class="text-danger hovering" style="text-decoration: none;font-size: 1rem">
                        <i class="fas fa-trash"></i>
                    </a>

                </span>
            </h4>
            
            <table class="table table-bordered text-center table-hover">

                <thead class="bg-light text-dark">
                    <tr>
                        <th>Host</th>
                        <th>Status</th>
                        <th>Services</th>
                    </tr>
                </thead>

                @foreach ($hostgroup->members as $member)
                    <tr>
                        <td>{{ $member->host_name }}</td>
                        
                        @switch($member->host_state)

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
                        
                        <td>
                            <span class="badge badge-success">{{ $member->services_ok }}</span>
                            <span class="badge badge-warning">{{ $member->services_warning }}</span>
                            <span class="badge badge-danger">{{ $member->services_critical }}</span>
                            <span class="badge badge-unknown">{{ $member->services_unknown }}</span>
                        </td>
                    </tr>

                @endforeach
                
            </table>
        </div>

    @empty
        <p>No hostgroups found</p>
    @endforelse

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-hg').classList.toggle("active-link");
});

</script>