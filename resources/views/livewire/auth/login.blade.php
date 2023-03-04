<div class="container pt-0 pr-4 pb-4 pl-4 w-25 bg-white shadow-sm rounded">
    <div class="container w-100 text-center p-4">
        <img src="{{ asset('image/net-logo.png') }}" alt="alarm manager logo" width="120px">
    </div>

    <form wire:submit.prevent="login">
        
        @if (session('status'))
            <div class="alert alert-danger text-center">
                {{ session('status') }}
            </div>
        @endif

        <div class="form-group">
            <input type="text" wire:model="name" class="form-control @error('name') border-danger @enderror" placeholder="Username" value="{{ old('name') }}" pattern="[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]{3,15}" title="Username must be between 3 & 15 charcarters in length and containes only letters, numbers, and these symbols -_()">
            @error('name')
                <div class="text-danger">
                        {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" wire:model="password" class="form-control @error('password') border-danger @enderror" placeholder="Password" pattern="[a-zA-Z0-9-_().@$=%&#+{}*ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]{5,12}" title="Password must be between 6 & 12 charcarters in length and containes only letters, numbers, and these symbols -_().@$=%&#+{}*">
            @error('password')
            <div class="text-danger">
                    {{ $message }}
            </div>
            @enderror   
        </div>
        
        <div class="clear-fix">
            <div class="form-group float-left">
                <input type="checkbox" tabindex="3" class="" wire:model="remember" name="remember" id="remember">
                <label for="remember"> Remember Me</label>
            </div>

            {{-- <div class="form-group float-right">  
                <a href="" tabindex="5" class="forgot-password">Mot de pass oublie?</a>
            </div> --}}
        </div>

        <div>
            <button type="submit" class="btn btn-primary w-100 font-weight-bold">Login</button>
        </div>
    </form>
</div>