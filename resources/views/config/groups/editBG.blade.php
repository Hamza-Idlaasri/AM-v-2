@extends('layouts.app')

@section('content')

<div class="container p-3 bg-white w-50 rounded shadow mx-auto my-4">

    <form action="{{ route('editBG', $boxgroup->hostgroup_id) }}" method="get">
        <label for="hg_name"><b>Boxgroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="boxgroup_name" class="form-control w-100 @error('boxgroup_name') border-danger @enderror" id="hg_name" value="{{ $boxgroup->boxgroup_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="Boxgroup name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
        @error('boxgroup_name')
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

        <div class="p-2 bg-white w-100" style="overflow: auto;max-height:250px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @foreach ($boxes as $box)
                @if (in_array($box->host_object_id, $all_members))
                    <input type="checkbox" name="members[]" id="{{$box->host_object_id}}" value="{{$box->box_name}}" checked> <label for="{{$box->host_object_id}}"> {{$box->box_name}}</label>
                    <br>
                @else
                    <input type="checkbox" name="members[]" id="{{$box->host_object_id}}" value="{{$box->box_name}}"> <label for="{{$box->host_object_id}}"> {{$box->box_name}}</label>
                    <br>
                @endif
            @endforeach
        </div>
        <br>

        <button class="btn btn-primary">Edit</button>

    </form>

</div>

@endsection