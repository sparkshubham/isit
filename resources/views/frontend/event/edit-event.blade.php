
@extends('frontend.layouts.app')
@section('content')

<form method="POST" action="{{ route('create-event') }}" accept-charset="UTF-8"  enctype="multipart/form-data" autocomplete="off">
                @csrf

                 <input type="text" class="form-control clock-icon" name="gmt_time" id="gmt_time_t" placeholder = "Start Time">
                <div class="form-floating mb-3">
                                
                                <input type="date" class="form-control calendar-icon" name="date" id="date_event" placeholder="Date">
                                <label for="date_event">Date</label>
                            </div>
                            <div class="form-floating mb-3"> 
                                <input type="text" class="form-control clock-icon" name="start_time" id="start_time_event" placeholder = "Start Time">
                                <label for="start_time">Start Time</label>
                            </div>
                                <div class="col-6"><button  type="submit" class="btn btn-primary">Create</button></div>
                 


</form>


@endsection
