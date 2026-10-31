

                                
@extends('frontend.layouts.app')
@section('content')
    <section class="main-wrapper">
                <div class="no_data_found">
                        <img src="{{ asset('front/images/nodata.svg') }}" alt="">
                @if(!empty($error_name))
                        <p>{{$error_name}}</p>
                @else
                        <p>No Post found</p>
                @endif
                </div>
    </section>
@endsection
