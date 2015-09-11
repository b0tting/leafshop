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
			@if ($errors->has())
				<div class="alert alert-danger">
					@foreach ($errors->all() as $error)
						{{ $error }}<br>
					@endforeach
				</div>
			@endif
			@yield('main')
			<div id="cart"></div>
			<div><button id="prevstep" style="color:black;">Button</button><button id="nextstep" style="color:black;">Button</button></div>
		</div>
	</div>
	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	 <script src="/js/js.cookie.js"></script>
<script>
	var cart = Cookies.getJSON('cart');
	if(!cart) {
		var cart = { country: "",items: Array() };
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
				cart.items.push({itemid: id, itemprice: $(this).attr("itemprice"), itemname : $(this).attr("itemname"), itemamount : 1, shippingfactor: $(this).attr("shippingfactor")})
			}

			// Now redraw cart contents
			fillCart(cart);
		});
		}

	// Used in cart drawing

	$("#country").change(function() {
		cart.country = $("#country").val();
		fillCart(cart);
	})

	// Take shipping weight from all items and recalc shipping based on country
	// Don't worry - we will recalc this server side to prevent people from messing here
	// Bad thing: it's not easy to change costs like this. I should have done an ajax call
	var shipping ={"NL":{'light': '2.52', 'heavy': '3.84'}, "BE":{'light':'5.25', 'heavy': '9.45'},"OTHER":{'light': '5.75', 'heavy': '10.35'}}
	function recalcShipping() {
		if(cart.country) {
			cartRemove(-1);
			weight = 0;
			$.each(cart.items, function (index, value) {
				weight += value.shippingfactor * value.itemamount
			})
			code = ['BE', 'NL'].indexOf(cart.country) > -1 ? cart.country : "OTHER";
			costs = weight >= 3 ? shipping[code].heavy :shipping[code].light

			cart.items.push({
				itemid: -1,
				itemprice: costs,
				itemname: "Shipping to " + cart.country,
				itemamount: 1,
				shippingfactor: 0
			})
		}
	}

	// Set up submit button
	// TODO: Deze urls moeten uit actions komen en niet met de hand zijn uitgeschreven
	targettext = "";
	targeturl = "";
	prevtext = "";
	prevurl = "";
	showcart = true;
	switch(state) {
		case 'shipping':
			targettext = "Review and complete"
			targeturl = "/review";
			prevtext = "Return to shop";
			prevurl = "/";
			break;
		case 'shop':
			targettext = "Enter shipping address"
			targeturl = "/addressinfo";
			prevtext = "";
			prevurl = "/";
			break;
		case 'review':
			targettext = "Submit order"
			targeturl = "/submit";
			prevtext = "Return to shipping address";
			prevurl = "/addressinfo";
			showcart = false;
			break;
		case 'thanks':
			targettext = "To shop"
			targeturl = "/";
			prevtext = "";
			prevurl = "";
			showcart = false;
			break;
		case 'problem':
			targettext = "To shop"
			targeturl = "/";
			prevtext = "";
			prevurl = "";
			showcart = false;
			break;
		default:
			targettext = "error";
			break;
	}

	// Wat doet de PREV knop vandaag?
	$("#prevstep").text(prevtext);
	$("#prevstep").click(function() {
		window.location.replace(prevurl);
	})
	if(prevtext == "") {
		$("#prevstep").hide()
	}

	// Wat doet de NEXT knop deze ronde?
	$("#nextstep").text(targettext);
	$("#nextstep").click(function() {
		if(state == "shipping") {
			$("#cartjson").val(JSON.stringify(cart));
			$("#shipping").submit()
		} else if(state == "review") {
			ordernumber = $("#ordernumber").val();
			window.location.replace(targeturl + "/" + ordernumber);
		} else {
			window.location.replace(targeturl);
		}
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

		// If only shipping is left..
		if(cart.items.length == 1 && cart.items[0].itemid == -1) {
			cart.items = new Array();
		}

		// OEI! LELIJK! Niet redraw bij shipping remove
		if(id != -1) {
			fillCart(cart);
		}
	}

	function fillCart(cart) {
		if (showcart) {
			// Ik wed dat het cookie ook de lucht in moet!
			Cookies.set("cart", cart);

			// Ook even shipping opnieuw doen, er is vast iets veranderd
			recalcShipping();

			carthtml = '<div class="row">';
			carthtml += '<div class="col-md-12"><h2>Order overview<h2></div>'
			totalamount = 0
			$.each(cart.items, function (index, value) {
				carthtml += '<div class="col-md-5 col-md-offset-1">' + value.itemname + "</div>";
				carthtml += '<div class="col-md-2">&euro; ' + value.itemprice + "</div>";
				carthtml += '<div class="col-md-2">x ' + value.itemamount + "</div>";

				if (value.itemid != -1 && state != "review") {
					carthtml += '<div class="col-md-2"><button class=".btn-sm" type="button" style="color: black;" onClick="cartRemove(' + value.itemid + ')">Remove</button></div>';
				}
				totalamount += (value.itemamount * value.itemprice)
			});

			if (totalamount > 0) {

				carthtml += '<div class="col-md-12"><h2>Total amount: &euro; ' + totalamount.toFixed(2) + '</h2></div></div>';
				$("#nextstep").show()
				$("#cart").html(carthtml);
			} else {
				$("#cart").html("");
				$("#nextstep").hide()
			}

		}
	}

	if(state == 'thanks') {
		Cookies.remove('cart');
	} else {
		fillCart(cart);
	}


</script>
</body>
</html>
