@extends('layouts.app')

@section('content')

<div class="container p-4 bg-white rounded shadow mx-auto my-4 w-50">

    <form action="{{ route('createBG') }}" method="get">
        <label for="bg_name"><b>Boxgroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="boxgroup_name" class="form-control @error('boxgroup_name') border-danger @enderror" id="bg_name" value="{{ old('boxgroup_name') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{4,20}" title="HostGroup name must be between 4 & 20 charcarters in length and containes only letters, numbers, and these symbols -_+">
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

        <div class="p-2 bg-white" style="overflow: auto;max-height:200px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @forelse ($boxes as $box)
                <input type="checkbox" name="members[]" id="{{$box->box_name}}" value="{{$box->box_name}}"> <label for="{{$box->box_name}}">{{$box->box_name}}</label>
                <br>
            @empty
                <p>No boxes</p>
            @endforelse
        </div>
        
        <br>

        <button class="btn btn-primary">Add</button>

    </form>

</div>

@endsection