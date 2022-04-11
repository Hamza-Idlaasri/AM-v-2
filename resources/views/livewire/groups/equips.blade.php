
{{-- <style>
    .setting {
        opacity: .2;
        transition-duration: .3s
    }
    .setting:hover{
        opacity: 1;
    }
    .pop {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
    }
</style> --}}

<div class="container p-4 d-flex justify-content-center flex-wrap" wire:poll.5000>

    <div class="container px-4 py-2 w-100 d-flex justify-content-between align-items-center">
        <a href="/config/add-equipgroup" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>
    </div>

    <?php $i=0?>

    @forelse ($equipgroups as $group)

        <?php $i++ ?>

        <div class="container bg-white rounded shadow" style="width: 45%;height: 10%;">
            
            <h4 class="my-4 d-flex justify-content-around align-items-center">
                <a href="{{ route('eg-details', ['id' => $group->servicegroup_id]) }}" class="text-center" style="width: 85%">{{ $group->equipgroup }}</a>
                <span class="d-flex justify-content-around align-items-center" style="width: 15%">

                    {{-- Edit --}}
                    <a href="{{ route('manageEG', $group->servicegroup_id) }}" class="text-info hovering" style="text-decoration: none;font-size: 1rem">
                        <i class="fas fa-pen"></i>
                    </a>

                    {{-- Delete --}}
                    <a href="{{ route('deleteEG', $group->servicegroup_id) }}" class="text-danger hovering" style="text-decoration: none;font-size: 1rem">
                        <i class="fas fa-trash"></i>
                    </a>

                </span>
            </h4>
                
                {{-- <span class="float-right setting">
                    <a href="{{route('manageEG', $group->servicegroup_id)}}" class="mx-2"><i class="fas fa-pen fa-xs"></i></a>
                    <button title="delete" class="btn mx-2 text-danger" onclick="show({{$i}})" style="outline: none"><i class="fas fa-trash"></i></button>
                </span> --}}

                {{-- Pop-up --}}
                {{-- <div class="popup{{$i}} container p-3 bg-white shadow rounded pop w-50" style="display: none">
                    <h6><b>Are you sure?</b></h6>
                    <p>Do you really you want to delete this Equipgroup <b>"{{$group->servicegroup}}"</b> ?</p>
                    <a href="{{route('deleteEG', $group->servicegroup_id)}}" class="btn btn-danger d-inline">Delete</a>
                    <button type="submit" title="Cancel" class="btn btn-light border border-secondary d-inline" onclick="cancel({{$i}})">Cancel</button>
                </div> --}}
            </h5>
            <table class="table table-bordered text-center">
                <tr class="text-dark bg-light">
                    <th>Box</th>
                    <th>Status</th>
                    <th>Equipment</th>
                </tr>

                @forelse ($members as $member)

                    @if ($member->servicegroup_id == $group->servicegroup_id)
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

                                @switch($member->equip_state)
                                    @case(0)
                                        <span class="badge badge-success">{{ $member->equip_name }}</span>
                                        @break
                                    @case(1)
                                        <span class="badge badge-warning">{{ $member->equip_name }}</span>
                                        @break
                                    @case(2)
                                        <span class="badge badge-danger">{{ $member->equip_name }}</span>
                                        @break
                                    @case(3)
                                        <span class="badge badge-unknown">{{ $member->equip_name }}</span>
                                        @break
                                    @default
                                        
                                @endswitch
            
                            </td>
                        </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="3">No members found</td>
                    </tr>
                @endforelse

                </table>
            </div>
            
    @empty
        <h6>No hostgroups existe</h6>
    @endforelse

</div>
{{-- 
<script>

    show = (i) => {
        document.querySelector('.popup'+i).style.display = 'block';
    }
    
    cancel = (i) => {
        document.querySelector('.popup'+i).style.display = 'none';
    }
    
</script> --}}

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-eg').classList.toggle("active-link");
});

</script>