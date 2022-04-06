<style>

    label:hover{
        font-weight: bolder;
        cursor: pointer;
    }
    
    #cancel{
        widows: 5px;
        height: 5px;
        padding:0 5px;
        cursor: pointer;
        color: red
    }
    
    #cancel:hover{
        font-weight: bolder;
    }
    
</style>
    
<div class="container back">

    <form action="{{ route('create-service') }}" method="get">

    <div class="card container mt-3 mx-2 bg-white p-0 rounded">

        <div class="card-header">
            <h5 class="font-weight-bolder">Choose Service : <span class="text-danger font-weight-bolder">*</span></h5>
        </div>
        
        <div class="card-text text-center text-muted mt-3">
            <h6>choose Service depend on type of Host</h6>
            @error('service')
                <p class="alert alert-danger w-25 m-auto text-center">
                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <div class="card-body d-flex justify-content-around">

            {{-- Windows --}}
            <div class="card shadow-sm mx-1" style="width: 200px">
                <div class="card-header bg-primary text-white text-center">Windows</div>
                <div class="card-body">
                    
                    @for ($i = 0; $i < sizeof($windows); $i++)
                        <input type="radio" name="service" value="{{ $windows[$i] }}" id="{{ $windows[$i] }}" onclick="removeAll()"> <label for="{{ $windows[$i] }}">{{ $windows[$i] }}</label>
                        <br>
                    @endfor
                    
                </div>
            </div>

            {{-- Linux --}}
            <div class="card shadow-sm mx-1" style="width: 200px">
                <div class="card-header bg-primary text-white text-center">Linux</div>
                <div class="card-body">

                    <input type="radio" name="service" value="PING(linux)" id="PING(linux)" onclick="removeAll()"> <label for="PING(linux)">PING</label>
                    <br>
                    @for ($i = 0; $i < sizeof($linux); $i++)
                        <input type="radio" name="service" value="{{ $linux[$i] }}" id="{{ $linux[$i] }}" onclick="removeAll()"> <label for="{{ $linux[$i] }}">{{ $linux[$i] }}</label>
                        <br>
                    @endfor
                </div>
            </div>

            {{-- Router --}}
            <div class="card shadow-sm mx-1" style="width: 200px">
                <div class="card-header bg-primary text-white text-center">Router</div>
                <div class="card-body">

                    <input type="radio" name="service" value="PING(router)" id="PING(router)" onclick="removeAll()"> <label for="PING(router)">PING</label>
                    <br>
                    <input type="radio" name="service" value="Port n Link Status(router)" id="Port_n_link_status(router)" onclick="showPortNbrandCS()"> <label class="portNumber" for="Port_n_link_status(router)">Port n Link Status</label>
                    <br>
                    <input type="radio" name="service" value="Uptime(router)" id="Uptime(router)" onclick="showCS()"> <label for="Uptime(router)">Uptime</label>
                    <br>
                    
                </div>
            </div>

            {{-- Switch --}}
            <div class="card shadow-sm mx-1" style="width: 200px">
                <div class="card-header bg-primary text-white text-center">Switch</div>
                <div class="card-body">
                    <input type="radio" name="service" value="PING(switch)" id="PING(switch)" onclick="removeAll()"> <label for="PING(switch)">PING</label>
                    <br>
                    <input type="radio" name="service" value="Port n Link Status(switch)" id="Port_n_link_status(switch)" onclick="showPortNbrandCS()"> <label class="portNumber" for="Port_n_link_status(switch)">Port n Link Status</label>
                    <br>
                    <input type="radio" name="service" value="Uptime(switch)" id="Uptime(switch)" onclick="showCS()"> <label for="Uptime(switch)">Uptime</label>
                    <br>
                    <input type="radio" name="service" value="Port n Bandwidth Usage" id="Port_n_Bandwidth_Usage" onclick="showPortNbr()"> <label class="portNumber" for="Port_n_Bandwidth_Usage">Port n Bandwidth Usage</label>
                    
                </div>
            </div>

            {{-- Printer --}}
            <div class="card shadow-sm mx-1" style="width: 200px">
                <div class="card-header bg-primary text-white text-center">Printer</div>
                <div class="card-body">
                    <input type="radio" name="service" value="PING(printer)" id="PING(printer)" onclick="removeAll()"> <label for="PING(printer)">PING</label>
                    <br>
                    <input type="radio" name="service" value="Printer Status" id="Printer_Status" onclick="showCS()"> <label for="Printer_Status">Printer Status</label>
                    <br>
                </div>
            </div>
            
        </div>

        <div class="m-4" id="cs" style="display: none">
            <h5 class="font-weight-bolder">Community String : <span class="text-danger">*</span></h5>
            <input type="text" name="community" class="form-control w-25 @error('community') border-danger @enderror" id="community" value="public" pattern="[a-zA-Z0-9]{2,100}" title="name of community string must between 2 and 100 charaters">
            @error('community')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="m-4" id="portNbrInput" style="display: none">
            <h5 class="font-weight-bolder">Choose port number : <span class="text-danger">*</span></h5>
            <input  type="number" min="1" max="50" name="portNbr" class="form-control w-25 @error('portNbr') border-danger @enderror" id="PortNbr" value="1">
            @error('portNbr')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        
    </div>

    

    <div class="card container p-0 mt-3 mx-2 bg-white rounded">
        <div class="card-header">
            <h5 class="font-weight-bolder">For the Host : <span class="text-danger font-weight-bolder">*</span></h5>
        </div>
        
        <div class="card-body">
            <div class="p-2" style="max-height: 200px; overflow: auto">
                @error('host')
                    <p class="alert alert-danger w-25 m-auto text-center">
                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                    </p>
                @enderror
                
                @foreach ($hosts as $host)
                    <input type="radio" name="host" value="{{ $host->host_name }}" id="{{ $host->host_name }}"> <label for="{{ $host->host_name }}">{{ $host->host_name }}</label>
                    <br>
                @endforeach

            </div> 
        </div>
        
    </div>    

    <button type="submit" class="btn btn-primary m-2" id="addService">Add</button>


    </form>

</div>

<script>

    portNbrInput = document.getElementById('portNbrInput');
    cs = document.getElementById('cs');

    function showPortNbr() {
        portNbrInput.style.display = 'block';
        cs.style.display = 'none';
    }
    
    function showPortNbrandCS() {
        portNbrInput.style.display = 'block';
        cs.style.display = 'block';
    }

    function showCS() {
        cs.style.display = 'block';
        portNbrInput.style.display = 'none';
    }

    function removeAll() {
        cs.style.display = 'none';
        portNbrInput.style.display = 'none';
    }
</script>