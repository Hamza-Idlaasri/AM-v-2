<div class="container pt-0 pr-4 pb-4 pl-4 w-25 bg-white shadow-sm rounded">
    <div class="container w-100 text-center p-4">
        <img src="{{ asset('image/am-logo-180px.png') }}" alt="alarm manager logo">
    </div>

    <form wire:submit.prevent="register">

        {{-- Username --}}
        <div class="form-group">
            <input type="text" wire:model="name" class="form-control @error('name') border-danger @enderror" placeholder="Username" value="{{ old('name') }}" pattern="[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]{3,15}" title="Username must be between 3 & 15 charcarters in length and containes only letters, numbers, and these symbols -_()">
            @error('name')
                <div class="text-danger">
                        {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <input type="email" wire:model="email" class="form-control @error('email') border-danger @enderror" placeholder="Email"  value="{{ old('email') }}">
            @error('email')
                <div class="text-danger">
                        {{ $message }}
                </div>
            @enderror
        </div>
       
        {{-- Phone Number --}}
        <div class="form-group">
            <div class="d-flex">
                <span class="unity">+212</span>
                <input type="text" wire:model="phone_number" class="form-control p-unity @error('phone_number') border-danger @enderror" placeholder="Phone Number" value="{{ old('phone_number') }}" pattern="[0-9]{9}" title="Phone Number must be at least 9 numbers">
            </div>
            @error('phone_number')
                <div class="text-danger">
                        {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <input type="password" wire:model="password" class="form-control @error('password') border-danger @enderror" placeholder="Password" pattern="[a-zA-Z0-9-_().@$=%&#+{}*ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]{5,12}" title="Password must be between 6 & 12 charcarters in length and containes only letters, numbers, and these symbols -_().@$=%&#+{}*">
            @error('password')
            <div class="text-danger">
                    {{ $message }}
            </div>
            @enderror   
        </div>
        
        {{-- Confirm Password --}}
        <div class="form-group">
            <input type="password" wire:model="password_confirmation" class="form-control" placeholder="Confirme Password">
        </div>
        
        {{-- Append to a site --}}
        <div class="p-2 rounded @error('password') border-danger @enderror" style="max-height: 120px;overflow: auto;border: 1px solid #ced4da;">
                
            @forelse ($all_sites as $site)
                @if ($site->id != 1)
                    <input type="radio" wire:model="site" id="{{$site->id}}" value="{{$site->site_name}}"> <label for="{{$site->id}}">{{ $site->site_name }}</label>
                    <br>
                @endif
            @empty
                <p>No site found</p>
            @endforelse

        </div>
            @error('site')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror   
        <br>

        {{-- Submit Button --}}
        <div>
            <button type="submit" class="btn btn-primary w-100 font-weight-bold">Register</button>
        </div>
    </form>
</div>
