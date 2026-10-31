@extends('frontend.layouts.app')
@section('content')
   <section class="login-page">
       <div class="container">
           <div class="row justify-content-center align-items-center">
               <div class="col-md-6">
                  <div class="login-form">
                        <div class="login-icon"  style="background: url('{{ asset('front/images/login-icon-bg.svg')}}');">
                            <img  src="{{ asset('front/images/email-verification.svg') }}" alt="Lock">
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <h3>Forgot Password</h3>
                        <p>Please enter your email address to reset your password</p>
                        <form method="POST" action="{{ route('password.email') }}" id="forget_form">
                        @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    
                                <div class="mypost-and-save-post-section">
                                    <ul class="nav nav-tabs mb-3" id="tabs-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="email-tab-id" data-bs-toggle="pill" data-bs-target="#email-tab" type="button" role="tab" aria-controls="my-posts" aria-selected="true">Email</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="phone-tab-id" data-bs-toggle="pill" data-bs-target="#phone-tab" type="button" role="tab" aria-controls="saved-posts" aria-selected="false">Phone</button>
                                        </li>
                                    </ul>
                                                        
                                    <div class="tab-content" id="tabs-tabContent">
                                        <div class="tab-pane fade show active" id="email-tab" role="tabpanel" aria-labelledby="email-tab-id">
                                            
                                            <div class="form-input">
                                                <input type="hidden" name="mail_send" id="mail_send"  value="0">
                                                <input placeholder="Email Address" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="phone-tab" role="tabpanel" aria-labelledby="phone-tab-id">
                                        
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
                                        </div>
                                    </div>
                                </div>
                               
                                    
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
            
                                </div>                                
                                <div class="col-md-12 text-center">
                                    <a href="#" id="already_code" class="forgot-password">Already have a code</a>
                                </div>
                            </div>
                        </form>
                  </div>
                  <div class="dont-have-account">
                      <p><a href="{{ route('login') }}"><img  src="{{ asset('front/images/back-arrow.svg') }}" alt=""> Back to Login</a></p>
                  </div>
               </div>
           </div>
       </div>
   </section>
   @endsection
@section('page_wise_scripts')
    <script>
        $('#already_code').click(function(){
               
            var email=$("#email").val();
            $('#mail_send').val(1)
            $('#forget_form').submit();
           
        });
        
        $(document).ready(function () {
            // Initialize the tab behavior
            $(".nav-link").click(function () {
                var targetTab = $(this).attr("data-bs-target");
                var oppositeTab = targetTab === "#email-tab" ? "#phone-tab" : "#email-tab";
                
                // Clear the input field of the opposite tab
                $(oppositeTab + " input").val("");
            });
        });


        $('#forget_form').validate({
                debug: false,
                ignore : [],
                errorClass: 'help-block',
                errorElement: "span",
                rules: {
                    email: {
                        required: function() {
                          return $("#email-tab").hasClass("active");
                        }
                    },
                    phone_number: {
                        required: function() {
                          return $("#phone-tab").hasClass("active");
                        }
                    },
                },
                messages: {
                    email: {
                        required: "The email is required.",
                    },
                    phone_number: {
                        required: "The phone number is required.",
                    },
                },

                errorPlacement: function(error,element){
                    if($(element).prop('nodeName') == 'SELECT'){
                    error.insertAfter($(element).next('span'));
                    }
                    else if($(element).prop('type') == 'file'){
                    error.insertAfter($(element).parent().parent());
                    }
                    else{
                    error.insertAfter($(element));
                    }
                },
                highlight: function (element, errorClass, validClass)
                        {
                            $(element).parents('.col-field-wrapper').addClass('has-error');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).parents('.col-field-wrapper').removeClass('has-error');
                        },
                        invalidHandler: function(form, validator)
                        {
                            if (!validator.numberOfInvalids())
                                return;

                            $('html, body').animate({
                                scrollTop: ($(validator.errorList[0].element).offset().top - 100)
                            }, 500);
                        }
                });
        </script>

        
@endsection
