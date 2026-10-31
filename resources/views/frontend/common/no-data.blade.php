<div class="no_data_found">
    <img src="{{ asset('front/images/nodata.svg') }}" alt="">
    @if(!empty($error_name))
            <p>{{$error_name}}</p>
    @else
            <p>No data found</p>
    @endif
</div>
                                