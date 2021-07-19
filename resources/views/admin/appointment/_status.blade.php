@if($appointment['status'])
    <input type="checkbox" class="change_appointment_status" id="change_status_{{$appointment['id']}}" appointment-id='{{$appointment['id']}}' checked netliva-switch data-active-text="{{__('Completed')}}" data-passive-text=" {{__('Pending')}}"/>
@else 
    <input type="checkbox" class="change_appointment_status" id="change_status_{{$appointment['id']}}" appointment-id='{{$appointment['id']}}' netliva-switch data-active-text="{{__('Completed')}}" data-passive-text="{{__('Pending')}}"/>
@endif