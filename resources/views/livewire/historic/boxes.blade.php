<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2">

    <div class="container w-100 p-0 d-flex justify-content-between align-items-center">
        {{-- Download PDF & CSV--}}
        @include('inc.download',['pdf_path' => 'boxes.pdf', 'csv_path' => 'boxes.csv'])
        {{-- Filter --}}
        @include('inc.filter',['names' => $boxes_names,'type' => 'box'])
    </div>

    {{-- Table --}}
    <table class="table table-bordered text-center table-hover">
    
        <thead class="bg-light text-dark">
            <tr>
                <th>Boxes</th>
                <th>Adresse IP</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th style="width: 40%">Description</th>
            </tr>
        </thead>
    
        @forelse ($boxes_histories as $box_history)
            <tr>
                <td>{{$box_history->display_name}}</td>
                <td>{{$box_history->address}}</td>
                
                @switch($box_history->state)
                
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
                
                <td>{{$box_history->start_time}}</td>
                <td>{{$box_history->end_time}}</td>
                <td class="description">{{$box_history->output}}</td>
            </tr>

        @empty

            <tr>
                <td colspan="6">No result found {{-- <strong>{{ request()->query('search') }}</strong> --}}</td>
            </tr>

        @endforelse
    </table>

    {{-- {{$boxes_histories->appends(['status' => request()->query('status'),'from' => request()->query('from'),'to' => request()->query('to'),'name' => request()->query('name')])->links('vendor.pagination.bootstrap-4')}} --}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('historic').style.display = 'block';
    document.getElementById('historic-btn').classList.toggle("active-btn");
    document.getElementById('h-boxes').classList.toggle("active-link");
});

</script>