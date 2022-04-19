<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2">    

    {{-- Filter --}}
    <div class="container w-100 p-0 d-flex justify-content-between align-items-center">
        {{-- Download PDF & CSV--}}
        @include('inc.download',['pdf_path' => 'equips.pdf', 'csv_path' => 'equips.csv'])
        {{-- Filter --}}
        @include('inc.filter',['names' => $equips_names,'type' => 'equip'])
    </div>

    {{-- Table --}}
    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Box</th>
                <th>Equipement</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th style="width: 40%">Description</th>
            </tr>
        </thead>
    

        @forelse ($equips_histories as $equip_history)

        <tr>        

            <td>{{$equip_history->box_name}}</td> 

            <td>{{$equip_history->equip_name}}</td>
            
            @switch($equip_history->state)
                
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
            
            <td>{{$equip_history->start_time}}</td>
            <td>{{$equip_history->end_time}}</td>
            <td class="description">{{$equip_history->output}}</td>
        </tr>
 
        @empty

            <tr>
                <td colspan="6">No result found</td>
            </tr>

        @endforelse
        
    </table>

    {{-- {{$equips_histories->appends(['status' => request()->query('status'),'from' => request()->query('from'),'to' => request()->query('to'),'name' => request()->query('name')])->links('vendor.pagination.bootstrap-4')}} --}}

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('historic').style.display = 'block';
    document.getElementById('historic-btn').classList.toggle("active-btn");
    document.getElementById('h-equips').classList.toggle("active-link");
});

</script>