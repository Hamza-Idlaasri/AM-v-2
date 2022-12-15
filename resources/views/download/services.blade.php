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

    <h4 class="text-center">Services History</h4>
    <br>
    <table class="table table-striped table-bordered table-hover text-center">
        <tr class="bg-light text-dark">

            <th>Host</th>
            <th>Service</th>
            <th>Status</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Description</th>
        </tr>

    
    
        @foreach ($services_history as $service_history)

        <tr>
   
            <td>{{$service_history['host_name']}}</td> 

            <td>{{$service_history['service_name']}}</td>
            
            @switch($service_history['state'])

                @case('Ok')
                    <td><span class="badge badge-success">Ok</span></td>
                    @break
                @case('Warning')
                    <td><span class="badge badge-warning">Warning</span></td>
                    @break
                @case('Critical')
                    <td><span class="badge badge-danger">Critical</span></td>
                    @break
                @case('Unknown')
                    <td><span class="badge badge-unknown">Unknown</span></td>
                    @break
                    
            @endswitch
            
            <td>{{$service_history['start_time']}}</td>
            <td>{{$service_history['end_time']}}</td>
            <td class="description">{{$service_history['output']}}</td>
        </tr>
        @endforeach
    </table>
 

