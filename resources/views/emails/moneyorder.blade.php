<h2>Thank you for your order!</h2>

<p>We have recieved your order and will start on packaging as soon as possible. Your order details: </p>
@include("emails.orderoverview")
<br>
<h2>Money order details</h2>
<p>Please wire the total amount of {{ $order->totalamount }} to our bank, 123.456 with your order number ({{$order->ordernumber}}}as a comment. After receiving the money, we will ship your order.</p>

<p>For questions, you can mail us at {{ \App\Http\Controllers\WelcomeController::LEAFMAIL }}</p>
<br>
Regards, <br>
<br>
<br>
<a href="leaf-music.com">LEAF</a>

