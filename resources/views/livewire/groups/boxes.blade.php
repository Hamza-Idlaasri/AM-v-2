<div class="container p-4 d-flex justify-content-between flex-wrap" wire:poll.5000>
        
    @forelse ($boxgroups as $boxgroup)
        
        <div class="container bg-white rounded shadow my-3" style="width: 45%;height: 10%">
        
            <h4 class="my-4 d-flex justify-content-around align-items-center">
                <a href="{{ route('bg-details', ['id' => $boxgroup->hostgroup_id]) }}" class="text-center" style="width: 85%">{{ $boxgroup->boxgroup_name }}</a>
                <span class="d-flex justify-content-around align-items-center" style="width: 15%">

                    {{-- Edit --}}
                    <a href="{{ route('manageBG', $boxgroup->hostgroup_id) }}" class="text-info hovering" style="text-decoration: none;font-size: 1rem">
                        <i class="fas fa-pen"></i>
                    </a>

                    {{-- Delete --}}
                    <a href="{{ route('deleteBG', $boxgroup->hostgroup_id) }}" class="text-danger hovering" style="text-decoration: none;font-size: 1rem">
                        <i class="fas fa-trash"></i>
                    </a>

                </span>
            </h4>

            <table class="table table-bordered text-center table-hover">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>Box</th>
                        <th>Status</th>
                        <th>Equipements</th>
                    </tr>
                </thead>

                @foreach ($boxgroup->members as $member)
                    <tr>
                        <td>{{ $member->box_name }}</td>
                        
                        @switch($member->box_state)

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
                            <span class="badge badge-success">{{ $member->equips_ok }}</span>
                            <span class="badge badge-warning">{{ $member->equips_warning }}</span>
                            <span class="badge badge-danger">{{ $member->equips_critical }}</span>
                            <span class="badge badge-unknown">{{ $member->equips_unknown }}</span>
                        </td>
                    </tr>

                @endforeach
                
            </table>
        </div>

    @empty
        <p>No result found</p>
    @endforelse

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-bg').classList.toggle("active-link");
});

</script>