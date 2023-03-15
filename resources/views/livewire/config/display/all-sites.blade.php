<div class="container bg-white shadow rounded w-100 my-4 mx-4 px-4 py-2" id="back">

    <table class="table table-bordered text-center table-hover">

        <thead class="bg-light text-dark">
            <tr>
                <th>Site Name</th>
                <th>Elements</th>
                <th style="width:15%">Config</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($sites as $site)
                <tr>
                    {{-- Site Name --}}
                    <td>{{ $site->site_name }}</td>

                    {{-- Elelemnts --}}
                    <td>
                        {{-- Hosts --}}
                        <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des hosts : {{$site->hosts}}">{{$site->hosts}}</span>
                        <span class="m-1 badge" title="Hosts"><i class="fa-solid fa-display fa-lg"></i></span>

                        <span class="text-secondary mx-4">|</span>

                        {{-- Services --}}
                        <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des Services : {{$site->services}}">{{$site->services}}</span>
                        <span class="m-1 badge" title="Services"><i class="fa-solid fa-gear fa-lg"></i></span>

                        <span class="text-secondary mx-4">|</span>

                        {{-- Boxes --}}
                        <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des Boxes : {{$site->boxes}}">{{$site->boxes}}</span>
                        <span class="m-1 badge" title="Boxes"><i class="fa-solid fa-microchip fa-lg"></i></span>

                        <span class="text-secondary mx-4">|</span>

                        {{-- Equips --}}
                        <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des Equipements : {{$site->equips}}">{{$site->equips}}</span>
                        <span class="m-1 badge" title="Equipements"><i class="fa-solid fa-server fa-lg"></i></span>

                        <span class="text-secondary mx-4">|</span>

                        {{-- Pins --}}
                        <span class="badge m-1 font-weight-bold" style="cursor: default" title="total des Pins : {{$site->pins}}">{{$site->pins}}</span>
                        <span class="m-1 badge" title="Pins"><i class="fa-solid fa-server fa-lg"></i></span>

                    </td>

                    {{-- Config --}}
                    <td>
                        <span class="w-100 d-flex justify-content-around align-items-center">

                            {{-- Delete Popup --}}
                            <div id="delete-popup-{{$site->id}}" style="position: absolute;top:50%;left:55%;transform:translate(-50%,-55%);display:none" class="w-25 bg-light rounded shadow p-4">
                                <h6 class="font-weight-bold">Be Aware if you remove "{{$site->site_name}}" site all its Hosts, Services, Boxes, Equips and Users will be removed too!</h6>
                                <br>
                                <div class="px-4 d-flex justify-content-around align-items-center">
                                    <button class="btn btn-outline-secondary" onclick="cancel('delete-popup-{{$site->id}}')">Cancel</button>
                                    <a href="{{ route('delete-site', ['id' => $site->id]) }}" class="btn btn-danger">Delete</a>
                                </div>
                            </div>

                            {{-- Edit Popup --}}
                            <div id="edit-popup-{{$site->id}}" style="position: absolute;top:50%;left:55%;transform:translate(-50%,-55%);display:none" class="w-25 bg-light rounded shadow p-4">
                                <label id="{{$site->id}}" class="font-weight-bold">Edit the name of "{{$site->site_name}}" site :</label>
                                <input type="text" name="site_name" id="{{$site->id}}" value="{{$site->site_name}}" class="form-control">
                                <br>

                                <div class="px-4 d-flex justify-content-around align-items-center">
                                    <button class="btn btn-outline-secondary" onclick="cancel('edit-popup-{{$site->id}}')">Cancel</button>
                                    <a href="{{ route('edit-site', ['id' => $site->id]) }}" class="btn btn-primary">Edit</a>
                                </div>
                            </div>

                            {{-- Edit Host --}}
                            <button class="btn text-info" style="border: 0;" onclick="show('edit-popup-{{$site->id}}')"><i class="fas fa-pen"></i></button>
                            
                            {{-- Delete Host --}}
                            <button class="btn text-danger" onclick="show('delete-popup-{{$site->id}}')">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </span>
                    </td>
                </tr>
            @empty
                <tr>    
                    <td>No site found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<script>

function cancel(id) {
    document.getElementById(id).style.display = 'none';
}

function show(id) {
    document.getElementById(id).style.display = 'block';
}

</script>

<div class="text-center mx-auto position-absolute" id="flash-message" style="width:30%;bottom: 5px;left:50%;transform: translate(-50%, -10%);">
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
</div>

<script>

    window.addEventListener('load', function() {
        document.getElementById('config').style.display = 'block';
        document.getElementById('config-btn').classList.toggle("active-btn");
        document.getElementById('c-all-sites').classList.toggle("active-link");
    });
        
</script>