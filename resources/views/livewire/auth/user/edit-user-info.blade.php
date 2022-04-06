<div class="container bg-white w-25 shadow-sm rounded p-3 my-4">

    <form wire:submit.prevent="edit">
        
        <div class="form-group">
            <label for="username" class="font-weight-bold text-secondary">Username :</label><br>
            <input type="text" wire:model="username" class="form-control @error('username') border-danger @enderror" id="username" value="{{ $username }}" pattern="[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]{3,15}" title="Username must be between 3 & 15 charcarters in length and containes only letters, numbers, and these symbols -_()">
            @error('username')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror   
        </div>
        
        <div class="form-group">
            <label for="email" class="font-weight-bold text-secondary">Email :</label><br>
            <input type="email" wire:model="email" class="form-control @error('email') border-danger @enderror" id="email" value="{{ $email }}">
            @error('email')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror   
        </div>
        
        <div class="form-group">
            <label for="phone_number" class="font-weight-bold text-secondary">Phone Number :</label><br>
            <div class="d-flex">
                <span class="unity">+212</span>
                <input type="text" wire:model="phone_number" class="form-control p-unity @error('phone_number') border-danger @enderror" id="phone_number" value="{{ $phone_number }}" pattern="[0-9]{9}" title="Phone Number must be at least 9 numbers">
            </div>
            @error('phone_number')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>
        
        <br>

        <div class="form-check d-flex justify-content-between p-0">
            
            <div>
                <h6 class="font-weight-bold text-secondary">Receive email notifications</h6>
            </div>
            
            <div>
                @if ($notified)
                    <div style="width: 35px">
                        <div class="check">
                            <input type="checkbox" wire:model="notified" id="rail" value="{{ $user_id }}" checked>
                        </div>
                    </div>
                @else
                    <div class="d-inline" style="width: 35px">
                        <div class="check">
                            <input type="checkbox" wire:model="notified" id="rail" value="{{ $user_id }}">
                        </div>
                    </div>
                @endif
            </div>
            <br><br>
        </div>

        <button type="submit" class="btn btn-primary w-100 font-weight-bold">Save Changes</button>
        
    </form>

</div>
