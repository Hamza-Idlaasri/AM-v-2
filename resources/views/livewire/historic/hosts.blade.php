<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2">
    
    @php
        $query = http_build_query(array('data' => $hosts_histories));

        if (empty($query)) {
            $query = 'null';
        }
    @endphp

    {{-- Filter --}}
    <div class="container w-100 p-0 d-flex justify-content-between align-items-center">
        {{-- Download PDF & CSV --}}
        @include('inc.download',['pdf_path' => 'hosts.pdf', 'csv_path' => 'hosts.csv', 'data' => $query])
        {{-- Filter --}}
        @include('inc.filter',['names' => $hosts_names,'type' => 'host'])
    </div>

    {{-- Table --}}
    <table class="table table-bordered text-center table-hover">
    
        <thead class="bg-light text-dark">
            <tr>
                <th>Host</th>
                <th>Adresse IP</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th style="width: 40%">Description</th>
            </tr>
        </thead>
    
        @forelse ($hosts_histories as $host_history)
            <tr>
                <td>{{$host_history->display_name}}</td>
                <td>{{$host_history->address}}</td>
                
                @switch($host_history->state)
                
                    @case('Up')
                        <td><span class="badge badge-success">Up</span></td>
                        @break

                    @case('Down')
                        <td><span class="badge badge-danger">Down</span></td>
                        @break
                        
                    @case('Unreachable')
                        <td><span class="badge badge-unknown">Unreachable</span></td>
                        @break
                    
                    @default
                        
                @endswitch
                
                <td>{{$host_history->start_time}}</td>
                <td>{{$host_history->end_time}}</td>
                <td class="description">{{$host_history->output}}</td>
            </tr>

        @empty

            <tr>
                <td colspan="6">No result found {{-- <strong>{{ request()->query('search') }}</strong> --}}</td>
            </tr>

        @endforelse
    </table>

    {{-- {{$hosts_histories->appends(['status' => request()->query('status'),'from' => request()->query('from'),'to' => request()->query('to'),'name' => request()->query('name')])->links('vendor.pagination.bootstrap-4')}} --}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('historic').style.display = 'block';
    document.getElementById('historic-btn').classList.toggle("active-btn");
    document.getElementById('h-hosts').classList.toggle("active-link");
});

</script>