@extends('frontend.layouts.app')
@section('content')  <section class="login-page">
       <div class="container">
           <div class="row justify-content-center align-items-center">
               <div class="col-md-6">
                   <div class="login-img">
                    <img src="{{ asset('front/images/login-img.png') }}"alt="">
                    </div>
                    <div class="login-app-icons">
                        <ul>
                            <li><a href=""><img  src="{{ asset('front/images/google-play-store.svg') }}"  alt=""></a></li>
                            <li><a href=""><img   src="{{ asset('front/images/ios-play-store.svg') }}" alt=""></a></li>
                        </ul>
                    </div>
               </div>
               <div class="col-md-6">
                  <div class="login-form">
                        <h3>{{ __('Login') }}</h3>
                        <p>Please login to your account.</p>
                        {{ Form::open(['url' => route('login'), 'class' =>'m-login__form m-form', 'method' => 'POST']) }}
							<div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        {{ Form::text('email', old('email'), ['class' => 'form-control required'.($errors->has('email') ? ' is-invalid' : ''),'placeholder' => 'Enter Email Address', 'label' => 'Email Address']) }}
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                            {{ Form::password('password',['class' => 'form-control required' . ($errors->has('password') ? ' is-invalid' : ''),'placeholder' => 'Password']) }}    
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        <span class="eyeicon">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    {{ Form::submit(__('Login'), ['id' => 'm_login_signin_submit', 'Class' => 'btn btn-primary']) }}
                                </div>
                                
                                <div class="col-md-12 text-center">
                                    @if (Route::has('password.request'))
                                    <a class="forgot-password" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                        <!-- <a href="forgot-password.html" class="forgot-password">Forgot Password</a> -->
                                    </a>
                                @endif
                                </div>
                            </div>
                        {{ Form::close() }}
                  </div>
                  <div class="dont-have-account">
                      <p>Don't have an account? <a href="{{ route('register') }}">Sign Up</a></p>
                  </div>
               </div>
           </div>
       </div>
   </section>
@endsection
