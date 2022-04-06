
<div class="" id="sidebar">

    <div class="" id="logo">
        <img src="{{ asset('image/am-logo.png') }}" alt="am-logo">
    </div>

    <div id="menu" class="">

        {{-- Overview --}}
        <div class="item-container">
            <a href="/overview" class="single-item" style="text-decoration: none" id="overview"><i class="fal fa-globe"></i> <span class="item-title">Overview</span></a>
        </div>

        {{-- Monitoring --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="monitoring-btn" class="dd-menu item" @click.prevent="open = true"><i class="far fa-eye"></i> <span class="item-title">Monitoring <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="far fa-angle-down"></i></span></span></button>
            <div id="monitoring" class="sub-menu" x-show="open" x-cloak @click.away="open = false" 
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/monitoring/hosts" class="sub-item" id="m-hosts"><i class="far fa-desktop"></i><span class="item-title"> Hosts</span></a>
                <a href="/monitoring/services" class="sub-item" id="m-services"><i class="far fa-cog"></i><span class="item-title"> Services</span></a>
                <a href="/monitoring/boxes" class="sub-item" id="m-boxes"><i class="far fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/monitoring/equipements" class="sub-item" id="m-equips"><i class="far fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>

        {{-- Problems --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="problems-btn" class="dd-menu item" @click.prevent="open = true"><i class="far fa-exclamation-triangle"></i> <span class="item-title">Problems  <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="far fa-angle-down"></i></span></span></button>
            <div id="problems" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/problems/hosts" class="sub-item" id="p-hosts"><i class="far fa-desktop"></i><span class="item-title"> Hosts</span></a>
                <a href="/problems/services" class="sub-item" id="p-services"><i class="far fa-cog"></i><span class="item-title"> Services</span></a>
                <a href="/problems/boxes" class="sub-item" id="p-boxes"><i class="far fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/problems/equipements" class="sub-item" id="p-equips"><i class="far fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>

        {{-- <div class="item-container" x-data="{ open: false }">
            <button id="groups-btn" class="dd-menu item" @click.prevent="open = true"><i class="far fa-sitemap"></i> <span class="item-title">Groups  <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="far fa-angle-down"></i></span></span></button>
            <div id="groups" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/hostgroups" class="sub-item" id="g-hosts"><i class="far fa-desktop"></i><span class="item-title"> HostGroups</span></a>
                <a href="/servicegroups" class="sub-item" id="g-services"><i class="far fa-cog"></i><span class="item-title"> ServiceGroups</span></a>
                <a href="/boxgroups" class="sub-item" id="g-boxes"><i class="far fa-microchip"></i><span class="item-title">BoxGroups</span></a>
                <a href="/equipgroups" class="sub-item" id="g-equips"><i class="far fa-server"></i><span class="item-title"> EquipGroups</span></a>
            </div>
        </div> --}}

        {{-- Configuration --}}
        @if (auth()->user()->hasRole('agent'))
            
        <div class="item-container" x-data="{ open: false }">
            <button id="config-btn" class="dd-menu item" @click.prevent="open = true"><i class="far fa-tools"></i> <span class="item-title">Configuration <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="far fa-angle-down"></i></span></span></button>
            <div id="config" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/config/hosts" class="sub-item" id="c-hosts"><i class="far fa-desktop"></i><span class="item-title"> Hosts</span></a>
                <a href="/config/services" class="sub-item" id="c-services"><i class="far fa-cog"></i><span class="item-title"> Services</span></a>
                <a href="/config/boxes" class="sub-item" id="c-boxes"><i class="far fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/config/equipements" class="sub-item" id="c-equips"><i class="far fa-server"></i><span class="item-title"> Equipemnts</span></a>
                <a href="/config/hostgroups" class="sub-item" id="c-hg"><i class="far fa-desktop"></i><span class="item-title"> Hostgroups</span></a>
                <a href="/config/servicegroups" class="sub-item" id="c-sg"><i class="far fa-cog"></i><span class="item-title"> Servicegroups</span></a>
                <a href="/config/boxgroups" class="sub-item" id="c-bg"><i class="far fa-microchip"></i><span class="item-title">Boxgroups</span></a>
                <a href="/config/equipgroups" class="sub-item" id="c-eg"><i class="far fa-server"></i><span class="item-title"> Equipgroups</span></a>
                <a href="/config/users" class="sub-item" id="c-users"><i class="fas fa-users"></i><span class="item-title"> Users</span></a>
            </div>
        </div>

        @endif

        {{-- Network-map --}}
        <div class="item-container">
            <a href="/network-map" class="single-item" style="text-decoration: none" id="network-map"><i class="far fa-chart-network"></i> <span class="item-title">Network Map</span></a>
        </div>

        {{-- Statistic --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="statistic-btn" class="dd-menu item" @click.prevent="open = true"><i class="far fa-chart-pie-alt"></i> <span class="item-title">Statistic <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="far fa-angle-down"></i></span></span></button>
            <div id="statistic" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/statistiques/hosts" class="sub-item" id="s-hosts"><i class="far fa-desktop"></i><span class="item-title"> Hosts</span></a>
                <a href="/statistiques/services" class="sub-item" id="s-services"><i class="far fa-cog"></i><span class="item-title"> Services</span></a>
                <a href="/statistiques/boxes" class="sub-item" id="s-boxes"><i class="far fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/statistiques/equipements" class="sub-item" id="s-equips"><i class="far fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>

        {{-- Historic --}}
        <div class="item-container" x-data="{ open: false }">
            <button id="historic-btn" class="dd-menu item" @click.prevent="open = true"><i class="far fa-calendar-alt"></i> <span class="item-title">Historic <span class="angle" :aria-expanded="open ? 'true' : 'false'" :class="{ 'sub-menu-opend': open }"><i class="far fa-angle-down"></i></span></span></button>
            <div id="historic" class="sub-menu" x-show="open" x-cloak @click.away="open = false"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter="ease-out transition-medium"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave="ease-in transition-faster"
                x-transition:leave-end="opacity-0 scale-90">
                <a href="/historiques/hosts" class="sub-item" id="h-hosts"><i class="far fa-desktop"></i><span class="item-title"> Hosts</span></a>
                <a href="/historiques/services" class="sub-item" id="h-services"><i class="far fa-cog"></i><span class="item-title"> Services</span></a>
                <a href="/historiques/boxes" class="sub-item" id="h-boxes"><i class="far fa-microchip"></i><span class="item-title">Boxes</span></a>
                <a href="/historiques/equipements" class="sub-item" id="h-equips"><i class="far fa-server"></i><span class="item-title"> Equipemnts</span></a>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/sidebar.js') }}"></script>
