@extends('frontend.layouts.app')
@section('content')

 <section class="main-wrapper">             
        <div class="container-card">
            <div class="card">
                <div class="card-header bg-white">
                    Profile 
                </div>
                <div class="card-body">
                    <div class="profile-card-body">
                    <form method="POST" action="{{$profileUpdateBtnLink }}" id="profile-form"  accept-charset="UTF-8" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="form-control"   name="id" value="@if(isset($user) && !empty($user)){{$user->id}}@else{{old('id')}}@endif">
                        <input name="value_pic" type='hidden' id="value_pic" value="@if(isset($user) && !empty($user->profile_picture)){{1}}@endif"  /></input>
                             
                         <input type="hidden" name="remove_img" value="0" id="remove_img"> 
         
                            <div class="avatar-upload mb-3" for="profile_picture">
                                <div class="avatar-edit">
                                    <input name="profile_picture" type='file' id="profile_picture" value="@if(isset($user) && !empty($user)){{$user->profile_picture}}@else{{old('profile_picture')}}@endif" accept=".png, .jpg,.jpeg" /></input>
                                    <label for="profile_picture"><img src=" {{ asset('front/images/pencil.svg') }} " alt=""></label>
                                </div>
                                <div class="avatar-preview">
                                    @if(isset($user['image_path']) && $user['image_path_exist'])
                                        <div id="imagePreview" style="background-image: url('{{  asset($user['image_path_exist']) }}');">
                                        </div>
                                    @else
                                        <div id="imagePreview" style="background-image: url({{ asset('front/images/placeholder-user.png') }});">
                                        </div>
                                    @endif
                                </div>
                            </div>
                             @if ($errors->has('profile_picture'))
                             <span for="profile_picture" style="margin-left: 48px;" class="help-block error-help-block">{{ $errors->first('profile_picture') }}</span>
                         
                        
                            @endif
                        
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput"  name="first_name" placeholder="First Name" value="@if(isset($user) && !empty($user)){{$user->first_name}}@else{{old('first_name')}}@endif">
                                <label for="floatingInput">First Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="last_name"  name="last_name" placeholder="Last Name" value="@if(isset($user) && !empty($user)){{$user->last_name}}@else{{old('last_name')}}@endif">
                                <label for="last_name">Last Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name ="email" disabled  placeholder="Email Address" value="@if(isset($user) && !empty($user)){{$user->email}}@else{{old('email')}}@endif">
                                <label for="email">Email Address</label>
                                <span class="correctemail"><img src="{{ asset('front/images/correct-email.svg') }}" alt=""></span>
                            </div>
                            <div class="contactnumber w-100 mb-3">    
                                <div class="form-floating select-arrow" for="country_code">
                                    {{ Form::select('country_code', $country_arr, isset($user['country_code']) ? $user['country_code']: null, ['class'=>'form-control', 'id'=>'country_code']) }}
               
                                    <label for="country_code">Code</label>
                                </div>
                                <div class="form-floating">
                                    <input type="number" class="form-control w-100" id="phone_number"  name="phone_number" placeholder="Phone Number" value="@if(isset($user) && !empty($user)){{$user->phone_number}}@else{{old('phone_number')}}@endif">
                                    <label for="phone_number">Phone Number</label>
                                </div>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Leave a bio. here" id="bio_text" name="bio_text"  value="" style="resize:none;" >@if(isset($user) && !empty($user)){{$user->bio_text}}@else{{old('bio_text')}}@endif</textarea>
                                <label for="floatingTextarea">Bio</label>
                            </div>
                            <div class="change-assword">
                                <a href="#" id="password_request_add" data-bs-toggle="modal" data-bs-target="#ChangePassword">Change Password</a>
                            </div>
                                      
                            <div class="row">
                                <div class="col-6"> <a href="{{$backBtnLink}}" class="btn btn-secondary">Cancel</a> 
                                <!-- <button type="button" class="btn btn-secondary">Cancel</button> -->
                            </div>
                                       
                                <div class="col-6"><button type="submit" class="btn btn-primary">Update</button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
  
@include('frontend.user.change-password');


@endsection
