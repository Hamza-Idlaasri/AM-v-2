<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll>

    @include('inc.searchbar',['route' => 'problems.hosts'])

    <div class="float-none mt-4" style="font-size: 90%">

        <table class="table table-bordered text-center table-hover">
            
        <thead class="bg-light text-dark">
            <tr>
                <th>Host</th>
                <th>Adresse IP</th>
                <th>Status</th>
                @if ($site_name == "All")
                <th>Ville</th>
                @endif
                <th>Dernier verification</th>
                <th style="width: 45%">Description</th>
            </tr>
        </thead>
        
        @forelse ($hosts as $host)
        
        <tr>
            {{-- Host Name --}}
            <td>
                <a href="{{ route('mh-details', ['id' => $host->host_object_id]) }}">{{ $host->display_name }}</a>
                
                @if ($host->is_flapping)
                <span class="float-right text-danger" title="This Host is flapping" style="cursor: pointer">
                    <i class="fas fa-retweet"></i>
                </span>
                @endif
                
            </td>
            
            {{-- IP Address --}}
            <td>{{ $host->address }}</td>
            
            {{-- Status --}}
            @switch($host->current_state)
            
                @case(1)
                <td ><span class="badge badge-danger">Down</span></td>
                @break
                
                @case(2)
                <td ><span class="badge badge-unknown">Ureachable</span></td>
                @break
                
                @default
            
            @endswitch
            
            {{-- City --}}
            @if ($site_name == "All")
            <td>{{ $host->site_name }}</td>
            @endif

            {{-- Last Check --}}
            <td>{{ $host->last_check }}</td>

            {{-- Description --}}
            <td class="description">{{ $host->output }}</td>
        </tr>
        
        
        @empty
        
        <tr>
            <td colspan="5">No result found <strong>{{ $search }}</strong></td>
        </tr>
        
        @endforelse
        
        </table>

    </div>

    {{-- Pagination --}}
    {{ $hosts->appends(['search' => $search])->links('vendor.livewire.bootstrap') }}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('problems').style.display = 'block';
    document.getElementById('problems-btn').classList.toggle("active-btn");
    document.getElementById('p-hosts').classList.toggle("active-link");
});

</script>
