<div wire:poll.2500 class="text-center mx-auto position-absolute d-flex flex-column align-items-around" id="popup-notif">

    {{-- Hosts --}}
    @if (session()->has('hosts_notifs'))
    <div class="alert alert-danger shadow notif-container d-flex flex-column align-items-around">
        <button wire:click="hide('hosts','{{$hosts[0]->end_time}}')" type="button" class="btn text-dark" style="position: absolute;top:5px;right:10px;">
            <span aria-hidden="true">&times;</span>
        </button>
        <br>
        @php
            $h = 0;
        @endphp
        @foreach ($hosts as $host)
            <h6>
                <i class="fas fa-exclamation-triangle"></i>

                {{ $host->host_name }} 
                
                host

                @switch($host->state)
                    @case(0)
                        <td><span class="badge badge-success">Up</span></td>
                    @break

                    @case(1)
                        <td><span class="badge badge-danger">Down</span></td>
                    @break

                    @case(2)
                        <td><span class="badge badge-unknown">Ureachable</span></td>
                    @break
                @endswitch
                
                for
                
                @php
                    $start_time = Carbon\Carbon::parse($host->start_time_usec);
                    $end_time = Carbon\Carbon::parse($host->end_time_usec);
                @endphp
                
                @if ($start_time->diff($end_time)->format('%a') != 0)
                    {{$start_time->diff($end_time)->format('%a d, %h h, %i m and %s s')}}
                @else
                    @if ($start_time->diff($end_time)->format('%h') != 0)
                        {{$start_time->diff($end_time)->format('%h h, %i m and %s s')}}
                    @else
                        @if ($start_time->diff($end_time)->format('%i') != 0)
                            {{$start_time->diff($end_time)->format('%i m and %s s')}}
                        @else
                            {{$start_time->diff($end_time)->format('%s s')}}
                        @endif
                    @endif
                @endif
                @php
                    $h++;
                @endphp
            </h6>

            @if ($h < sizeof($hosts))
                <hr>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Services --}}
    @if (session()->has('services_notifs'))
    <div class="alert alert-danger shadow notif-container">
        <button wire:click="hide('services','{{ $services[0]->end_time }}')" type="button" class="btn text-dark" style="position: absolute;top:5px;right:10px;">
            <span aria-hidden="true">&times;</span>
        </button>
        <br>
        @php
            $s = 0;
        @endphp
        @foreach ($services as $service)
        <h6>
            <i class="fas fa-exclamation-triangle"></i>

            {{ $service->service_name }} 
            
            service

            @switch($service->state)
                @case(0)
                    <td><span class="badge badge-success">Up</span></td>
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
            @endswitch
            
            for
            
            @php
                $start_time = Carbon\Carbon::parse($service->start_time_usec);
                $end_time = Carbon\Carbon::parse($service->end_time_usec);
            @endphp
            
            @if ($start_time->diff($end_time)->format('%a') != 0)
                {{$start_time->diff($end_time)->format('%a d, %h h, %i m and %s s')}}
            @else
                @if ($start_time->diff($end_time)->format('%h') != 0)
                    {{$start_time->diff($end_time)->format('%h h, %i m and %s s')}}
                @else
                    @if ($start_time->diff($end_time)->format('%i') != 0)
                        {{$start_time->diff($end_time)->format('%i m and %s s')}}
                    @else
                        {{$start_time->diff($end_time)->format('%s s')}}
                    @endif
                @endif
            @endif
            @php
                $s++;
            @endphp
        </h6>
            @if ($s < sizeof($services))
                <hr>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Boxes --}}
    @if (session()->has('boxes_notifs'))
    <div class="alert alert-danger shadow notif-container">
        <button wire:click="hide('boxes','{{ $boxes[0]->end_time }}')" type="button" class="btn text-dark" style="position: absolute;top:5px;right:10px;">
            <span aria-hidden="true">&times;</span>
        </button>
        <br>
        @php
            $b = 0;
        @endphp
        @foreach ($boxes as $box)
            <h6>
                <i class="fas fa-exclamation-triangle"></i>

                {{ $box->box_name }} 
                
                box

                @switch($box->state)
                    @case(0)
                        <td><span class="badge badge-success">Up</span></td>
                    @break

                    @case(1)
                        <td><span class="badge badge-danger">Down</span></td>
                    @break

                    @case(2)
                        <td><span class="badge badge-unknown">Ureachable</span></td>
                    @break
                @endswitch
                
                for
                
                @php
                    $start_time = Carbon\Carbon::parse($box->start_time_usec);
                    $end_time = Carbon\Carbon::parse($box->end_time_usec);
                @endphp
                
                @if ($start_time->diff($end_time)->format('%a') != 0)
                    {{$start_time->diff($end_time)->format('%a d, %h h, %i m and %s s')}}
                @else
                    @if ($start_time->diff($end_time)->format('%h') != 0)
                        {{$start_time->diff($end_time)->format('%h h, %i m and %s s')}}
                    @else
                        @if ($start_time->diff($end_time)->format('%i') != 0)
                            {{$start_time->diff($end_time)->format('%i m and %s s')}}
                        @else
                            {{$start_time->diff($end_time)->format('%s s')}}
                        @endif
                    @endif
                @endif
                @php
                    $b++;
                @endphp 
            </h6>
            @if ($b < sizeof($boxes))
                <hr>
            @endif
        @endforeach
       
    </div>
    @endif

    {{-- Equips --}}
    @if (session()->has('equips_notifs'))
    <div class="alert alert-danger notif-container">
        <button wire:click="hide('equips','{{ $equips[0]->end_time }}')" type="button" class="btn text-dark" style="position: absolute;top:5px;right:10px;">
            <span aria-hidden="true">&times;</span>
        </button>
        <br>
        @php
            $e = 0;
        @endphp
        @foreach ($equips as $equip)
            <h6>
                <i class="fas fa-exclamation-triangle"></i>

                {{ $equip->equip_name }} 
                
                service

                @switch($equip->state)
                    @case(0)
                        <td><span class="badge badge-success">Up</span></td>
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
                @endswitch
                
                for
                
                @php
                    $start_time = Carbon\Carbon::parse($equip->start_time_usec);
                    $end_time = Carbon\Carbon::parse($equip->end_time_usec);
                @endphp
                
                @if ($start_time->diff($end_time)->format('%a') != 0)
                    {{$start_time->diff($end_time)->format('%a d, %h h, %i m and %s s')}}
                @else
                    @if ($start_time->diff($end_time)->format('%h') != 0)
                        {{$start_time->diff($end_time)->format('%h h, %i m and %s s')}}
                    @else
                        @if ($start_time->diff($end_time)->format('%i') != 0)
                            {{$start_time->diff($end_time)->format('%i m and %s s')}}
                        @else
                            {{$start_time->diff($end_time)->format('%s s')}}
                        @endif
                    @endif
                @endif
                @php
                    $e++;
                @endphp
            </h6>
                @if ($e < sizeof($equips))
                    <hr>
                @endif
        @endforeach
        
    </div>
    @endif
</div>