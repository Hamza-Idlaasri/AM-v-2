@extends('layouts.app')

@section('content')

<div class="container p-4 bg-white rounded shadow mx-auto my-4 w-50">

    <form action="{{ route('createEG') }}" method="get">
        <label for="hg_name"><b>Equipgroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="equipgroup_name" class="form-control @error('equipgroup_name') border-danger @enderror" id="hg_name" value="{{ old('equipgroup_name') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Equip. name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
        @error('equipgroup_name')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror
        
        <br>
        
        <label for="mbrs"><b>Members <span class="text-danger">*</span></b></label>
        <br>
        @error('members')
            <div class="text-danger">
                {{ $message }}
            </div>
        @enderror

        <div class="p-2 bg-white" style="overflow: auto;max-height:250px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @forelse ($equips as $equip)
            <input type="checkbox" name="members[]" id="{{$equip->service_object_id}}" value="{{$equip->service_object_id}}"> <label for="{{$equip->service_object_id}}" style="user-select: none">{{$equip->equip_name}} <span class="text-secondary">({{$equip->box_name}})</span></label>
            <br>
            @empty
            <p>No equipments</p>
            @endforelse
        </div>

        <br>

        <button class="btn btn-primary">Add</button>

    </form>

</div>

@endsection