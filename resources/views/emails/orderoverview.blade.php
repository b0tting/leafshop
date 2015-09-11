<style>
    th, td {
        padding: 2px 10px;
    }
</style>
<h2>Order information</h2>
<table style="margin: 10px; padding: 10px;">
    <tbody>
        <tr>
            <td>Order number</td>
            <td>{{$order->ordernumber}}</td>
        </tr>
        <tr>
            <td>Name</td>
            <td>{{$order->name}}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>{{$order->email}}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td>{{$order->address1}}
                @if(isset($order->address2))
                <br>{{$order->address1}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Postal code</td>
            <td>{{$order->postcode}} {{$order->city}}</td>
        </tr>
        @if(isset($order->state))
            <tr>
                <td>State</td>
                <td>{{$order->state}}</td>
            </tr>
        @endif
        <tr>
            <td>Country</td>
            <td>{{\App\Http\Controllers\WelcomeController::COUNTRIES[$order->country]}}</td>
        </tr>
        <tr>
            <td>Payment type</td>
            <td>{{\App\Http\Controllers\WelcomeController::PAYMENT_METHODS[$order->payment_method] }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <th>Item</th>
        <th>Price</th>
        <th>Amount</th>
        <th>Subtotal</th>
    </thead>
    <tbody>
@foreach ($order->orderItems as $orderitem)
    <tr>
        <td>{{ $orderitem->item->title}}</td>
        @if($orderitem->item_id != \App\Item::SHIPPING)
            <td>&euro; {{ number_format($orderitem->itemprice, 2, ",", ".") }}</td>
            <td>x {{ $orderitem->amount }}</td>
            <td>&euro; {{ number_format($orderitem->amount * $orderitem->itemprice, 2, ",", ".")}}</td>
        @else
            <td></td>
            <td></td>
            <td>&euro; {{ number_format($orderitem->itemprice, 2, ",", ".")}}</td>

        @endif
    </tr>
@endforeach
    <tr>
        <td colspan="4"><h2>Total amount: &euro; {{ number_format($order->totalamount, 2, ",", ".")}}</h2></td>
    </tr>
    </tbody>
</table>





