<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll.5000>

    @include('inc.searchbar',['route' => 'problems.boxes'])

    <div class="float-none mt-4" style="font-size: 90%">

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Boxs</th>
                <th>Adresse IP</th>
                <th>Status</th>
                <th>Dernier verification</th>
                <th style="width: 45%">Description</th>
            </tr>
        </thead>

        @forelse ($boxs as $box)

            <tr>
                <td>
                    <a href="{{ route('mb-details', ['id' => $box->host_object_id]) }}">{{ $box->display_name }}</a>
                    
                    @if ($box->is_flapping)
                        <span class="float-right text-danger" title="This box is flapping" style="cursor: pointer">
                            <i class="fas fa-retweet"></i>
                        </span>
                    @endif

                </td>
                
                <td>{{ $box->address }}</td>

                @switch($box->current_state)

                    @case(1)
                        <td><span class="badge badge-danger">Down</span></td>
                    @break

                    @case(2)
                        <td><span class="badge badge-unknown">Ureachable</span></td>
                    @break

                @endswitch

                <td>{{ $box->last_check }}</td>
                <td class="description">{{ $box->output }}</td>
            </tr>


            @empty

                <tr>
                    <td colspan="5">No result found <strong>{{ $search }}</strong></td>
                </tr>

            @endforelse

        </table>
    </div>

    {{-- Pagination --}}
    {{ $boxs->appends(['search' => $search])->links('vendor.livewire.bootstrap') }}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('problems').style.display = 'block';
    document.getElementById('problems-btn').classList.toggle("active-btn");
    document.getElementById('p-boxes').classList.toggle("active-link");
});

</script>