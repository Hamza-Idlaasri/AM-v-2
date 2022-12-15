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

    <h4 class="text-center">Boxes History</h4>
    <br>
    <table class="table table-striped table-bordered text-center">
        <tr class="bg-light text-dark text-center">
            <th>Box</th>
            <th>Adresse IP</th>
            <th>Status</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Description</th>
        </tr>
    
        @forelse ($boxes_history as $box_history)
            <tr>
                <td>{{$box_history['display_name']}}</td>
                <td>{{$box_history['address']}}</td>
                
                @switch($box_history['state'])
                
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
                
                <td>{{$box_history['start_time']}}</td>
                <td>{{$box_history['end_time']}}</td>
                <td class="description">{{$box_history['output']}}</td>
            </tr>

        @empty

            <tr>
                <td colspan="5">No result found {{-- <strong>{{ request()->query('search') }}</strong> --}}</td>
            </tr>

        @endforelse
    </table>
 

