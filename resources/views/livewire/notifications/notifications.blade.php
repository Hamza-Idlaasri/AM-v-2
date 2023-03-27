<div class="container w-100 bg-white rounded shadow my-4 mx-4 px-4 pt-4 pb-2">
    
    <div class="container d-flex w-50 justify-content-around mb-4 mx-auto" id="btn-list">

        {{-- @if (auth()->user()->hasRole('super_admin')) --}}
            
        {{-- Hosts --}}
        <button  id="hosts-btn" class="btn font-weight-bold position-relative btn-primary">Hosts 
            @if ($hosts_not_checked)
                <span class="bg-danger notif-popup" style="width:8px;height:8px;top:5px;right:-2px"></span>
            @endif
        </button>

        {{-- Services --}}
        <button  id="services-btn" class="btn font-weight-bold position-relative ">Services 
            @if ($services_not_checked)
                <span class="bg-danger notif-popup" style="width:8px;height:8px;top:5px;right:-2px"></span>   
            @endif
        </button>

        {{-- @endif --}}

        {{-- Boxes --}}
        <button  id="boxes-btn" class="btn font-weight-bold position-relative ">Boxes 
            @if ($boxes_not_checked)
                <span class="bg-danger notif-popup" style="width:8px;height:8px;top:5px;right:-2px"></span>
            @endif
        </button>

        {{-- Equips --}}
        <button  id="equips-btn" class="btn font-weight-bold position-relative ">Equipements 
            @if ($equips_not_checked)
                <span class="bg-danger notif-popup" style="width:8px;height:8px;top:5px;right:-2px"></span>
            @endif
        </button>

    </div>

    {{-- @if (auth()->user()->hasRole('super_admin')) --}}
        
    {{-- Hosts --}}
    @livewire('notifications.items.hosts')
    
    {{-- Services --}}
    @livewire('notifications.items.services')
    
    {{-- @endif --}}

    {{-- Boxes --}}
    @livewire('notifications.items.boxes')

    {{-- Equips --}}
    @livewire('notifications.items.equips')

</div>


<script>

document.getElementById('icon-bell').classList.remove('far');
document.getElementById('icon-bell').classList.add('fas');
document.getElementById('icon-bell').classList.add('text-primary');

</script>