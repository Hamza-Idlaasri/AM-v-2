<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll>

    @include('inc.searchbar',['route' => 'problems.equips'])

    <div class="float-none mt-4" style="font-size: 90%">

        <table class="table table-bordered text-center table-hover">

            <thead class="bg-light text-dark">
                <tr>
                    <th>Equipement</th>
                    <th>Pins</th>
                    <th>Status</th>
                    <th>Salle</th>
                    @if ($site_name == "All")
                    <th>Site</th>
                    @endif
                    <th>Dernier verification</th>
                    <th>Input Nbr</th>
                    <th style="width: 30%">Description</th>
                </tr>
            </thead>
    
            @forelse ($equips_problems as $equip)
                @if(sizeof($equip->pins) > 0)
                <tr>
                {{-- Equipement Name --}}
                <td>{{$equip->equip_name}}</td>
                
                @forelse ($equip->pins as $pin)
                        @if (array_search($pin,$equip->pins) > 0)
                            <td></td>
                        @endif
                        {{-- Pin Name --}}
                        <td>{{$pin->pin_name}}
                            @if ($pin->is_flapping)
                                <span class="float-right text-danger" title="This equip is flapping" style="cursor: pointer">
                                    <i class="fas fa-retweet"></i>
                                </span>
                            @endif
                        </td>
    
                        {{-- Status --}}
                        @switch($pin->current_state)
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
                        
                        {{-- Hall Nake --}}
                        <td>{{$pin->hall_name}}</td>
    
                        {{-- Site Name --}}
                        @if ($site_name == "All")
                        <td>{{$pin->site_name}}</td>
                        @endif
                        
                        {{-- Dernier verification --}}
                        <td>{{$pin->last_check}}</td>
    
                        {{-- Input Nr --}}
                        <td>{{$pin->check_command}}</td>
    
                        {{-- Description --}}
                        <td class="description">{{$msg[$pin->current_state]}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No result found <strong>{{ $search }}</strong></td>
                    </tr>
                @endforelse
                @endif
            @empty
                <tr>
                    <td colspan="7">No result found <strong>{{ $search }}</strong></td>
                </tr>
            @endforelse
    
        </table>

</div>

    {{-- Pagination --}}
    {{$equips_problems->appends(['search' => $search])->links('vendor.livewire.bootstrap')}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('problems').style.display = 'block';
    document.getElementById('problems-btn').classList.toggle("active-btn");
    document.getElementById('p-equips').classList.toggle("active-link");
});

</script>