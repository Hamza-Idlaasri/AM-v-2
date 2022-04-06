<div class="container bg-white w-25 shadow-sm rounded p-3 my-4">

    <form wire:submit.prevent="changePWD">

        @if (session('status'))
            <div class="alert alert-danger text-center">
                {{ session('status') }}
            </div>
        @endif
        
        <div class="form-group">
            <label for="old_password">Old Password :</label><br>
            <input type="password" class="form-control @error('old_password') border-danger @enderror" wire:model="old_password" id="old_password"  pattern="[a-zA-Z0-9\.]{4,12}" title="Password must be between 4 & 12 charcarters in length and containes only letters, numbers, and symbols">
            @error('old_password')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror   
        </div>
        
        <div class="form-group">
            <label for="password">New Password :</label><br>
            <input type="password" class="form-control @error('password') border-danger @enderror" wire:model="password" id="password" name="password" pattern="[a-zA-Z0-9\.]{4,12}" title="Password must be between 4 & 12 charcarters in length and containes only letters, numbers, and symbols">
            @error('password')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror   
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirme New Password :</label><br>
            <input type="password" class="form-control" wire:model="password_confirmation" name="password_confirmation" id="password_confirmation">
        </div>

        <button type="submit" class="btn btn-primary w-100 ">Save Changes</button>
    </form> 
</div>
