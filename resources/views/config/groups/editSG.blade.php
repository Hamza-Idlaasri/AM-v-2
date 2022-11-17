@extends('layouts.app')

@section('content')

<div class="container p-3 bg-white w-50 rounded shadow mx-auto my-4">

    <form action="{{ route('editSG', $servicegroup->servicegroup_id) }}" method="get">
        <label for="hg_name"><b>Servicegroup Name <span class="text-danger">*</span></b></label>
        <input type="text" name="servicegroup_name" class="form-control w-100 @error('servicegroup_name') border-danger @enderror" id="hg_name" value="{{ $servicegroup->servicegroup_name }}" pattern="[a-zA-Z][a-zA-Z0-9-_+ ]{2,200}" title="ServiceGroup name must be between 2 & 200 charcarters in length and containes only letters, numbers, and these symbols -_+">
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

        <div class="p-2 bg-white w-100" style="overflow: auto;max-height:250px;border:1px solid rgb(216, 215, 215);border-radius:5px">
            @forelse ($services as $service)
                @if (in_array($service->service_object_id, $all_members))
                    <input type="checkbox" name="members[]" id="{{$service->service_object_id}}" value="{{$service->service_object_id}}" checked> <label for="{{$service->service_object_id}}" style="user-select: none"> {{$service->service_name}} <span class="text-secondary">({{$service->host_name}})</span></label>
                    <br>
                @else
                    <input type="checkbox" name="members[]" id="{{$service->service_object_id}}" value="{{$service->service_object_id}}"> <label for="{{$service->service_object_id}}" style="user-select: none"> {{$service->service_name}} <span class="text-secondary">({{$service->host_name}})</span></label>
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