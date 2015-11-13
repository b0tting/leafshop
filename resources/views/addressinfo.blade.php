@extends('app')
@section('main')
<div class="row">
	<div class="col-md-6 col-md-offset-2">
		{!! Form::open(array('url' => action("WelcomeController@review"), 'method' => 'POST', 'class'=>"form-horizontal", 'role'=>'form', 'name'=>'shipping', 'id'=>'shipping')) !!}
		<fieldset>

				<!-- Form Name -->
				<h2>Address Details</h2><br><br>

				<!-- Text input-->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">Name*</label>
					<div class="col-sm-9">
						{!! Form::text('name','', $attributes = array('class'=>'form-control','placeholder'=>'Receiver Name',)) !!}
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">Email*</label>
					<div class="col-sm-9">
						{!! Form::text('email','', $attributes = array('class'=>'form-control','placeholder'=>'Email Address',)) !!}
						<small>We will also use this to contact you with questions on your order if needed so</small>
					</div>

				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">Line 1*</label>
					<div class="col-sm-9">
						{!! Form::text('address1','', $attributes = array('class'=>'form-control','placeholder'=>'Address',)) !!}
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">Line 2</label>
					<div class="col-sm-9">
						{!! Form::text('address2','', $attributes = array('class'=>'form-control','placeholder'=>'(if applicable)',)) !!}
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">City*</label>
					<div class="col-sm-9">
						{!! Form::text('city','', $attributes = array('class'=>'form-control',)) !!}
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">State</label>
					<div class="col-sm-3">
						{!! Form::text('state','', $attributes = array('class'=>'form-control',)) !!}
						<small>..if applicable</small>
					</div>

					<label class="col-sm-3 control-label" for="textinput">Postcode*</label>
					<div class="col-sm-3">
						{!! Form::text('postcode','', $attributes = array('class'=>'form-control',)) !!}
					</div>
				</div>



				<!-- Text input-->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">Country*</label>
					<div class="col-sm-9">
						{!! Form::select('country', \App\Http\Controllers\WelcomeController::COUNTRIES, '', ["id"=>"country", "class"=>"form-control"]) !!}
					</div>
				</div>

				<!-- Text input-->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="textinput">Payment method</label>
					<div class="col-sm-9">
						{!! Form::select('payment_method', \App\Http\Controllers\WelcomeController::PAYMENT_METHODS, '', ["id"=>"payment_method", "class"=>"form-control"]) !!}
						<small>For now, we only accept money transfers and Paypal.</small>
					</div>
				</div>

			</fieldset>
			<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<input type="hidden" id="cartjson" name="cartjson" value="" />
		{!! Form::close() !!}
	</div><!-- /.col-lg-12 -->
</div>
	<script language="JavaScript">
		state = "shipping";
	</script>
@endsection