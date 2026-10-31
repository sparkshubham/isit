@extends('frontend.layouts.app')
@section('content')

   <section class="login-page">
       <div class="container">
           <div class="row justify-content-center align-items-center">
               <div class="col-md-6">
                   <div class="login-img">
                    <img src="{{ asset('front/images/login-img.png') }}"alt="">
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
                        <h3>{{ __('Login') }}</h3>
                        <p>Please login to your account.</p>
                        {{ Form::open(['url' => route('front-login'), 'class' =>'m-login__form m-form', 'id'=>'loginFormRequest', 'method' => 'POST']) }}
							<div class="row">
                            <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input type="radio" id="email_type" value="email" name="login_type" checked>
                                        <label for="email_type">Email</label>
                                        
                                        <input type="radio" id="phone_type" value="phone" name="login_type">
                                        <label for="phone_type">Phone</label>
                                    </div>

                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input email_selection">
                                        {{ Form::text('email', old('email'), ['class' => 'form-control required'.($errors->has('email') ? ' is-invalid' : ''),'placeholder' => 'Enter Email Address', 'label' => 'Email Address']) }}
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="contactnumber w-100 mb-3 phone_selection hide">    
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
                                            {{ Form::password('password',['id'=>'password', 'class' => 'form-control required' . ($errors->has('password') ? ' is-invalid' : ''),'placeholder' => 'Password']) }}    
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        <span class="eyeicon" id="first_password">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    {{ Form::submit(__('Login'), ['id' => 'm_login_signin_submit', 'Class' => 'btn btn-primary']) }}
                                </div>
                                <div class="col-md-12 text-center">
                                    <a href="{{ route('password-forgot') }}" class='forgot-password'>Forgot Password?</a>
                                </div>
                            </div>
                        {{ Form::close() }}
                  </div>
                  <div class="dont-have-account">
                      <p>Don't have an account? <a href="{{ route('front-register') }}">Sign Up</a></p>
                  </div>
               </div>
           </div>
       </div>
   </section>
@endsection
@section('page_wise_scripts')
<script>
    $(document).ready(function(){
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
<!-- {!! JsValidator::formRequest('App\Http\Requests\LoginRequest') !!} -->
<script>
    $(document).ready(function(){
        // $('#first_password').on('click', function(){
        //     var passInput=$("#password");
        //     if(passInput.attr('type')==='password'){
        //         passInput.attr('type','text');
        //     }else{
        //         passInput.attr('type','password');
        //     }
        // })

        $('#loginFormRequest').validate({
            rules: {
                email: {
                    required: function(element) {
                        if($('input[name="login_type"]:checked').val() === 'email'){
                            return true ;
                        }else{
                            return false; 
                        }
                    },
                    email: true
                },
                phone_number: {
                    required: function(element) {
                        return $('input[name="login_type"]:checked').val() === 'phone';
                    },
                    // Add additional validation rules for phone numbers if needed
                },
                password: {
                    required: true
                }
            },
            messages: {
                email: {
                    required: "Please enter your email address",
                    email: "Please enter a valid email address"
                },
                phone_number: {
                    required: "Please enter your phone number"
                },
                password: {
                    required: "Please enter your password"
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.attr('name') === 'email' || element.attr('name') === 'phone_number') {
                    error.insertAfter(element.closest('.form-floating'));
                } else {
                    element.closest('.form-input').append(error);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    })

    
$(document).ready(function() {
    $("input[name='login_type']").change(function() {
        if ($(this).val() === "phone") {
            $(".phone_selection").removeClass('hide');
            $(".email_selection").addClass('hide');
            
        } else {
            $(".phone_selection").addClass('hide');
            $(".email_selection").removeClass('hide');
        }
    });
});
</script>
@endsection



