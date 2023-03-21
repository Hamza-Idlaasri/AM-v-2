<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2">    

    @php
        $query = json_encode($download);

        if (empty($query)) {
            $query = 'null';
        }
    @endphp

    {{-- Filter --}}
    <div class="container w-100 p-0 d-flex justify-content-between align-items-center">
        {{-- Download PDF & CSV --}}
        @include('inc.download',['pdf_path' => 'services.pdf', 'csv_path' => 'services.csv'])
        {{-- Filter --}}
        @include('inc.filter',['names' => $services_names, 'type' => 'service', 'from' => 'historic'])
    </div>

    {{-- Table --}}
    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Host</th>
                <th>Service</th>
                <th>Status</th>
                <th>State Time</th>
                {{-- <th>End Time</th> --}}
                <th style="width: 40%">Description</th>
            </tr>
        </thead>

        @forelse ($services_histories as $service_history)

        <tr>        

            <td>{{$service_history->host_name}}</td> 

            <td>{{$service_history->service_name}}</td>
            
            @switch($service_history->state)
                
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
                    <td><span class="badge badge-unknown">Unknown</span></td>
                    @break
                @default
                    
            @endswitch
            
            <td>{{$service_history->state_time}}</td>
            {{-- <td>{{$service_history->end_time}}</td> --}}
            <td class="description">{{$service_history->output}}</td>
        </tr>
 
        @empty

            <tr>
                <td colspan="6">No result found</td>
            </tr>

        @endforelse
        
    </table>

    {{-- {{$services_histories->appends(['status' => request()->query('status'),'from' => request()->query('from'),'to' => request()->query('to'),'name' => request()->query('name')])->links('vendor.pagination.bootstrap-4')}} --}}
    {{$services_histories->links('vendor.livewire.bootstrap')}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('historic').style.display = 'block';
    document.getElementById('historic-btn').classList.toggle("active-btn");
    document.getElementById('h-services').classList.toggle("active-link");
});

</script>