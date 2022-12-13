<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />

<style>

body{
    font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif
}

@page{
    margin: 10px 50px;
    padding: 0;
}

</style>

    <h4 class="text-center">Hosts History</h4>
    <br>
    <table class="table table-striped table-bordered text-center">
        <tr class="bg-light text-dark text-center">
            <th>Host</th>
            <th>Adresse IP</th>
            <th>Status</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Description</th>
        </tr>
    
        @forelse ($hosts_history as $host_history)
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
                <td colspan="5">No result found {{-- <strong>{{ request()->query('search') }}</strong> --}}</td>
            </tr>

        @endforelse
    </table>
 

