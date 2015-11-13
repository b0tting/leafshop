<?php namespace App\Http\Controllers;

use App\OrderItem;
use DB;
use PayPal\Exception\PayPalConnectionException;
use PhpSpec\Exception\Exception;
use Validator;
use Input;
use Cookie;
use Mail;
use Redirect;
use Config;
use Request;
use Log;
use App\Item;
use App\Order;
use PayPal\Rest\ApiContext;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ItemList;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;

class WelcomeController extends Controller {

	const PAYMENT_BANK = 1;
	const PAYMENT_PAYPAL = 2;
	const PAYMENT_METHODS = [self::PAYMENT_BANK=>"Money transfer", self::PAYMENT_PAYPAL=>"Paypal"];
	//const PAYMENT_METHODS = [self::PAYMENT_BANK=>"Money transfer"];

	const COUNTRIES = [""=>"","AR"=>"Argentina","AU"=>"Australia","AT"=>"Austria","BY"=>"Belarus","BE"=>"Belgium","BA"=>"Bosnia and Herzegovina","BR"=>"Brazil","BG"=>"Bulgaria","CA"=>"Canada","CL"=>"Chile","CN"=>"China","CO"=>"Colombia","CR"=>"Costa Rica","HR"=>"Croatia","CU"=>"Cuba","CY"=>"Cyprus","CZ"=>"Czech Republic","DK"=>"Denmark","DO"=>"Dominican Republic","EG"=>"Egypt","EE"=>"Estonia","FI"=>"Finland","FR"=>"France","GE"=>"Georgia","DE"=>"Germany","GI"=>"Gibraltar","GR"=>"Greece","HK"=>"Hong Kong S.A.R., China","HU"=>"Hungary","IS"=>"Iceland","IN"=>"India","ID"=>"Indonesia","IR"=>"Iran","IQ"=>"Iraq","IE"=>"Ireland","IL"=>"Israel","IT"=>"Italy","JM"=>"Jamaica","JP"=>"Japan","KZ"=>"Kazakhstan","KW"=>"Kuwait","KG"=>"Kyrgyzstan","LA"=>"Laos","LV"=>"Latvia","LB"=>"Lebanon","LT"=>"Lithuania","LU"=>"Luxembourg","MK"=>"Macedonia","MY"=>"Malaysia","MT"=>"Malta","MX"=>"Mexico","MD"=>"Moldova","MC"=>"Monaco","ME"=>"Montenegro","MA"=>"Morocco","NL"=>"Netherlands","NZ"=>"New Zealand","NI"=>"Nicaragua","KP"=>"North Korea","NO"=>"Norway","PK"=>"Pakistan","PS"=>"Palestinian Territory","PE"=>"Peru","PH"=>"Philippines","PL"=>"Poland","PT"=>"Portugal","PR"=>"Puerto Rico","QA"=>"Qatar","RO"=>"Romania","RU"=>"Russia","SA"=>"Saudi Arabia","RS"=>"Serbia","SG"=>"Singapore","SK"=>"Slovakia","SI"=>"Slovenia","ZA"=>"South Africa","KR"=>"South Korea","ES"=>"Spain","LK"=>"Sri Lanka","SE"=>"Sweden","CH"=>"Switzerland","TW"=>"Taiwan","TH"=>"Thailand","TN"=>"Tunisia","TR"=>"Turkey","UA"=>"Ukraine","AE"=>"United Arab Emirates","GB"=>"United Kingdom","US"=>"USA","UZ"=>"Uzbekistan","VN"=>"Vietnam"];

	const PAYPAL_OK = 1;
	const PAYPAL_PROBLEM = 2;

	const NL_LIGHT = "3.60";
	const NL_HEAVY = "6.80";
	const BE_LIGHT = "6.90";
	const BE_HEAVY = "13.60";
	const ALL_LIGHT = "7.50";
	const ALL_HEAVY = "14.70";

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	static $shopmail = "";

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
		self::$shopmail = config("shop.shopmail");
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('shop', ['items'=>Item::where("id", ">", "0")->get()]);
	}

	public function addressinfo()
	{
		return view('addressinfo');
	}

	public function review()
	{
		$rules= array(
			'name' => 'required|min:5',
			'email' => 'required|email',
			'address1' => 'required|min:5',
			'city' => 'required',
			'postcode' => 'required|min:3',
			'country' => 'required|max:2',
			'payment_method' => 'required|max:6'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator->errors())->withInput();
		} else {
			$addressinfo = Input::except('_token', 'page', 'cartjson');
			$order = Order::create($addressinfo);
			$order->ordernumber = "LEAF-" . mt_rand(100000, 999999);
			$cart = json_decode(Input::get('cartjson'));
			$totalamount = 0;
			foreach($cart->items as $cartitem) {
				$item = Item::find($cartitem->itemid);
				if($item->id == Item::SHIPPING) {
					$shipping = $this->calculateShipping($order);
					$orderitem = OrderItem::create(["item_id"=>$item->id, "order_id"=>$order->id, "itemprice"=>$shipping, "amount"=>1 ]);
					$totalamount += $shipping;
				} else {
					$orderitem = OrderItem::create(["item_id"=>$item->id, "order_id"=>$order->id, "itemprice"=>$item->price, "amount"=>$cartitem->itemamount ]);
					$totalamount += $item->price * $orderitem->amount;
				}
				$orderitem->save();

			}
			$order->totalamount = $totalamount;
			$order->save();
			return view('review', ["order"=>$order]);
		}
	}

	public static function calculateShipping($order) {
		$weight = 0;
		foreach($order->orderitems as $orderitem) {
			if($orderitem->itemid != Item::SHIPPING) {
				$weight += $orderitem->item->shippingfactor;
			}
		}

		$isheavy = $weight >= 3;

		$costs = $isheavy ? self::NL_HEAVY : self::NL_LIGHT;
		if($order->country == "BE") {
			$costs = $isheavy ? self::BE_HEAVY : self::BE_LIGHT;
		} else if($order->country != "NL") {
			$costs = $isheavy ? self::ALL_HEAVY : self::ALL_LIGHT;
		}

		return $costs;
	}

	public function submit($ordernumber) {
		$order = Order::where('ordernumber', "=", $ordernumber)->first();
		if($order->payment_method == self::PAYMENT_BANK) {
			Mail::send('emails.moneyorder', ['order' => $order], function ($message) use ($order) {
				$message->from(self::$shopmail, 'LEAF Music');
				$message->subject('LEAF Music Order ' . $order->ordernumber . ' - waiting for payment');
				$message->to($order->email)->bcc(self::$shopmail)->replyTo(self::$shopmail);
			});

			Mail::send('emails.kaatmail', ['order' => $order], function ($message) use ($order) {
				$message->from(self::$shopmail, 'LEAF Music');
				$message->subject('LEAF Music Order ' . $order->ordernumber . ' - waiting for payment');
				$message->to(self::$shopmail);
			});
			$order->status = Order::STATUS_WAITING;
			$order->save();
			return Redirect::action('WelcomeController@result', [$order->ordernumber]);
		} else {
			// setup PayPal api context
			$paypal_conf = \Config::get('paypal');
			$api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
			$api_context->setConfig($paypal_conf['settings']);
			$payer = new Payer();
			$payer->setPaymentMethod("paypal");
			$itemarray = [];
			$details = new Details();
			$subtotal = 0;
			foreach($order->orderitems as $orderitem) {
				//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/CreatePaymentUsingPayPal.html
				//http://learninglaravel.net/integrate-paypal-sdk-into-laravel-4-laravel-5/link

				if($orderitem->item_id != Item::SHIPPING) {
					$item = new \PayPal\Api\Item;
					$item->setName($orderitem->item->title)->setCurrency('EUR')->setQuantity($orderitem->amount)->setSku($orderitem->item_id)->setPrice($orderitem->itemprice);
					$itemarray[] = $item;
					$subtotal += $orderitem->amount *$orderitem->itemprice;
				} else {
					$details->setShipping($orderitem->itemprice)->setTax(0);
				}

			}
			$details->setSubtotal($subtotal);

			$itemlist = new ItemList();
			$itemlist->setItems($itemarray);

			$transaction = new Transaction();
			$amount = new Amount();
			$amount->setCurrency("EUR")->setTotal($order->totalamount)->setDetails($details);
			$transaction->setAmount($amount)->setItemList($itemlist)->setDescription("Payment description")->setInvoiceNumber($order->order_number);
			$redirectUrls = new RedirectUrls();
			$redirectUrls->setReturnUrl(action('WelcomeController@paypalReturn', [self::PAYPAL_OK, $order->ordernumber])) ->setCancelUrl(action('WelcomeController@paypalReturn', [self::PAYPAL_PROBLEM, $order->ordernumber]));

			$payment = new Payment();
			$payment->setIntent("sale")->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));
			try {
				$payment->create($api_context);
			} catch (\PayPal\Exception\PayPalConnectionException $pce) {
				// Don't spit out errors or use "exit" like this in production code
				return view('problem', ["order"=>$order]);
			}

			$approvalUrl = $payment->getApprovalLink();
			return Redirect::to($approvalUrl);
		}


	}

	public function paypalReturn($result, $ordernumber, $paymentId = null, $token = null, $PayerID = null) {
		Log::info('Got a return URL call from Paypal I guess with result ' . $result . ' for order '. $ordernumber);


		// MarkO: HORRIBLE! BAD!
		// My htaccess works by adding sub urls as a query string to index.php. But the paypal code sends
		// an additional query string so my solution breaks. This is a horrible hack to rebuild that.

		$url =  $_SERVER['REQUEST_URI'];

		if(str_contains($url, "paymentId")) {
			$querystring = substr($url, strpos($url, "?") + 1);
			Log::info("In horrible hack code, parsing " . $querystring);
			parse_str($querystring);
			Log::info("In horrible hack code, with paymentID " . $paymentId);
		}
		// End of bad, bad hack


		$order = Order::where('ordernumber', "=", $ordernumber)->first();
		if($result == self::PAYPAL_OK) {
			$paypal_conf = \Config::get('paypal');
			$api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
			$api_context->setConfig($paypal_conf['settings']);

			if ($paymentId) {
				Log::info("Paypal send us an okay for execution of payment " . $paymentId);
			} else {
				Log::error("Expected a payment ID in URL but there was not any");
				return view('problem', ["order" => $order, "problemdescription" => 'Expected a payment ID in URL but there was not any']);
			}
			$payment = Payment::get($paymentId, $api_context);
			$execution = new PaymentExecution();
			$execution->setPayerId($PayerID );

			try {
				Log::info("Attempting to execute " . $paymentId . " for " . $PayerID );
				$result = $payment->execute($execution, $api_context);
				try {
					Log::info("Got a good result after execution " . $result);
					$payment = Payment::get($paymentId, $api_context);
					Log::info("Got a good payment detail after " . $payment);

					Mail::send('emails.paypalokay', ['order' => $order], function ($message) use ($order) {
						$message->from(self::$shopmail, 'LEAF Music');
						$message->subject('LEAF Music order number ' . $order->ordernumber . ' - paid');
						$message->to($order->email)->bcc(self::$shopmail)->replyTo(self::$shopmail);
					});

					Mail::send('emails.kaatmail', ['order' => $order], function ($message) use ($order) {
						$message->from(self::$shopmail, 'LEAF Music');
						$message->subject('LEAF Music order number ' . $order->ordernumber . ' - paid');
						$message->to($order->email);
					});
					$order->status = Order::STATUS_PAID;
					$order->save();
					Log::info("Updated order " . $order->id . " with an okay and paid for state");
				} catch (Exception $e) {
					Log::error('Payment result was not found for payment id ' . $paymentId);
					return view('problem', ["order" => $order, "problemdescription" => 'Payment result was not found']);
				}
			} catch (PayPalConnectionException $ex) {
				Log::error("Paypal error " . $ex->getCode());
				Log::error("Paypal full" . $ex->getData());
				return view('problem', ["order"=>$order, "problemdescription"=>'Payment execution failed for your payment ID']);
			} catch (Exception $e) {
				Log::error('Payment execution failed for ' . $paymentId);
				return view('problem', ["order"=>$order, "problemdescription"=>'Payment execution failed for your payment ID']);
			}
			return Redirect::action('WelcomeController@result', [$order->ordernumber]);
		} else {
			return view('problem', ["order"=>$order]);
		}
	}

	public function result($ordernumber) {
		$order = Order::where('ordernumber', '=', $ordernumber)->first();
		return view('thanks', ["order"=>$order]);
	}

	public function overview() {
		$orderarray = [];
		foreach(array_keys(Order::STATUS_TYPES) as $status) {
			$orderarray[$status]  = Order::where('status', '=',$status)->get();
		}
		return view('overview', ["ordertypes"=>$orderarray]);
	}

	public function acknowledgeSend($ordernumber) {
		$order = Order::where('ordernumber', '=', $ordernumber)->first();
		$order->status = Order::STATUS_SEND;
		$order->save();
		Mail::send('emails.sendokay', ['order' => $order], function ($message) use ($order) {
			$message->from(self::$shopmail, 'LEAF Music');
			$message->subject('Update on LEAF Music order number ' . $order->ordernumber);
            $message->to($order->email)->bcc(self::$shopmail)->replyTo(self::$shopmail);
		});
		return Redirect::action('WelcomeController@overview')->withErrors(['Order ' . $order->ordernumber . " is gemarkeerd als verzonden."]);
	}

	public function delete($ordernumber) {
		$order = Order::where('ordernumber', '=', $ordernumber)->first();
		$order->delete();
		return Redirect::action('WelcomeController@overview')->withErrors(['Order ' . $order->ordernumber . " is verwijderd."]);
	}

}
