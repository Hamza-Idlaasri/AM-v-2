@extends('layouts.app')

@section('content')
    
<div class="container w-100 p-4 my-4 d-flex justify-content-around align-items-center" style="height: 60vh">

    <a href="/config/add-host/windows" class="w-25 bg-white text-center  shadow p-4 m-2" style="text-decoration: none;border-radius:12px" id="windows">
        <div>

            <span>    
                <i class="fab fa-windows fa-3x"></i>
            </span>
            
            <br>
            
            <h5>windows</h5>

            <br>

            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Monitoring Windows Vista,7,8,10,Server</h6>
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Install NCPA agent on windows machine</h6>
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Passive Check</h6>
            
        </div>
    </a>

    <a href="/config/add-host/linux" class="w-25 text-center bg-white  shadow p-4 m-2" style="text-decoration: none;border-radius:12px" id="linux">
        <div>
            <span>
                <i class="fab fa-linux fa-3x"></i>
            </span>
            
            <br>
            
            <h5>linux</h5>

            <br>

            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Install NRPE agent on linux machine</h6>
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Passive Check</h6>

        </div>
    </a>


    <a href="/config/add-host/switch" class="w-25 text-center bg-white  shadow p-4 m-2" style="text-decoration: none;border-radius:12px" id="switch">   
        <div>
            <span>
                <i class="far fa-router"></i>
            </span>
            
            <br>
            
            <h5>switch</h5>

            <br>

            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- </h6>

        </div>
    </a>    
        
    <a href="/config/add-host/router" class="w-25 text-center bg-white  shadow p-4 m-2" style="text-decoration: none;border-radius:12px" id="router">
        <div>
            <h5>router</h5>

            <br>

            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Enable SNMP on your Router</h6>

        </div>
    </a>
    
    <a href="/config/add-host/printer" class="w-25 text-center bg-white  shadow p-4 m-2" style="text-decoration: none;border-radius:12px" id="printer">
        <div>
            <span class="bg-white rounde-circle p-2">
                <i class="far fa-print fa-3x"></i>
            </span>
            
            <br>
            
            <h5>printer</h5>

            <br>

            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Monitoring HP printers</h6>

        </div>
    </a>

</div>

@endsection