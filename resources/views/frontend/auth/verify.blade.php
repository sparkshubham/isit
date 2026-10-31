@extends('frontend.layouts.app')
@section('content')
<section class="login-page">
       <div class="container">
           <div class="row justify-content-center align-items-center">
               <div class="col-md-6">
                  <div class="login-form">
                        <div class="login-icon" style="background: url('{{ asset('front/images/login-icon-bg.svg')}}');">
                            <img src="{{ asset('front/images/email-verification.svg') }}"  alt="Lock">
                        </div>
                        <h3> {{ __('Email Verification') }}</h3>
                        <p>Please enter the verification code sent to: <strong>{{ $user->email }}</strong></p>                        
                        <form method="POST" action="{{ route('verifypost') }}" class="digit-group" data-group-name="digits">
                            @csrf
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <input type="hidden" name="password" value="{{ $user->password }}">                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <ul class="otp-inputs">
            
                                            <input  id="digit-1" data-next="digit-2" name="otp_1" class=" inputs form-control" type="text" placeholder="0" maxlength="1" required>
                                    
                                            <input id="digit-2"  data-next="digit-3" data-previous="digit-1"  name="otp_2" class=" inputs form-control" type="text" placeholder="0" maxlength="1" required>
                                     
                                            <input id="digit-3" data-next="digit-4" data-previous="digit-2" name="otp_3" class="inputs form-control" type="text" placeholder="0" maxlength="1" required>
                                      
                                            <input id="digit-4" data-next="digit-5" data-previous="digit-3" name="otp_4" class="inputs form-control" type="text" placeholder="0" maxlength="1" required>
                                      
                                    </ul>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary">Submit</button>
                                </div>                                
                                <div class="col-md-12 text-center">                                    
                                    <div class="dont-have-account mt-3">
                                        <p class="mb-0">Didn't receive code? <a href="{{ url('resend_verify/'.$user->email) }}">Request again</a></p>
                                    </div>
                                </div>
                            </div>
                        </form>
                  </div>
               </div>
           </div>
       </div>
</section>
@endsection