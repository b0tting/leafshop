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

	</style>
</head>
<body>
	 <div class="container" style="margin:auto; margin-top: 5%; width: 1200px; align:left;">
		<div class="contentcontainer" style="width: 760px;">
			@yield('main')
			<div id="cart"></div>
			<div><button id="nextstep" style="color:black;">Enter Shipping address</button></div>
		</div>
	</div>
	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	 <script src="/js/js.cookie.js"></script>
<script>
	var cart = Cookies.getJSON('cart');
	if(!cart) {
		var cart = { items: Array() };
	}

	// Elke button krijgt zo een eigen actie toegevoegd
	if ($("button.button_add").size() > 0) {
		$(".button_add").click(function () {
			isin = false;
			id = $(this).attr("itemid");
			$.each(cart.items, function(index, value) {
				if(value.itemid == id) {
					isin = true;
					value.itemamount++
				}
			});
			if(!isin) {
				cart.items.push({itemid: id, itemprice: $(this).attr("itemprice"), itemname : $(this).attr("itemname"), itemamount : 1})
			}

			// Now redraw cart contents
			fillCart(cart);
		});
		}

	$("#nextstep").click(function() {
		window.location.replace("/addressinfo");
	})

	function cartRemove(id) {

		removeItem = -1
		$.each(cart.items, function(index, value) {
			if (value.itemid == id) {
				removeItem = index;
			}
		})
		if(removeItem >= 0) {
			cart.items.splice(removeItem, 1);
		}
		fillCart(cart);
	}

	function fillCart(cart) {
		// Ik wed dat het cookie ook de lucht in moet!
		Cookies.set("cart", cart);

		carthtml =  '<div class="row">';
		carthtml +=  '<div class="col-md-12"><h2>Order overview<h2></div>'

		totalamount = 0
		$.each(cart.items, function(index, value) {
			carthtml += '<div class="col-md-6">' + value.itemname + "</div>";
			carthtml += '<div class="col-md-2">' + value.itemprice + "</div>";
			carthtml += '<div class="col-md-2">' + value.itemamount + "</div>";
			carthtml += '<div class="col-md-2"><button class="btn-sm" style="color: black;" onClick="cartRemove('+ value.itemid+ ')">Remove</button></div>';
			totalamount += (value.itemamount * value.itemprice)
		});
		if(totalamount > 0) {
			carthtml += '<div class="col-md-12">Total amount: &euro; ' + totalamount.toFixed(2) + '</h2><br>(excluding <a href="">shipping</a>)</div></div>';
			$("#nextstep").show()
			$("#cart").html(carthtml);
		} else {
			$("#cart").html("");
			$("#nextstep").hide()
		}
	}

	fillCart(cart);


</script>
</body>
</html>
