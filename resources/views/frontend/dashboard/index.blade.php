@extends('frontend.layouts.app')
@section('content')  
   	<section class="main-wrapper">
        <div class="container">
            <div class="gallery-images"  id="post_ano_exp">
                @if(count($posts) > 0)
                        @include('frontend.dashboard.post-index')
                @endif
            </div>
        </div>
    </section>
@endsection

