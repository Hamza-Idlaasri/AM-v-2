<div class="container bg-white shadow rounded w-100 my-4 px-4 py-2">
    
    <span class="float-left my-2">
        <a href="{{ route('register') }}" class="btn text-primary"><i class="fas fa-user-plus"></i></a>
    </span>

    <table class="table table-bordered text-center table-hover" {{--style="color: rgb(14,96,131)"--}}>
        
        <thead class="bg-light text-dark">
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>User Type</th>
                <th>Notified</th>
                <th>Created at</th>
                <th style="width: 10%">Edit</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>
                    
                    <td>{{ $user->phone_number }}</td>

                    @if($user->hasRole('agent'))
                        <td>Agent</td>
                    @else
                        <td>Superviseur</td>
                    @endif

                    <td>
                        @if ($user->notified)
                            <div class="form-check">
                                <input wire:click="notifiedUser('notified',{{ $user->id }})" class="form-check-input position-static" type="checkbox" name="notified[]" form="up" id="blankCheckbox" checked>
                            </div>
                        @else
                            <div class="form-check">
                                <input wire:click="notifiedUser('not_notified',{{ $user->id }})" class="form-check-input position-static" type="checkbox" name="notified[]" form="up" id="blankCheckbox">
                            </div>
                        @endif
                    </td>

                    <td>{{ date_format($user->created_at, "d M Y") }}</td>

                    <td>
                        <span class="w-100 d-flex justify-content-around align-items-center">

                            {{-- Upgrade User --}}
                            @if ($user->hasRole('agent'))
                                <div>
                                    <div class="check">
                                        <input wire:click="upgradeUser('superviseur',{{ $user->id }})" type="checkbox" id="rail" name="users[]" form="up" checked>
                                    </div>
                                </div>
                            @else
                                <div>
                                    <div class="check">
                                        <input wire:click="upgradeUser('agent',{{ $user->id }})" type="checkbox" id="rail" name="users[]" form="up">
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Delete User --}}
                            <button wire:click="deleteUser({{ $user->id }})" class="btn text-danger">
                                <i class="far fa-trash-alt" style="font-size: 1rem"></i>
                            </button>

                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No result found</td>
                </tr>
            @endforelse
        </tbody>

    </table>

    <div class="text-center mx-auto position-absolute" id="flash-message" style="width:30%;bottom: 5px;left:50%;transform: translate(-50%, -10%);">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
    </div>

</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-users').classList.toggle("active-link");
});

</script>
