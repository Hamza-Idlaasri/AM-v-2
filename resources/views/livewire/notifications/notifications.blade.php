<div class="container w-100 bg-white rounded shadow my-4 mx-4 px-4 pt-4 pb-2">
    
    <div class="container d-flex w-50 justify-content-around mb-4 mx-auto" id="btn-list">

        <button  id="hosts-btn" class="btn font-weight-bold position-relative btn-primary">Hosts 
            @if ($hosts_not_checked)
                <span class="badge badge-danger notif-popup">{{ $hosts_not_checked }}</span>
            @endif
        </button>

        <button  id="services-btn" class="btn font-weight-bold position-relative ">Services 
            @if ($services_not_checked)
                <span class="badge badge-danger notif-popup">{{ $services_not_checked }}</span>   
            @endif
        </button>

        <button  id="boxes-btn" class="btn font-weight-bold position-relative ">Boxes 
            @if ($boxes_not_checked)
                <span class="badge badge-danger notif-popup">{{ $boxes_not_checked }}</span>
            @endif
        </button>

        <button  id="equips-btn" class="btn font-weight-bold position-relative ">Equipements 
            @if ($equips_not_checked)
                <span class="badge badge-danger notif-popup">{{ $equips_not_checked }}</span>
            @endif
        </button>

    </div>

    {{-- Hosts --}}
    @livewire('notifications.items.hosts')

    {{-- Services --}}
    @livewire('notifications.items.services')

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