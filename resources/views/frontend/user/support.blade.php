@extends('frontend.layouts.app')
@section('content')
@php
        $categories = postHelp();
    @endphp

<section class="main-wrapper">             
        <div class="container-card">
            <div class="card">
                <div class="card-header bg-white">
                    Help 
                </div>
                <div class="card-body">
                    <div class="help-body">       
                        <form method="POST"  action="{{ route('support-user-web') }}" id="help-form" autocomplete="false">
                        @csrf              
                            <div class="form-floating  mb-3 category-arrow">
                                    <select  class="form-select category" id="help_category"  name="category" aria-label="Floating label select example">
                                        <option disabled selected>Please select one</option>
                                            @foreach($categories as $cate)
                                                <option value="{{ $cate->name }}" data-name="{{ $cate->name }}">{{ $cate->name }}</option>
                                            @endforeach
                                    </select>
                                    <label for="floatingSelect">Category</label>
                            </div>                            
                            <div class="form-floating hide" id="help_description_hide">
                                <textarea style="resize: none" class="form-control "  name="description" placeholder="Leave a description here" id="help_description"></textarea>
                                <label for="help_description">Description</label>
                            </div>
                            <div class="row mt-4">
                                <div class="col-6"><a href="{{ route('post-board') }}"><button type="button" id="help_cancel" class="btn btn-secondary">Cancel</button></a></div>
                                <div class="col-6"><button type="submit" class="btn btn-primary">Submit</button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection
