@extends('frontend.layouts.app')

@section('content')
<section class="login-page">
       <div class="container">
           <div class="row justify-content-center align-items-center">
               <div class="col-md-6">
                  <div class="login-form">
                        <div class="login-icon" style="background: url('{{ asset('front/images/login-icon-bg.svg')}}');">
                            <img  src="{{ asset('front/images/lock.svg') }}"  alt="Lock">
                        </div>
                        <h3>{{ __('Set Password') }}</h3>
                        <p>Kindly set your password for future logins.</p>
                        <form method="POST" action="{{ route('password.reset') }}" id="reset-form">
                          @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                    <input type="hidden" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? ''}}">
                                        <input  placeholder="Password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                                        <span class="eyeicon toggle-password" id="first_password">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>                                        
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input  placeholder="Confirm Password" id="confirm_password" type="password" class="form-control @error('confirm_password') is-invalid @enderror" name="confirm_password" autocomplete="current-password">
                                        <span class="eyeicon toggle-password" id="confirm-password">
                                            <i class="uil uil-eye" id="show_eye"></i>
                                            <i class="uil uil-eye-slash d-none" id="hide_eye"></i>
                                        </span>
                                        @error('confirm_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                       @enderror
                                       <div style="margin-top: 7px;" id="CheckPasswordMatch"></div>
                                        <br>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary butnSubmit">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                  </div>
               </div>
           </div>
       </div>
   </section>
@endsection
@section('page_wise_scripts')
    {!! JsValidator::formRequest('App\Http\Requests\ResetPasswordRequest','#reset-form') !!}
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
