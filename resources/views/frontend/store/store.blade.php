
@extends('frontend.layouts.app')
@section('content')
@php

$filter_arr = config('constants.store_filter');
$store_filter_exchange = config('constants.store_filter_exchange');

 @endphp

<section class="main-wrapper">
        <div class="container">     
            <div class="main-title d-flex align-items-center justify-content-between">
                <h3>Store</h3>
                <div class="acreate-add-and-filter">
                    <div class="store-filter-dropdown">
            @php
            $link = request()->url();
            $link_array = explode('/',$link);
            $valueUrl= end($link_array);
            @endphp

                {{ Form::select('product_type', $filter_arr, isset(request()->product_type) ? request()->product_type: null, ['class'=>'form-select country', 'id'=>'filter_store']) }}
    
                       <!--                
                        <select  class="form-select country"  id="filter_store"  name="product_type" aria-label="Floating label select example">
                             <option value="">ALL</option>
                                @foreach($filter_arr as $key=>$value)
                                    <option value="{{ $key}}">
                                        {{ $value}}</option>
                                @endforeach
                        </select>
                        -->

                    </div>
                    <a class="btn btn-secondary" onclick="createAdmodalOpen()" data-bs-toggle="modal">Create Ad</a>
                </div>
            </div>
         
            @if(count($products)>0)
                <div class="row" id="store_list">
                        @include('frontend.store.list')  
                </div>
            @else
                    @php  $data_arr=['error_name'=>'No product found'];@endphp
                    @include('frontend.common.no-data',$data_arr)
            @endif
        </div>
        
       
    </section>

    @include('frontend.store.add-modal')  

@endsection





