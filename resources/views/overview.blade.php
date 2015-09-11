<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LEAFSHOP</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .itemblock {
            margin: 10px;
        }

        body {
            background-color: black;
            background-image: none;
        }

    </style>
</head>
<body>
<div class="container">
    <div class="contentcontainer">
        @if ($errors->has())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        @foreach ([\App\Order::STATUS_PAID, \App\Order::STATUS_WAITING, \App\Order::STATUS_NEW, \App\Order::STATUS_SEND] as $orderstatustype)
                <div class="row">
                    <div class="col-md-12"><h2>{{ \App\Order::STATUS_TYPES[$orderstatustype] }}</h2></div>
                </div>

            @foreach ($ordertypes[$orderstatustype] as $order)
            <div class="row">
                <div class="col-md-3">{{ $order->ordernumber }}<br>Laatste update:<br>{{ $order->updated_at }}</div>
                <div class="col-md-3">{{ $order->name }}<br>
                    {{ $order->address1 }}<br>
                    @if($order->address2)
                        {{$order->address2}}<br>
                    @endif
                    {{ $order->postcode }} {{ $order->city }}<br>
                    @if($order->state)
                        {{$order->state}}<br>
                    @endif
                    {{ $order->country}}<br>
                    {{ $order->email}}<br>
                    Totaal: &euro; {{ $order->totalamount}}
                    </div>
                <div class="col-md-3">
                    @foreach($order->orderitems as $orderitem)
                        {{ $orderitem->item->title}} x{{ $orderitem->amount}}<br>
                    @endforeach
                </div>
                <div class="col-md-3">{{ App\Order::STATUS_TYPES[$order->status] }}</div>
                <div class="col-md-3">
                   <a href="{{ action('WelcomeController@delete', [$order->ordernumber]) }}">Verwijder order</a><br>
                    <a href="{{ action('WelcomeController@acknowledgeSend', [$order->ordernumber]) }}">Geld okay, verstuur!</a>
                </div>
            </div>
                    <hr>
            @endforeach
        @endforeach
    </div>
</div>
<!-- Scripts -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="/js/js.cookie.js"></script>
</body>
</html>
