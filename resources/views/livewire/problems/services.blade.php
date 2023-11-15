<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" wire:poll>

    @include('inc.searchbar',['route' => 'problems.services'])

    <div class="float-none mt-4" style="font-size: 90%">
        
    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Host</th>
                <th>Service</th>
                <th>Status</th>
                @if ($site_name == "All")
                <th>Ville</th>
                @endif
                <th>Dernier verification</th>
                <th style="width: 45%">Description</th>
            </tr>
        </thead>

        <?php $check = 0 ?>
        
        @forelse ($services as $service)        
        
        <tr>

            {{-- Host Name --}}
            @if ($check == 0 || $service->host_object_id != $check)       
                
                    <td>
                        <a href="{{ route('mh-details', ['id' => $service->host_object_id]) }}">{{$service->host_name}}</a>
                    </td> 

                    <?php $check = $service->host_object_id ?>
                
            @else
                <td></td>
            @endif
            
            {{-- Service Name --}}
            <td>
                <a href="{{ route('ms-details', ['id' => $service->service_object_id]) }}">{{$service->service_name}}</a>
                
                @if ($service->is_flapping)
                    <span class="float-right text-danger" title="This Service is flapping" style="cursor: pointer">
                        <i class="fas fa-retweet"></i>
                    </span>
                @endif
            </td>
            
            {{-- Status --}}
            @switch($service->current_state)
                
                @case(1)
                    <td><span class="badge badge-warning">Warning</span></td>
                    @break
                @case(2)
                    <td><span class="badge badge-danger">Critical</span></td>
                    @break
                @case(3)
                    <td><span class="badge badge-unknown">Ureachable</span></td>
                    @break
                    
            @endswitch
            
            {{-- City --}}
            @if ($site_name == "All")
                <td>{{ $service->site_name }}</td>
            @endif
            
            {{-- Last Check --}}
            <td>{{$service->last_check}}</td>

            {{-- Description --}}
            <td class="description">{{$service->output}}</td>
        </tr>
            

        @empty

            <tr>
                <td colspan="5">No result found <strong>{{ $search }}</strong></td>
            </tr>

        @endforelse

        
    </table>
</div>
    
    {{-- Pagination --}}
    {{$services->appends(['search' => $search])->links('vendor.livewire.bootstrap')}}
    
</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('problems').style.display = 'block';
    document.getElementById('problems-btn').classList.toggle("active-btn");
    document.getElementById('p-services').classList.toggle("active-link");
});

</script>

