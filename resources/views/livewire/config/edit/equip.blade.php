<div class="container my-4 w-50 ">

    <form action="{{ route('save-equip-edits', $equip->id) }}" method="get">

        <div class="card">

            <div class="card-header">Define Equipement</div>

            <div class="card-body">

                {{-- Equip Name --}}
                <label for="equip_name"><b>Equip Name <span class="text-danger">*</span></b></label>
                <input type="text" name="equipName" class="form-control @error('equipName') border-danger @enderror" id="equip_name" value="{{ $equip->equip_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Service name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
                @error('equipName')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>
        
        <br>

        <button type="submit" class="btn btn-primary">Save</button>

    </form>
</div>

<script>

window.addEventListener('load', function() {
    document.getElementById('config').style.display = 'block';
    document.getElementById('config-btn').classList.toggle("active-btn");
    document.getElementById('c-equips').classList.toggle("active-link");
});
        
</script>