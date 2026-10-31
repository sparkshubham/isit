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
                           <li><a href="https://play.google.com/store/apps/details?id=com.isit.app"><img  src="{{ asset('front/images/google-play-store.svg') }}"  alt=""></a></li>
                            <li><a href="https://apps.apple.com/us/app/isit-in-spirit-in-truth/id1637031067"><img   src="{{ asset('front/images/ios-play-store.svg') }}" alt=""></a></li>
                        </ul>
                    </div>
               </div>
               <div class="col-md-6">
                  <div class="login-form">
                        <h3>Sign Up</h3>
                        <p>We would love you to join us</p>
                        <form method="POST" action="{{ route('postregister') }}" id="postregister-form" autocomplete="off">
                        @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input placeholder="First Name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" autocomplete="name" autofocus>
                                        @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                               <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input placeholder="Last Name" id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" autocomplete="name" autofocus>
                                        @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input placeholder="Email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
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
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input placeholder="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <span class="eyeicon toggle-password" id="first_password">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-input">
                                        <input name="confirm_password" type="password" value="" class="form-control" id="confirm_password" placeholder="Confirm New Password" />
                                        @error('confirm_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <span class="eyeicon toggle-password" id="confirm-password">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>
                                    </div>
                                </div>                                
                                <div class="col-md-12 mt-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" name="term_n_condition" type="checkbox" value="1" id="term_n_condition">
                                        <label class="form-check-label font-12" for="term_n_condition">
                                            I agree to the <a href="{{ url('terms-of-uses') }}">Terms and Conditions</a> and <a href="{{ url('privacy-policy') }}">Privacy Policy</a>.
                                        </label>
                                        @error('terms')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Sign Up Now</button>
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
@section('page_wise_scripts')
{!! JsValidator::formRequest('App\Http\Requests\FrontRegisterRequest','#postregister-form') !!}

<script>
    $(document).ready(function(){
        $('#confirm-password').on('click', function(){
            var passInput=$("#confirm_password");
            if(passInput.attr('type')==='password')
            {
                passInput.attr('type','text');
            }else{
                passInput.attr('type','password');
            }
        })

        $('#term_n_condition').on('click', function(){
           var value= $("#term_n_condition").val(1)
            if(value==1)
            {
               $("#term_n_condition").val(1);
            }
        })

        
        $('#first_password').on('click', function(){
            var passInput=$("#password");
            if(passInput.attr('type')==='password')
            {
                passInput.attr('type','text');
            }else{
                passInput.attr('type','password');
            }
        })
    })
</script>
@endsection
