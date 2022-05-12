@extends('layouts.app')

@section('content')

<div class="container p-4 bg-white rounded shadow mx-auto my-4 w-50">

    <form action="{{ route('createSG') }}" method="get">
        <label for="hg_name"><b>Servicegroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="servicegroup_name" class="form-control @error('servicegroup_name') border-danger @enderror" id="hg_name" value="{{ old('servicegroup_name') }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,20}" title="ServiceGroup name must be between 2 & 20 charcarters in length and containes only letters, numbers, and these symbols -_+">
        @error('servicegroup_name')
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
        @forelse ($services as $service)
            <input type="checkbox" name="members[]" id="{{$service->service_object_id}}" value="{{$service->service_object_id}}"> <label for="{{$service->service_object_id}}" style="user-select: none">{{$service->service_name}} <span class="text-secondary">({{$service->host_name}})</span></label>
            <br>
        @empty
            <p>No services</p>
        @endforelse
        </div>
        <br>

        <button class="btn btn-primary">Add</button>

    </form>

</div>

@endsection