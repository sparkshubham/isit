@extends('frontend.layouts.app')
@section('content')


   <section class="main-wrapper">
    <div class="container">
        <div class="card">
            <div class="card-header bg-white">
                PRIVACY POLICY
            </div>
            <div class="card-body">
                <div class="terms-conditions-section">
                <p>{!! $content !!}</p>
                </div>
            </div>
        </div>
    </div>
    </section>



    
@endsection
