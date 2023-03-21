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
        @include('inc.download',['pdf_path' => 'equips.pdf', 'csv_path' => 'equips.csv'])
        {{-- Filter --}}
        @include('inc.filter',['names' => $equips_names,'type' => 'equip', 'from' => 'historic'])
    </div>
    
    {{-- Table --}}
    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Site</th>
                @if ($site_name == "All")
                <th>Ville</th>
                @endif
                <th>Equipement</th>
                <th>Pin</th>
                <th>Status</th>
                <th>State Time</th>
                {{-- <th>Durée</th> --}}
                {{-- <th>End Time</th> --}}
                <th style="width: 30%">Description</th>
            </tr>
        </thead>
    

        @forelse ($equips_histories as $equip_history)
            
        <tr>        

            <td>{{$equip_history->box_name}}</td> 

            @if ($site_name == "All")
                <td>{{$equip_history->site_name}}</td>
            @endif

            <td>{{$equip_history->equip_name}}</td>

            <td>{{substr($equip_history->input_nbr,9,-2);}}</td>
            
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
                    <td><span class="badge badge-unknown">Unknown</span></td>
                    @break
                @default
                    
            @endswitch
            
            {{-- State Time --}}
            <td>{{$equip_history->state_time}}</td>
            {{-- <td>{{$equip_history->end_time}}</td> --}}

            {{-- Duration --}}
            {{-- <td>{{formatSeconds($equip_history->state_time_usec)}}</td> --}}

            {{-- Description --}}
            @if ($equip_history->state == 0)
                <td class="description">fonction normalement</td>
            @else
                <td class="description">{{$equip_history->pin_name}}</td>
            @endif
        </tr>
 
        @empty

            <tr>
                <td colspan="6">No result found</td>
            </tr>

        @endforelse
        
    </table>

    {{-- {{$equips_histories->appends(['status' => request()->query('status'),'from' => request()->query('from'),'to' => request()->query('to'),'name' => request()->query('name')])->links('vendor.pagination.bootstrap-4')}} --}}
    {{-- Pagination --}}
    {{$equips_histories->links('vendor.livewire.bootstrap')}}
</div>

@php
    function formatSeconds($seconds) {

        $weeks = floor($seconds / 604800);
        $seconds -= $weeks * 604800;
        $days = floor($seconds / 86400);
        $seconds -= $days * 86400;
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $output = '';
        if ($weeks > 0) {
            $output .= $weeks . ' w, ';
        }
        if ($days > 0) {
            $output .= $days . ' d, ';
        }
        if ($hours > 0) {
            $output .= $hours . ' h, ';
        }
        if ($minutes > 0) {
            $output .= $minutes . ' m, ';
        }
        $output .= $seconds . ' s';
        return $output;
    }

@endphp

<script>

window.addEventListener('load', function() {
    document.getElementById('historic').style.display = 'block';
    document.getElementById('historic-btn').classList.toggle("active-btn");
    document.getElementById('h-equips').classList.toggle("active-link");
});

</script>