<h2>Thank you for your order!</h2>

<p>We have recieved your order and will start on packaging as soon as possible. Your order details: </p>
@include("emails.orderoverview")
<br>
<h2>Money order details</h2>
<p>Please wire the total amount of &euro; {{ $order->totalamount }} to our bank, with your order number ({{$order->ordernumber}}} as a comment. After receiving the money, we will ship your order.</p>

<p>Our bank details:</p>
<table style="border: none">
    <tbody>
        <tr>
            <td>Bank</td>
            <td>{{ config('shop.bank') }}</td>
        </tr>
        <tr>
            <td>IBAN</td>
            <td>{{ config('shop.iban') }}</td>
        </tr>
        <tr>
            <td>BIC</td>
            <td>{{ config('shop.bic') }}</td>
        </tr>
        <tr>
            <td>Account holder name</td>
            <td>{{ config('shop.bankname') }}</td>
        </tr>
        <tr>
            <td>Account address</td>
            <td>{{ config('shop.billaddress') }}</td>
        </tr>
    </tbody>
</table>

<p>For questions, you can mail us at {{ config('shop.shopcontactmail') }}</p>
<br>
Regards, <br>
<br>
<br>
<a href="http://leaf-music.com">LEAF</a>

