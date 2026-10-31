@extends('backend.layouts.login')

@section('content')
<style>
.login-box-msg{
	padding:50px 0 0 0;
}
</style>
<div class="login-box">
	<div class="login-logo">
		 <!-- <a href="#"><img src="../img/Vector Smart Object.png" width="360"></a> -->
  	</div>
	<div class="login-box-body">
		<a href="#">{{Html::image('admin/logo-white.svg','',['class'=>'img-responsive','alt'=>'Click App logo white','style'=>'margin:0 auto;'])}}</a>
		<h1 style="color:#313131; text-align:center;">Link Expired</h1>
		<p class="login-box-msg">Please <a href="{{ url('admin-panel') }}"> click here </a> to login.</p>
	</div>
</div>
@endsection
