<nav class="navbar navbar-light pt-3 pb-4 px-0 float-right form-inline w-100">

    @if ($type == 'host' || $type == 'box')
        @if ($from != 'statistic')
            {{-- Status --}}
            <label class="font-weight-bold ml-4 mr-0" for="status">Status :</label>
            <select wire:model="status" name="status" id="status" class="form-control" style="margin-left: -45px">
                <option value="all">All</option>
                <option value="0">Up</option>
                <option value="1">Down</option>
                <option value="2">Unreachable</option>
            </select>
        @endif


        {{-- Names --}}
        @if ($type == 'host')
            {{-- Hosts Names --}}
            <label class="font-weight-bold ml-4 mr-0" for="hosts_names">Host :</label>
            <select wire:model="host_name" name="hosts_names" id="hosts_names" class="form-control" style="width: 20%;margin-left:-45px">
                <option value="">All</option>
                @foreach ($names as $name)
                <option value="{{$name->host_name}}">{{$name->host_name}}</option>
                @endforeach
            </select>
        @endif

        @if ($type == 'box')
            {{-- Boxes Names --}}
            <label class="font-weight-bold ml-4 mr-0" for="boxes_names">Box :</label>
            <select wire:model="box_name" name="boxes_names" id="boxes_names" class="form-control" style="width: 20%;margin-left:-45px">
                <option value="">All</option>
                @foreach ($names as $name)
                <option value="{{$name->box_name}}">{{$name->box_name}}</option>
                @endforeach
            </select>
        @endif

    @endif

    @if ($type == 'service' || $type == 'equip')
        @if ($from != 'statistic')
            {{-- Status --}}
            <label class="font-weight-bold ml-4 mr-0" for="status">Status :</label>
            <select wire:model="status" name="status" id="status" class="form-control" style="margin-left:-30px">
                <option value="all">All</option>
                <option value="0">Ok</option>
                <option value="1">Warning</option>
                <option value="2">Critical</option>
                <option value="3">Unknown</option>
            </select>
        @endif

        {{-- Names --}}
        @if ($type == 'service')
            {{-- Services Names --}}
            <label class="font-weight-bold ml-4 mr-0" for="services_names">Service :</label>
            <select wire:model="service_name" name="services_names" id="services_names" class="form-control" style="width: 20%;margin-left:-45px">
                <option value="">All</option>

                @foreach ($names as $name)
        
                    <optgroup label="{{ $name->host_name }}">
                    
                        @if (sizeof($name->services_names))
                            @for ($i = 0; $i < sizeof($name->services_names); $i++)

                                <option value="{{$name->services_names[$i]}}">{{$name->services_names[$i]}}</option>
                                
                            @endfor
                        @else
                            <option value="" style="font-style: italic; font-size: 12px" disabled><i>Has No Equips</i></option>
                        @endif 
                    
                    </optgroup>
                    
                @endforeach

            </select>
        @endif

        @if ($type == 'equip')
            {{-- Equips Names --}}
            <label class="font-weight-bold ml-2 mr-0" for="equips_names">Equipement :</label>
            <select wire:model="equip_name" name="equips_names" id="equips_names" class="form-control" style="width: 10%;margin-left:-30px">
                <option value="">All</option>

                @foreach ($names as $name)
        
                <optgroup label="{{ $name->box_name }}">
                
                    @if (sizeof($name->equips_names))
                        @for ($i = 0; $i < sizeof($name->equips_names); $i++)

                            <option value="{{$name->equips_names[$i]}}">{{$name->equips_names[$i]}}</option>
                            
                        @endfor
                    @else
                        <option value="" style="font-style: italic; font-size: 12px" disabled><i>Has No Equips</i></option>
                    @endif
                    
                </optgroup>
                    
                @endforeach

            </select>

            {{-- Pin Nbr --}}
            <label class="font-weight-bold ml-2 mr-0" for="pin_nbr">Pin Number :</label>
            <select wire:model="pin_nbr" name="pin_nbr" id="pin_nbr" class="form-control" style="margin-left:-30px">
                <option value="">All</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>
        @endif

    @endif

    {{-- Date From --}}
    <label class="font-weight-bold ml-2 mr-0" for="from">From :</label>
    <input wire:model="date_from" class="form-control" type="date" name="from" min="2020-01-01" max="{{ date('Y-m-d') }}" id="from" value="{{ request('from') }}" style="margin-left:-30px">

    {{-- Date To --}}
    <label class="font-weight-bold ml-2 mr-0" for="to">To :</label>
    <input wire:model="date_to" class="form-control" type="date" name="to" min="2020-01-01" max="{{ date('Y-m-d') }}" id="to" value="{{ request('to') }}" style="margin-left:-30px">

</nav>