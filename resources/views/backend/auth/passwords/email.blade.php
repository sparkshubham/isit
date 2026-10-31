@extends('backend.layouts.login')

@section('content')
<div class="login-box">
        <div class="login-logo">			
			<a href="#">{{ Html::image('admin/admin_orange_logo.png','',['class'=>'img-responsive','alt'=>'Click box logo white'])}}</a>	
		</div>
    <div class="login-box-body">
        <p class="box-msg">Forgot Password</p>
        <!-- @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif -->
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
        <form method="POST" action="{{ route('admin-panel.password.email') }}">
            @csrf

            <div class="form-group has-feedback">
				{{ Form::label('Email Address', null, ['class' => 'control-label']) }}
				{{ Form::text('email', old('email'), [
						'class' => 'form-control required'.($errors->has('email') ? ' is-invalid' : ''),
						'placeholder' => 'Enter Email Address'
					]) }}
				<span class="fa fa-envelope form-control-feedback"></span>
				@if ($errors->has('email'))
					<span class="invalid-feedback text-danger" role="alert">
						<strong>{{ $errors->first('email') }}</strong>
					</span>
				@endif
			</div>
            <div class="row mt-12">
				<div class="col-xs-4">

						{{ Form::submit(__('Send'), [
							'id' => 'm_login_signin_submit',
							'class' => 'btn btn-primary'
						]) }}
				</div>
				<div class="col-xs-8">
					<a href="{{ route('admin-login') }}" class="m-link m-link-bb" style="float:right;">{{ __('Back to Login') }}</a>
				</div>
            </div>
        </form>
        </div>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script type="text/javascript">
	$('#captcha').val('');
	$('#refresh').click(function(){
	  $.ajax({
	     type:'GET',
		 url:'{{url("admin-panel/refreshcaptcha")}}',
		 beforeSend: function() {
			 $('#captcha').val('');
			$(".captcha span").html('<center><i class="fa fa-spinner"></i></center>');
		 },
	     success:function(data){
	        $(".captcha span").html(data.captcha);
	     }
	  });
});
</script>
@endsection
