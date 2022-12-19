<nav class="navbar navbar-light pt-3 pb-4 px-0 float-right form-inline" style="width: 90%">

    @if ($type == 'host' || $type == 'box')
        
        {{-- Status --}}
        <label class="font-weight-bold ml-4 mr-2" for="status">Status :</label>
        <select wire:model="status" name="status" id="status" class="form-control">
            <option value="all">All</option>
            <option value="up">Up</option>
            <option value="down">Down</option>
            <option value="unreachable">Unreachable</option>
        </select>

        {{-- Names --}}
        @if ($type == 'host')
            {{-- Hosts Names --}}
            <label class="font-weight-bold ml-4 mr-2" for="hosts_names">Host :</label>
            <select wire:model="host_name" name="hosts_names" id="hosts_names" class="form-control" style="width: 20%">
                <option value="">All</option>
                @foreach ($names as $name)
                <option value="{{$name->host_name}}">{{$name->host_name}}</option>
                @endforeach
            </select>
        @endif

        @if ($type == 'box')
            {{-- Boxes Names --}}
            <label class="font-weight-bold ml-4 mr-2" for="boxes_names">Box :</label>
            <select wire:model="box_name" name="boxes_names" id="boxes_names" class="form-control" style="width: 20%">
                <option value="">All</option>
                @foreach ($names as $name)
                <option value="{{$name->box_name}}">{{$name->box_name}}</option>
                @endforeach
            </select>
        @endif

    @endif

    @if ($type == 'service' || $type == 'equip')
        
        {{-- Status --}}
        <label class="font-weight-bold ml-4 mr-2" for="status">Status :</label>
        <select wire:model="status" name="status" id="status" class="form-control">
            <option value="all">All</option>
            <option value="ok">Ok</option>
            <option value="warning">Warning</option>
            <option value="critical">Critical</option>
            <option value="unknown">Unknown</option>
        </select>

        {{-- Names --}}
        @if ($type == 'service')
            {{-- Services Names --}}
            <label class="font-weight-bold ml-4 mr-2" for="services_names">Service :</label>
            <select wire:model="service_name" name="services_names" id="services_names" class="form-control" style="width: 20%">
                <option value="">All</option>

                @foreach ($names as $name)
        
                    <optgroup label="{{ $name->host_name }}">
                    
                        @for ($i = 0; $i < sizeof($name->services); $i++)

                            <option value="{{$name->services[$i]}}">{{$name->services[$i]}}</option>  
                            
                        @endfor
                    
                    </optgroup>
                    
                @endforeach

            </select>
        @endif

        @if ($type == 'equip')
            {{-- Equips Names --}}
            <label class="font-weight-bold ml-4 mr-2" for="equips_names">Equipement :</label>
            <select wire:model="equip_name" name="equips_names" id="equips_names" class="form-control" style="width: 20%">
                <option value="">All</option>

                @foreach ($names as $name)
        
                <optgroup label="{{ $name->box_name }}">
                
                    @for ($i = 0; $i < sizeof($name->equips); $i++)

                        <option value="{{$name->equips[$i]}}">{{$name->equips[$i]}}</option>  
                        
                    @endfor
                
                </optgroup>
                    
                @endforeach

            </select>
        @endif

    @endif

    {{-- Date From --}}
    <label class="font-weight-bold ml-4 mr-2" for="from">From :</label>
    <input wire:model="date_from" class="form-control" type="date" name="from" min="2020-01-01" max="{{ date('Y-m-d') }}" id="from" value="{{ request('from') }}">

    {{-- Date To --}}
    <label class="font-weight-bold ml-4 mr-2" for="to">To :</label>
    <input wire:model="date_to" class="form-control" type="date" name="to" min="2020-01-01" max="{{ date('Y-m-d') }}" id="to" value="{{ request('to') }}">

</nav>