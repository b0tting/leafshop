@extends('app')
@section('main')
<div class="row">
					<div class="col-sm-12">
						<h2>Please review your order before submitting:</h2><br><br>
					</div>
	<div class="col-sm-12">
		@include('emails.orderoverview')
	</div>
	<input type="hidden" id="ordernumber" value="{{$order->ordernumber}}">
</div>

	<script language="JavaScript">
		state = "review";
	</script>
@endsection