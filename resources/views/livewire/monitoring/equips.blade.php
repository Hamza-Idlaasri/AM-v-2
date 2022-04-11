<div class="container bg-white shadow rounded w-100 my-4 px-4 py-2" wire:poll.5000>

    @include('inc.searchbar',['route' => 'monitoring.equips'])

    <div class="float-none mt-4" style="font-size: 90%">

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Boxes</th>
                <th>Equips</th>
                <th>Status</th>
                <th>Dernier verification</th>
                <th style="width: 45%">Description</th>
            </tr>
        </thead>

        <?php $check = 0 ?>
        
        @forelse ($equips as $equip)        
        
        <tr>

            @if ($check == 0 || $equip->host_object_id != $check)       
                
                    <td>
                        <a href="{{ route('mb-details', ['id' => $equip->host_object_id]) }}">{{$equip->box_name}}</a>
                    </td> 

                    <?php $check = $equip->host_object_id ?>
                
            @else
                <td></td>
            @endif
            

            <td>
                <a href="{{ route('me-details', ['id' => $equip->service_object_id]) }}">{{$equip->equip_name}}</a>
                
                @if ($equip->is_flapping)
                    <span class="float-right text-danger" title="This equip is flapping" style="cursor: pointer">
                        <i class="fas fa-retweet"></i>
                    </span>
                @endif
            </td>
            
            @switch($equip->current_state)
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
                @default
                    
            @endswitch
            
            <td>{{$equip->last_check}}</td>
            <td class="description">{{$equip->output}}</td>
        </tr>
            

        @empty

            <tr>
                <td colspan="5">No result found <strong>{{ $search }}</strong></td>
            </tr>

        @endforelse

        
    </table>

</div>

    {{$equips->appends(['serach' => $search])->links('vendor.livewire.bootstrap')}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('monitoring').style.display = 'block';
    document.getElementById('monitoring-btn').classList.toggle("active-btn");
    document.getElementById('m-equips').classList.toggle("active-link");
});

</script>
