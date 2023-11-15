<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll.5000>

    <div class="container p-0 w-100 d-flex justify-content-between align-items-center">
        
        <a href="/config/add-equip" class="btn btn-success"><i class="fas fa-plus"></i> Add New</a>

        @include('inc.searchbar',['route' => 'config-equips'])

    </div>

    <br>

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Equipement</th>
                <th>Pins Used</th>
                <th>Box</th>
                @if ($site_name == "All")
                <th>Ville</th>
                @endif
                <th>Edit</th>
            </tr>
        </thead>

        @forelse ($all_equips as $equip)

            <tr>

                {{-- Equip Name --}}
                <td>
                    {{-- <a href="{{ route('me-details', ['id' => $equip->id]) }}">{{ $equip->equip_name }}</a> --}}
                    {{ $equip->equip_name }}
                </td>

                {{-- Pins Nbr--}}
                <td>
                    @forelse ($equip->details as $detail)
                        <span class="badge badge-light shadow-sm mt-1"><span class="badge" style="background: rgb(209, 235, 209)">{{$detail->input_nbr}}</span> - {{$detail->pin_name}}</span>
                        <br>
                    @empty
                        <p class="text-muted"><i>No pin used</i></p>
                    @endforelse
                </td>

                {{-- Box Name --}}
                <td>
                    {{-- <a href="{{ route('mb-details', ['id' => $equip->host_object_id]) }}">{{ $equip->box_name }}</a> --}}
                    {{$equip->box_name}}
                </td>

                {{-- Ville --}}
                @if ($site_name == "All")
                    <td>
                        {{$equip->site_name}}
                    </td>
                @endif

                {{-- Edit --}}
                <td>
                    <span class="w-100 d-flex justify-content-around align-items-center">

                        {{-- Edit Equip --}}
                        <a href="{{ route('edit-equip', ['id' => $equip->id]) }}" class="btn text-info" style="border: 0"><i class="fas fa-pen"></i></a>
                        
                        {{-- Delete Equip --}}
                        <a href="{{ route('delete-equip', ['id' => $equip->id]) }}" class="btn text-danger">
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

</div>

<script>

    window.addEventListener('load', function() {
        document.getElementById('config').style.display = 'block';
        document.getElementById('config-btn').classList.toggle("active-btn");
        document.getElementById('c-equips').classList.toggle("active-link");
    });
        
</script>