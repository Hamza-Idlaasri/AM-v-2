@extends('layouts.app')

@section('content')

<div class="container p-3 bg-white w-50 rounded shadow mx-auto my-4">

    <form action="{{ route('editEG', $equipgroup->servicegroup_id) }}" method="get">
        <label for="eg_name"><b>Equipgroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="equipgroup_name" class="form-control w-100 @error('equipgroup_name') border-danger @enderror" id="eg_name" value="{{ $equipgroup->equipgroup_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,20}" title="EquipGroup name must be between 2 & 20 charcarters in length and containes only letters, numbers, and these symbols -_+">
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

        <div class="p-2 bg-white w-100" style="overflow: auto;max-height:200px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @forelse ($equips as $equip)
                @if (in_array($equip->service_object_id, $all_members))
                    <input type="checkbox" name="members[]" id="mbrs" value="{{$equip->service_object_id}}" checked> {{$equip->equip_name}} <span class="text-secondary">({{$equip->box_name}})</span>
                    <br>
                @else
                    <input type="checkbox" name="members[]" id="mbrs" value="{{$equip->service_object_id}}"> {{$equip->equip_name}} <span class="text-secondary">({{$equip->box_name}})</span>
                    <br>
                @endif
                
            @empty
                <p>No services</p>
            @endforelse
        </div>
        <br>

        <button class="btn btn-primary">Edit</button>

    </form>

</div>

@endsection