<div class="container p-4 bg-white shadow rounded w-100 my-4 px-4 py-2" wire:poll>

    <table class="table table-bordered text-center table-hover">
        <thead class="bg-light text-dark">
            <tr>
                <th>Box</th>
                <th>Equipements</th>
                <th>Status</th>
                <th>Dernier verification</th>
                <th>Description</th>
            </tr>
        </thead>

            <h4 class="text-center">Equipgroup : <b>{{ $equipgroup[0]->alias }}</b></h4>

            <br>

            @forelse ($members as $member)
                <tr>
                           
                    <td>{{$member->box_name}}</td> 

                    <td>{{ $member->equip_name }}</td>

                    @switch($member->equip_state)
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

                    <td>{{ $member->last_check }}</td>
                    <td class="description">{{ $member->output }}</td>

                </tr>

            @empty
                <tr>
                    <td colspan="5">No results found</td>
                </tr>
            @endforelse
        
    </table>
</div>