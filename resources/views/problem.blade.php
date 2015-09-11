@extends('app')
@section('main')
    <div class="row">
        <div class="col-sm-8">
            <h2>Oh no!</h2><br><br>
        </div>
        <div class="col-sm-8">
            <p>So sorry to say, but there was a problem processing your Paypal request. You can try again, but consider trying an order using a bank payment</p>
        </div>
    </div>

    <script language="JavaScript">
        state = "problem";
    </script>
@endsection