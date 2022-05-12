{{-- <div class="container w-75 bg-white p-0 my-4 mx-auto shadow rounded d-flex">
    
    Left-side
    <div class="w-25 bg-light p-4 border-right">

        <span class="bg-light rounded-circle text-secondary mx-auto" id="user-items">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
        <h3>{{ auth()->user()->name }}</h3>
        @if (auth()->user()->hasRole('agent'))
            <h6 class="py-2 text-secondary" >Agent</h6>
        @else
            <h6 class="py-2 text-secondary" >Superviseur</h6>
        @endif

    </div>

    Right-side
    <div class="w-75 bg-white p-4">

    </div>

</div> --}}

<style>
    .icons{
        position: absolute;
        opacity: .5;
        top:1rem;

    }
    #edit-btn{
        right:1rem;
    }
    #key-btn{
        right:3rem;
    }
    .icons:hover{
        opacity: 1;
    }
</style>

@if (session('message'))
    <div class="alert alert-success text-center w-50 my-2 mx-auto p-2">
        {{ session('message') }}
    </div>
@endif

<div class="card bg-white shadow rounded w-50 my-4 mx-auto">

    <div class="card-header position-relative">
        <h4>My profile</h4>
        <a href="{{ route('edit-user-info') }}" class="text-primary icons" id="edit-btn" style=""><i class="fas fa-pen"></i></a>
        <a href="{{ route('change-password') }}" class="text-primary icons" id="key-btn" style=""><i class="fas fa-key"></i></a>
    </div>

    <div class="card-body">
        
        <div class="w-100">

            <div class="float-left mx-4">
                <h5 class="py-2 text-secondary" >Username </h5>
                <h5 class="py-2 text-secondary" >Email </h5>
                <h5 class="py-2 text-secondary" >Phone Number </h5>
                <h5 class="py-2 text-secondary" >User Type </h5>
                <h5 class="py-2 text-secondary" >Notified </h5>
                <h5 class="py-2 text-secondary" >Member Since </h5>
            </div>
        
            <div class="flot-right mx-4 text-secondary">
                <h5 class="py-2 text-primary" >{{ auth()->user()->name }}</h5>
                <h5 class="py-2 text-primary" >{{ auth()->user()->email }}</h5>
                <h5 class="py-2 text-primary" >{{ auth()->user()->phone_number }}</h5>
        
                @if (auth()->user()->hasRole('super_admin'))
                    <h5 class="py-2 text-primary" >Super Admin</h5>
                @endif
                @if (auth()->user()->hasRole('admin'))
                    <h5 class="py-2 text-primary" >Admin</h5>
                @endif
                @if (auth()->user()->hasRole('user'))
                    <h5 class="py-2 text-primary" >User</h5>
                @endif
                
                @if (auth()->user()->notified)
                    <h5 class="py-2 text-success" >Yes</h5>
                @else
                    <h5 class="py-2 text-danger" >No</h5>
                @endif
                
                <h5 class="py-2 text-primary" >{{ auth()->user()->created_at }}</h5>
            </div>
        
        </div>
    </div>
</div>

