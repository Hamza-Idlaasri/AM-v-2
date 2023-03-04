
<div class="" id="sidebar">

    <div class="" id="logo">
        <img src="{{ asset('image/net-logo.png') }}" alt="nm-logo">
    </div>

    {{-- Site Name --}}
    <div style="width: 100%;margin-top: -20px;font-size: 14px;font-style: italic;color: black" class="text-center mx-auto mb-3 font-weight-bold">{{$current_site_name}}</div>

    <div id="menu" class="">

        {{-- Overview --}}
        <div class="item-container">
            <a href="/overview" class="single-item" style="text-decoration: none" id="overview"><i class="fa-solid fa-globe"></i> <span class="item-title">Overview</span></a>
        </div>

        {{-- Monitoring --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="monitoring-btn" class="dd-menu item" @click.prevent="open = true"><i class="fa-solid fa-eye"></i> <span class="item-title">Monitoring <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="fa-solid fa-angle-down"></i></span></span></button>
            <div id="monitoring" class="sub-menu" x-show="open" x-cloak @click.away="open = false" 
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                @if (auth()->user()->hasRole('super_admin'))
                <a href="/monitoring/hosts" class="sub-item" id="m-hosts"><i class="fa-solid fa-display"></i><span class="item-title"> Hosts</span></a>
                <a href="/monitoring/services" class="sub-item" id="m-services"><i class="fa-solid fa-gear"></i><span class="item-title"> Services</span></a>
                @endif
                <a href="/monitoring/boxes" class="sub-item" id="m-boxes"><i class="fa-solid fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/monitoring/equipements" class="sub-item" id="m-equips"><i class="fa-solid fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>

        {{-- Problems --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="problems-btn" class="dd-menu item" @click.prevent="open = true"><i class="fa-solid fa-triangle-exclamation"></i> <span class="item-title">Problems  <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="fa-solid fa-angle-down"></i></span></span></button>
            <div id="problems" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                @if (auth()->user()->hasRole('super_admin'))
                <a href="/problems/hosts" class="sub-item" id="p-hosts"><i class="fa-solid fa-display"></i><span class="item-title"> Hosts</span></a>
                <a href="/problems/services" class="sub-item" id="p-services"><i class="fa-solid fa-gear"></i><span class="item-title"> Services</span></a>
                @endif
                <a href="/problems/boxes" class="sub-item" id="p-boxes"><i class="fa-solid fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/problems/equipements" class="sub-item" id="p-equips"><i class="fa-solid fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>

        {{-- <div class="item-container" x-data="{ open: false }">
            <button id="groups-btn" class="dd-menu item" @click.prevent="open = true"><i class="far fa-sitemap"></i> <span class="item-title">Groups  <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="fa-solid fa-angle-down"></i></span></span></button>
            <div id="groups" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/hostgroups" class="sub-item" id="g-hosts"><i class="fa-solid fa-display"></i><span class="item-title"> HostGroups</span></a>
                <a href="/servicegroups" class="sub-item" id="g-services"><i class="fa-solid fa-gear"></i><span class="item-title"> ServiceGroups</span></a>
                <a href="/boxgroups" class="sub-item" id="g-boxes"><i class="fa-solid fa-microchip"></i><span class="item-title">BoxGroups</span></a>
                <a href="/equipgroups" class="sub-item" id="g-equips"><i class="fa-solid fa-server"></i><span class="item-title"> EquipGroups</span></a>
            </div>
        </div> --}}

        {{-- Configuration --}}
        @if (auth()->user()->hasRole('super_admin'))
            
        <div class="item-container" x-data="{ open: false }">
            <button id="config-btn" class="dd-menu item" @click.prevent="open = true"><i class="fa-solid fa-screwdriver-wrench"></i> <span class="item-title">Configuration <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="fa-solid fa-angle-down"></i></span></span></button>
            <div id="config" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/config/hosts" class="sub-item" id="c-hosts"><i class="fa-solid fa-display"></i><span class="item-title"> Hosts</span></a>
                <a href="/config/services" class="sub-item" id="c-services"><i class="fa-solid fa-gear"></i><span class="item-title"> Services</span></a>
                <a href="/config/boxes" class="sub-item" id="c-boxes"><i class="fa-solid fa-microchip"></i><span class="item-title"> Boxes</span></a>
                <a href="/config/equipements" class="sub-item" id="c-equips"><i class="fa-solid fa-server"></i><span class="item-title"> Equipemnts</span></a>
                <a href="/config/hostgroups" class="sub-item" id="c-hg"><i class="fa-solid fa-network-wired"></i><span class="item-title"> Hostgroups</span></a>
                <a href="/config/servicegroups" class="sub-item" id="c-sg"><i class="fa-solid fa-gears"></i><span class="item-title"> Servicegroups</span></a>
                <a href="/config/boxgroups" class="sub-item" id="c-bg"><i class="fa-solid fa-microchip"></i><span class="item-title">Boxgroups</span></a>
                <a href="/config/equipgroups" class="sub-item" id="c-eg"><i class="fa-solid fa-server"></i><span class="item-title"> Equipgroups</span></a>
                <a href="/config/users" class="sub-item" id="c-users"><i class="fa-solid fa-users"></i><span class="item-title"> Users</span></a>
            </div>
        </div>

        @endif

        {{-- Network-map --}}
        <div class="item-container">
            <a href="/network-map" class="single-item" style="text-decoration: none" id="network-map"><i class="fa-solid fa-diagram-project"></i> <span class="item-title">Network Map</span></a>
        </div>

        {{-- Statistic --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="statistic-btn" class="dd-menu item" @click.prevent="open = true"><i class="fa-solid fa-chart-column"></i> <span class="item-title">Statistic <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="fa-solid fa-angle-down"></i></span></span></button>
            <div id="statistic" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                @if (auth()->user()->hasRole('super_admin'))
                <a href="/statistiques/hosts" class="sub-item" id="s-hosts"><i class="fa-solid fa-display"></i><span class="item-title"> Hosts</span></a>
                <a href="/statistiques/services" class="sub-item" id="s-services"><i class="fa-solid fa-gear"></i><span class="item-title"> Services</span></a>
                @endif
                <a href="/statistiques/boxes" class="sub-item" id="s-boxes"><i class="fa-solid fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/statistiques/equipements" class="sub-item" id="s-equips"><i class="fa-solid fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>

        {{-- Historic --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="historic-btn" class="dd-menu item" @click.prevent="open = true"><i class="fa-solid fa-calendar-days"></i> <span class="item-title">Historical Data <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="fa-solid fa-angle-down"></i></span></span></button>
            <div id="historic" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                @if (auth()->user()->hasRole('super_admin'))
                <a href="/historiques/hosts" class="sub-item" id="h-hosts"><i class="fa-solid fa-display"></i><span class="item-title"> Hosts</span></a>
                <a href="/historiques/services" class="sub-item" id="h-services"><i class="fa-solid fa-gear"></i><span class="item-title"> Services</span></a>
                @endif
                <a href="/historiques/boxes" class="sub-item" id="h-boxes"><i class="fa-solid fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/historiques/equipements" class="sub-item" id="h-equips"><i class="fa-solid fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>

        @if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
            
            {{-- Sites --}}
            <div class="item-container" x-data="{ open: false }">
                <button id="sites-btn" class="dd-menu item" @click.prevent="open = true"><i class="fa-solid fa-map-location-dot"></i> <span class="item-title">Sites <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="fa-solid fa-angle-down"></i></span></span></button>
                <div id="sites" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter="ease-out transition-medium"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave="ease-in transition-faster"
                    x-transition:leave-end="opacity-0 scale-90">

                    <a href="/sites" class="sub-item" style="cursor: pointer"><i class="fa-solid fa-map"></i> <span class="item-title" style="font-size:12px;">Global Overview</span></a> 

                    @foreach ($sites as $site)
                        <a wire:click="changeSite({{$site->id}})" class="sub-item" style="cursor: pointer"><i class="fa-solid fa-location-dot"></i> <span class="item-title"> {{$site->site_name}}</span></a> 
                    @endforeach

                </div>
            </div>

        @endif

    </div>

</div>

<script src="{{ asset('js/sidebar.js') }}"></script>
