<div class="d-flex justify-content-between align-items-center p-2 bg-white shadow-sm" wire:poll.5000>

    {{-- Summary --}}
    <div class="d-flex justify-content-around align-items-center" style="width: 65%;margin-left: 10%;">
        {{-- Hosts --}}
        <div class="bg-white d-flex rounded align-items-center p-2" id="summary_hosts">
            <span class="m-1 badge"><i class="far fa-desktop"></i></span>
            <span class="badge m-1 font-weight-bold" sstyle="cursor: default" title="total des hosts : {{ $total_hosts }}">
                @if ($total_hosts >= 1000)
                    {{floor($total_hosts/1000).'k'}}
                @else
                    {{ $total_hosts }}
                @endif
            </span>
            <span class="badge badge-success m-1" style="cursor: default" title="{{ $hosts_up }} {{ Str::plural('host',$hosts_up)}} Up">
                @if ($hosts_up >= 1000)
                    {{floor($hosts_up/1000).'k'}}
                @else
                    {{ $hosts_up }}                
                @endif
            </span>
            <span class="badge badge-danger m-1" style="cursor: default" title="{{ $hosts_down }} {{ Str::plural('host',$hosts_down)}} Down">
                @if ($hosts_down >= 1000)
                    {{floor($hosts_down/1000).'k'}}
                @else
                    {{ $hosts_down }}
                @endif    
            </span>
            <span class="badge badge-unknown m-1" style="cursor: default" title="{{ $hosts_unreachable }} {{ Str::plural('host',$hosts_unreachable)}} Unreachable">
                @if ($hosts_unreachable >= 1000)
                    {{floor($hosts_unreachable/1000).'k'}}
                @else
                    {{ $hosts_unreachable }}
                @endif
            </span>

        </div>

        <span style="font-size: 26px;opacity: .25;" class="text-secondary">|</span>

        {{-- Services --}}
        <div class="bg-white d-flex rounded align-items-center p-2" id="summary_services">
            <span class="m-1 badge"><i class="far fa-cog"></i></span>
            <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des services : {{ $total_services }}">
                @if ($total_services >= 1000)
                    {{floor($total_services/1000).'k'}}
                @else
                    {{ $total_services }}
                @endif
            </span>
            <span class="badge badge-success m-1" style="cursor: default" title="{{ $services_ok }} {{ Str::plural('service',$services_ok)}} ok">
                @if ($services_ok >= 1000)
                    {{floor($services_ok/1000).'k'}}
                @else
                    {{ $services_ok }}
                @endif
            </span>
            <span class="badge badge-warning m-1" style="cursor: default" title="{{ $services_warning }} {{ Str::plural('service',$services_warning)}} warning">
                @if ($services_warning >= 1000)
                    {{floor($services_warning/1000).'k'}}
                @else
                    {{ $services_warning }}
                @endif
            </span>
            <span class="badge badge-danger m-1" style="cursor: default" title="{{ $services_critical }} {{ Str::plural('service',$services_critical)}} critical">
                @if ($services_critical >= 1000)
                    {{floor($services_critical/1000).'k'}}
                @else
                    {{ $services_critical }}
                @endif
            </span>
            <span class="badge badge-unknown m-1" style="cursor: default" title="{{ $services_unknown }} {{ Str::plural('service',$services_unknown)}} unknown">
                @if ($services_unknown >= 1000)
                    {{floor($services_unknown/1000).'k'}}
                @else
                    {{ $services_unknown }}
                @endif
            </span>
        </div>
        <span style="font-size: 26px;opacity: .25;" class="text-secondary">|</span>
        {{-- Boxes --}}
        <div class="bg-white d-flex rounded align-items-center p-2" id="summary_boxs">
            <span class="m-1 badge"><i class="far fa-microchip"></i></span>
            <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des boxes : {{ $total_boxes }}">
                @if ($total_boxes >= 1000)
                    {{floor($total_boxes/1000).'k'}}
                @else
                    {{ $total_boxes }}
                @endif
            </span>
            <span class="badge badge-success m-1" style="cursor: default" title="{{ $boxes_up }} {{ Str::plural('box',$boxes_up)}} Up">
                @if ($boxes_up >= 1000)
                    {{floor($boxes_up/1000).'k'}}
                @else
                    {{ $boxes_up }}
                @endif
            </span>
            <span class="badge badge-danger m-1" style="cursor: default" title="{{ $boxes_down }} {{ Str::plural('box',$boxes_down)}} Down">
                @if ($boxes_down >= 1000)
                    {{floor($boxes_down/1000).'k'}}
                @else
                    {{ $boxes_down }}
                @endif
            </span>
            <span class="badge badge-unknown m-1" style="cursor: default" title="{{ $boxes_unreachable }} {{ Str::plural('box',$boxes_unreachable)}} Unreachable">
                @if ($boxes_unreachable >= 1000)
                    {{floor($boxes_unreachable/1000).'k'}}
                @else
                    {{ $boxes_unreachable }}
                @endif
            </span>
        </div>
        <span style="font-size: 26px;opacity: .25;" class="text-secondary">|</span>
        {{-- Equips --}}
        <div class="bg-white d-flex rounded align-items-center p-2" id="summary_equips">
            <span class="m-1 badge"><i class="far fa-server"></i></span>
            <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des equipements : {{ $total_equips }}">
                @if ($total_equips >= 1000)
                    {{floor($total_equips/1000).'k'}}
                @else
                    {{ $total_equips }}
                @endif
            </span>
            <span class="badge badge-success m-1" style="cursor: default" title="{{ $equips_ok }} {{ Str::plural('equipement',$equips_ok)}} ok">
                @if ($equips_ok >= 1000)
                    {{floor($equips_ok/1000).'k'}}
                @else
                    {{ $equips_ok }}
                @endif
            </span>
            <span class="badge badge-warning m-1" style="cursor: default" title="{{ $equips_warning }} {{ Str::plural('equipement',$equips_warning)}} ok">
                @if ($equips_warning >= 1000)
                    {{floor($equips_warning/1000).'k'}}
                @else
                    {{ $equips_warning }}
                @endif
            </span>
            <span class="badge badge-danger m-1" style="cursor: default" title="{{ $equips_critical }} {{ Str::plural('equipement',$equips_critical)}} ok">
                @if ($equips_critical >= 1000)
                    {{floor($equips_critical/1000).'k'}}
                @else
                    {{ $equips_critical }}
                @endif
            </span>
            <span class="badge badge-unknown m-1" style="cursor: default" title="{{ $equips_unknown }} {{ Str::plural('equipement',$equips_unknown)}} ok">
                @if ($equips_unknown >= 1000)
                    {{floor($equips_unknown/1000).'k'}}
                @else
                    {{ $equips_unknown }}
                @endif
            </span>
        </div>
    </div>

    {{-- User login--}}
    <div class="d-flex align-items-center mx-3">
        <span class="mx-4 text-secondary position-relative" style="font-size: 16px">
            <a href="{{ route('notifications') }}" class="text-dark">
                <i class="far fa-bell" id="icon-bell"></i>
                @if ($total_notifs > 0)
                    <span id="notif-sign"></span>
                @endif
            </a>
        </span>
        <span class="mx-2">{{ auth()->user()->name }}</span>

        <div x-data="{ open: false }" style="position: relative;z-index: 1">
            <button @click.prevent="open = true" class="btn btn-light rounded-circle text-secondary" id="user-items">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</button>
            <div class="sub-menu bg-white shadow-lg p-1 rounded" x-show="open" x-cloak @click.away="open = false" style="position: absolute;left: -80px;top:50px">
                <a href="{{ route('profile') }}" class="sub-item" style="margin:5px;width:100px"><i class="fas fa-user"></i><span class="item-title"> Profile</span></a>
                <a href="{{ route('edit-user-info') }}" class="sub-item" style="margin:5px;width:100px"><i class="far fa-cog"></i><span class="item-title"> Setting</span></a>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="sub-item" style="margin:5px;width:100px;"><i class="fas fa-sign-out fa-flip-horizontal"></i><span class="item-title"> Logout</span></button>
                </form>
            </div>
        </div>
    </div>
</div>