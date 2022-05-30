@extends('layouts.app')

@section('content')
    
<div class="container w-50 p-4 my-4 d-flex align-items-center" style="height: 60vh">

    {{-- BF-1010 --}}
    <a href="/config/add-box-type-bf-1010" class="bg-white text-center shadow p-4 m-2" style="width: 45%;text-decoration: none;border-radius:12px" id="bf-1010">
        <div>

            {{-- Icon --}}
            <span>    
                <i class="fa-solid fa-microchip fa-3x"></i>
            </span>
            
            <br><br>

            {{-- Title --}}
            <h5>BF-1010</h5>

            <br>

            {{-- Description --}}
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- 10 Inputs</h6>
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- 10 Outputs</h6>
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Activation of Inputs by <b>0V</b></h6>
            
        </div>
    </a>

    {{-- BF-2300 --}}
    <a href="/config/add-box-type-bf-2300" class="bg-white text-center shadow p-4 m-2" style="width: 45%;text-decoration: none;border-radius:12px" id="bf-1010">
        <div>

            {{-- Icon --}}
            <span>    
                <i class="fa-solid fa-microchip fa-3x"></i>
            </span>
            
            <br><br>
            
            {{-- Title --}}
            <h5>BF-2300</h5>

            <br>

            {{-- Description --}}
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- 12 Inputs</h6>
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- 6 Outputs</h6>
            <h6 class="text-left" style="color: rgba(128, 128, 128, 0.7)">- Activation of Inputs by <b>5V</b></h6>
            
        </div>
    </a>

</div>

@endsection