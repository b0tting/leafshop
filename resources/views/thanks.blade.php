@extends('app')
@section('main')
    <div class="row">
        <div class="col-sm-8">
            <h2>Thank you!</h2><br><br>
        </div>
        <div class="col-sm-8">

            @if($order->status == \App\Order::STATUS_PAID)
                <p style="word-wrap: break-word;">Thank you for your order! We received a payment okay from Paypal, so we'll get right on to the packaging and send out your package as soon as possible!</p>
                @else
                <p style="word-wrap: break-word;">Thank you for your order! You should receive a mail with the payment details soon. When we receive your payment, we'll get right on to the packaging and send out everything as soon as possible!</p>
            @endif
            <p>If the e-mail fails to arrive, please check your spam folders. For questions relating to your order, feel free to contact us at {{ config('shop.shopcontactmail')}}, please refer to your order number ({{ $order->ordernumber }}) in the subject.</p>
            <p>For reference, here's your order once more:</p>
        </div>
        <div class="col-sm-8">
            @include('emails.orderoverview')
        </div>
    </div>

    <script language="JavaScript">
        state = "thanks";
    </script>
@endsection