<h2>Thank you for your order!</h2>

<p>We have recieved your order and payment and will start on packaging as soon as possible. Your order details: </p>
@include("emails.orderoverview")
<br>
<p>For questions, you can mail us at {{ \App\Http\Controllers\WelcomeController::LEAFMAIL }}</p>
<br>
Regards, <br>
<br>
<br>
<a href="leaf-music.com">LEAF</a>

