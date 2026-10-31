@extends('frontend.layouts.app')
@section('content')
   <section class="login-page">
       <div class="container">
           <div class="row justify-content-center align-items-center">
               <div class="col-md-6">
                   <div class="login-img">
                    <img  src="{{ asset('front/images/login-img.png') }}"  alt="">
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
                        <h3>Sign Up</h3>
                        <p>We would love you to join us</p>
                        <form method="POST" action="{{ route('register') }}">
                        @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <!-- <input class="form-control" type="text" placeholder="First Name"> -->
                                        <input placeholder="First Name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                               <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <!-- <input placeholder="First Name" class="form-control" type="text" placeholder="Last Name"> -->
                                        <input placeholder="Last Name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input placeholder="Email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <!-- <input class="form-control" type="password" placeholder="Password"> -->
                                        <input placeholder="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <!-- <input name="password" type="password" value="" class="form-control" id="password" placeholder="New Password" /> -->
                                        <span class="eyeicon toggle-password" onclick="password_show_hide();">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-input">
                                        <!-- <input class="form-control" type="password" placeholder="Password"> -->
                                        <input  placeholder="Confirm Password" name="password_confirmation" type="password" value="" class="form-control" id="password_confirmation" placeholder="Confirm New Password" />
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <span class="eyeicon toggle-password" onclick="password_show_hide();">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mt-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                        <label class="form-check-label font-12" for="flexCheckDefault">
                                            I agree to the <a href="#">Terms and Conditions</a>.
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        Sign Up Now
                                    <!-- {{ __('Register') }} -->
                                    </button>
                                </div>
                            </div>
                        </form>
                  </div>
                  <div class="dont-have-account">
                      <p>Already have an account? <a href="{{ route('login') }}">Log In</a></p>                      
                  </div>
               </div>
           </div>
       </div>
   </section>

@endsection
