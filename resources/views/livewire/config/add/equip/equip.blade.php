<div class="container py-4 d-flex justify-content-center">
    <form action="{{ route('create-equip') }}" method="get" class="w-50">
        <div class="card my-2 rounded bg-white shadow-sm w-100 mx-auto">
            
            <div class="card-header">
                Define Equipement:
            </div>
            {{-- Equipements --}}
            <div class="card-body">
                <label for="equip_name"><b>Equipement name <span class="text-danger">*</span></b> </label>
                <input type="text" name="equip_name" class="form-control @error('equip_name') border-danger @enderror" id="equip_name" value="{{ old('equip_name') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Equip name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
                @error('equip_name')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            {{-- Choose Box --}}
            <div class="card-body">
                <label for="box_id"><b>Choose Box <span class="text-danger">*</span></b> </label>
                @error('box_id')
                <div class="text-danger">
                    {{ $message }}
                </div>
                @enderror
                <div class="p-2 rounded @error('box_id') border-danger @enderror" style="max-height: 180px;overflow: auto;border: 1px solid #ced4da;">
                        
                    @forelse ($boxes as $box)
                        <input type="radio" name="box_id" id="{{$box->host_object_id}}" value="{{$box->host_object_id}}" @if ($box->pins_not_used == 0) disabled @endif> <label for="{{$box->host_object_id}}" @if ($box->pins_not_used == 0) class="text-muted" style="opacity:.7" @endif>{{$box->box_name}} ({{$box->pins_used}}/{{$box->total_pins}}) @if ($box->pins_not_used == 0)<i class="ml-2 text-danger">(All pins of this box used)</i> @endif</label>
                        <br>
                    @empty
                        <p>No box found</p>
                    @endforelse

                </div>
            </div>    
        </div>
        <br>
        <button class="btn btn-primary px-4 w-25" >Add</button>
    </form>

<script>

    window.addEventListener('load', function() {
        document.getElementById('config').style.display = 'block';
        document.getElementById('config-btn').classList.toggle("active-btn");
        document.getElementById('c-equips').classList.toggle("active-link");
    });
        
</script>
</div>
