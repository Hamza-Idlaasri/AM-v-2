<div class="container w-100 bg-white rounded shadow my-4 px-4 pt-4 pb-2">
    
    <div class="container d-flex w-50 justify-content-around mb-4 mx-auto" id="btn-list">

        <button wire:click="checkNotif('hosts')" class="btn btn-primary font-weight-bold position-relative" id="hosts-btn">Hosts 
            @if ($total_hosts)
                <span class="badge badge-danger notif-popup">{{ $total_hosts }}</span>
            @endif
        </button>

        <button wire:click="checkNotif('services')" class="btn btn-light font-weight-bold position-relative" id="services-btn">Services 
            @if ($total_services)
                <span class="badge badge-danger notif-popup">{{ $total_services }}</span>   
            @endif
        </button>

        <button wire:click="checkNotif('boxes')" class="btn btn-light font-weight-bold position-relative" id="boxes-btn">Boxes 
            @if ($total_boxes)
                <span class="badge badge-danger notif-popup">{{ $total_boxes }}</span>
            @endif
        </button>

        <button wire:click="checkNotif('equips')" class="btn btn-light font-weight-bold position-relative" id="equips-btn">Equipements 
            @if ($total_equips)
                <span class="badge badge-danger notif-popup">{{ $total_equips }}</span>
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

<script src="{{ asset('js/notif.js') }}"></script>