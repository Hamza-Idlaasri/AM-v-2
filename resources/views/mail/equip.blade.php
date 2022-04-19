@component('mail::message')
{{-- Alarm Manager detected @if (Str::plural('Box',sizeof($boxs)) == 'Boxes') those @endif @if (Str::plural('Box',sizeof($boxs)) == 'Box') this @endif {{ Str::plural('Box',sizeof($boxs))}} : --}}
@foreach ($equips as $equip)
@component('mail::table')
|               |               |
| ------------- | ------------- |
| @component('mail::bolder') Box name @endcomponent   | {{ $equip->box_name}} |
| @component('mail::bolder') Equip name @endcomponent | {{ $equip->equip_name }} |
| @component('mail::bolder') State      @endcomponent | @switch($equip->state) @case(0) @component('mail::badge', ['class' => 'badge-success']) Ok @endcomponent  @break @case(1) @component('mail::badge', ['class' => 'badge-warning']) Warning @endcomponent  @break @case(2) @component('mail::badge', ['class' => 'badge-danger']) Down @endcomponent  @break @case(3) @component('mail::badge', ['class' => 'badge-unknown']) Unknown @endcomponent @break @endswitch |
| @component('mail::bolder') Date/Time  @endcomponent | {{ $equip->start_time }} |
| @component('mail::bolder') Info       @endcomponent | {{ $equip->long_output }} |
| @component('mail::bolder') Notif type @endcomponent | @switch($equip->notification_reason) @case(0) Normal notification  @break @case(1) Problem acknowledgement  @break @case(2) Flapping started  @break @case(3) Flapping stopped  @break @case(4) Flapping was disabled  @break @case(5) Downtime started  @break @case(6) Downtime ended  @break @case(7) Downtime was cancelled  @break @endswitch |
@endcomponent
@endforeach

{{-- @component('mail::subcopy')
    This is a subcopy component
@endcomponent --}}

@endcomponent