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
                        <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-input">
                                        <input placeholder="Email Address" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
            
                                </div>
                                
                                <div class="col-md-12 text-center">
                                    <a href="" class="forgot-password">Already have a code</a>
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
