@extends('backend.layouts.login')

@section('content')
<div class="login-box">
    <div class="login-logo">			
			<a href="#">{{ Html::image('admin/admin_orange_logo.png','',['class'=>'img-responsive','alt'=>'Click box logo white'])}}</a>	
	</div>
    <div class="login-box-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
		<p class="box-msg">Reset Password</p>
        <!-- <p class="login-box-msg">{{ __('Enter new Password') }}</p> -->
        <form method="POST" action="{{ url('admin-panel/password/reset') }}" id="admin_reset_password">
            @csrf
            {{ Form::hidden('token',$token,['readonly'=>true])}}
            {{ Form::hidden('email',$email,['readonly'=>true])}}
            <div class="form-group has-feedback">
					{{ Form::label('Password', null, ['class' => 'control-label']) }}
					{{ Form::password('password', [
							'class' => 'form-control required prevent-space' . ($errors->has('password') ? ' is-invalid' : ''),
							'placeholder' => __('Enter Password'),
							'id' => 'password'
						]) }}

					@if ($errors->has('password'))
						<span class="invalid-feedback text-danger" role="alert">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
			</div>
            <div class="form-group has-feedback">
					{{ Form::label('Confirm Password', null, ['class' => 'control-label']) }}
					{{ Form::password('password_confirmation', [
							'class' => 'form-control required prevent-space' . ($errors->has('password') ? ' is-invalid' : ''),
							'placeholder' => __('Enter Confirm Password')
						]) }}

					@if ($errors->has('password_confirmation'))
						<span class="invalid-feedback text-danger" role="alert">
							<strong>{{ $errors->first('password_confirmation') }}</strong>
						</span>
					@endif
			</div>

            <div class="row mt-12">
				<div class="col-xs-4">

						{{ Form::submit(__('Update'), [
							'id' => 'm_login_signin_submit',
							'class' => 'btn btn-primary'
						]) }}
				</div>
				<div class="col-xs-8">
					<a href="{{ route('admin-login') }}" class="m-link m-link-bb" style="float:right">{{ __('Back to Login') }}</a>
				</div>
            </div>
        </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".prevent-space").keypress(function (evt) {
	    var keycode = evt.charCode || evt.keyCode;
	    if (keycode  == 32) {
	      return false;
	    }
	  });
	$.validator.addMethod('checkPassword', function (value) {
        return /^(?=.*?[0-9])(?=.*?[a-z])(?=.*[A-Z])(?=.*[~!@#$%^&*()_+=-]).{8,15}$/.test(value);
	}, 'Your password must have 8 to 15 characters containing 1 upper case letter, 1 lower case letter, 1 special character & 1 digit.');

	$('#admin_reset_password').validate({
	    debug: false,
	    ignore : [],
	    errorClass: 'help-block',
	    errorElement: "span",
	    rules: {
	        password: {
	                required: true,
	                checkPassword: true
	            },
	        password_confirmation: {
	                required: true,
	                equalTo : '#password'
	            },
			captcha: {
	                required: true,
	            },
	        },
	        //errorPlacement: validateErrorPositioning,
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
	        },
	        messages: {
				password:{required: 'Password is required'},
	            password_confirmation: {
					required: 'Confirm Password is required',
	                equalTo: 'Confirm password and password must be same.'
	            },
				captcha:{required: 'Captcha is required'},
	        }
	});
})
</script>
@endsection
