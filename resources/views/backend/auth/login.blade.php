@extends('backend.layouts.login')

@section('content')

<div class="login-box">
	
		<div class="login-logo">			
			<a href="#">{{ Html::image('admin/admin_orange_logo.png','',['class'=>'img-responsive','alt'=>'Click box logo white'])}}</a>
			
		</div>
		<div class="login-box-body">
			<p class="box-msg">Sign In</p>
			@if($errors->has('deactivated'))
			<div class="alert alert-danger alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				{{$errors->get('deactivated')[0]}}
			</div>
			@endif
			@if (session('status'))
			<div class="alert alert-success" role="alert">
			{{ session('status') }}
			</div>
			@endif
			@if(session()->has('message'))
			<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				{{ session()->get('message') }}
			</div>
			@endif

			<!-- <p class="login-box-msg">Sign in to start your session</p> -->
				{{ Form::open([
								'url' => route('admin-login'),
								'class' =>'m-login__form m-form',
								'method' => 'POST'
							]) }}
							@csrf
				<div class="login-error">
					@if (Session::has('isExhibitor'))
						<label class="alert alert-danger">{{ session('isExhibitor') }}</label>
					@endif
				</div>
				<div class="form-group has-feedback">
					{{ Form::label('Email Address', null, ['class' => 'control-label']) }}
					{{ Form::text('email', old('email',$email ? $email: ''), [
							'class' => 'form-control required'.($errors->has('email') ? ' is-invalid' : ''),
							'placeholder' => 'Enter Email Address',
							'label' => 'Email Address'
						]) }}
					<span class="fa fa-envelope form-control-feedback"></span>
					@if ($errors->has('email'))

						<span class="invalid-feedback text-danger" role="alert">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group has-feedback">
						<!-- {{ Form::password('password',[
								'class' => 'form-control required' . ($errors->has('password') ? ' is-invalid' : ''),
								'placeholder' => 'Password'
							]) }} -->
						{{ Form::label('Password', null, ['class' => 'control-label']) }}
						<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder = "Enter Password" value={{ $password }}>
						<span class="fa fa-lock form-control-feedback"></span>
						@if ($errors->has('password'))
							<span class="invalid-feedback text-danger" role="alert">
								<strong>{{ $errors->first('password') }}</strong>
							</span>
						@endif
				</div>
				<div class="row mt-12">
					<div class="col-xs-8">
						<div class="checkbox icheck">
							<label class="">

								<input type="checkbox" name="remember" {{$is_checked}}> Remember Me
							</label>
						</div>
					</div>
				</div>
				<div class="row mt-12">
					<div class="col-xs-4 text-left">

							{{ Form::submit(__('Login'), [
								'id' => 'm_login_signin_submit',
								'class' => 'btn btn-primary'
							]) }}

					</div>
					<div class="col-xs-8 text-right">
						<a href="{{ route('admin-panel.password.reset') }}" id="m_login_forget_password" class="m-link">{{ __('Forgot password?') }}</a>
					</div>
				</div>

				{{ Form::close() }}

		</div>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script type="text/javascript">
// submit form
	// function onSubmit(token) {
	// 	alert();
    // 	// document.getElementById("demo-form").submit();
   	// }
	$('#captcha').val('');
	$('#refresh').click(function(){
	  $.ajax({
	     type:'GET',
		 url:'admin-panel/refreshcaptcha',
		 beforeSend: function() {
			$(".captcha span").html('<center><i class="fa fa-spinner"></i></center>');
		 },
	     success:function(data){
	        $(".captcha span").html(data.captcha);
	     }
	  });
});
</script>
@endsection
