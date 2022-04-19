@component('mail::message')
{{-- Alarm Manager detected @if (Str::plural('Box',sizeof($boxs)) == 'Boxes') those @endif @if (Str::plural('Box',sizeof($boxs)) == 'Box') this @endif {{ Str::plural('Box',sizeof($boxs))}} : --}}
@foreach ($boxes as $box)
@component('mail::table')
|               |               |
| ------------- | ------------- |
| @component('mail::bolder') Box name @endcomponent   | {{ $box->box_name}} |
| @component('mail::bolder') Address IP @endcomponent | {{ $box->address }} |
| @component('mail::bolder') State      @endcomponent | @switch($box->state) @case(0) @component('mail::badge', ['class' => 'badge-success']) Up @endcomponent | @break @case(1) @component('mail::badge', ['class' => 'badge-danger']) Down @endcomponent | @break @case(2) @component('mail::badge', ['class' => 'badge-unknown']) Unreachable @endcomponent @break @endswitch |
| @component('mail::bolder') Date/Time  @endcomponent | {{ $box->start_time }} |
| @component('mail::bolder') Info       @endcomponent | {{$box->long_output}} |
| @component('mail::bolder') Notif type @endcomponent | @switch($box->notification_reason) @case(0) Normal notification  @break @case(1) Problem acknowledgement  @break @case(2) Flapping started  @break @case(3) Flapping stopped  @break @case(4) Flapping was disabled  @break @case(5) Downtime started  @break @case(6) Downtime ended  @break @case(7) Downtime was cancelled  @break @endswitch |
@endcomponent
@endforeach

{{-- @component('mail::subcopy')
    This is a subcopy component
@endcomponent --}}

@endcomponent