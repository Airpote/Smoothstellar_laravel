<?php

namespace App\Http\Controllers;

use App\Deposit;

use App\Trx;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Auth;
use App\User;
use App\Gateway;
use App\Buymoney;
use App\Currency;
use App\Message;
use App\GeneralSettings;

use App\Coinmarket;
use App\Coinmarketpay;
use Session;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\Charge;
use App\Lib\coinPayments;
use App\Lib\BlockIo;
use App\Lib\CoinPaymentHosted;

class PaymentController extends Controller
{

    public function userDataUpdate($data)
    {

        $gnl = GeneralSettings::first();
        if ($data->status == 0) {
            $data['status'] = 1;
            $data->update();

            $user = User::find($data->user_id);
            $user['balance'] = $user->balance + $data->amount;


            $ref = User::where('refer', $user->id)->get();


            if($user->refer!=0)
            {
                $refer = User::find($user->refer);


                if($refer->level == 1)
                {
                    $commision = ($data->amount * $gnl->level_one)/100;
                }elseif ($refer->level == 2){
                    $commision = ($data->amount * $gnl->level_two)/100;
                }elseif ($refer->level == 3){
                    $commision = ($data->amount * $gnl->level_three)/100;
                }

                $refer['balance'] = round(($refer->balance + $commision),$gnl->decimal);
                $refer->update();

                $msg = $commision . ' '. $gnl->currency . ' Referral Commission  from '. $user->username ;
                send_email($refer->email, $refer->username, 'Referral Commission', $msg);
                send_sms($refer->phone, $msg);
            }

            $user->update();


            $txt = $data->amount . ' ' . $gnl->currency . ' Deposit Successful Via ' . $data->gateway->name;

            send_email($user->email, $user->username, 'Deposit Successful', $txt);

            send_sms($user->phone, $txt);

        }

    }

    public function depositConfirm(Request $request)
    {
        $gnl = GeneralSettings::first();
        $track = Session::get('Track');
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if (is_null($data)) {
            return redirect()->route('deposit')->with('danger', 'Invalid Deposit Request');
        }
        if ($data->status != 0) {
            return redirect()->route('deposit')->with('danger', 'Invalid Deposit Request');
        }

         if ($request->bank) {

              $this->validate($request,
            [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $data['image'] = uniqid().'.jpg';
            $request->image->move('uploads/payments',$data['image']);
            $data['code'] = $request->code ;
            $data['status'] = 2 ;
            $data->save();

               return redirect()->route('deposit')->with('success', 'Bank Transfer Deposit Request Received.Please wait while we vlidate your payment');
        }

        $gatewayData = Gateway::where('id', $data->gateway_id)->first();


        if ($data->gateway_id == 101) {
            $page_title = $gatewayData->name;
            $paypal['amount'] = $data->usd;
            $paypal['sendto'] = $gatewayData->val1;
            $paypal['track'] = $track;
            return view('user.payment.paypal', compact('paypal', 'gnl', 'page_title'));

        } elseif ($data->gateway_id == 102) {
            $page_title = $gatewayData->name;
            $perfect['amount'] = $data->usd;
            $perfect['value1'] = $gatewayData->val1;
            $perfect['value2'] = $gatewayData->val2;
            $perfect['track'] = $track;
            return view('user.payment.perfect', compact('perfect', 'gnl', 'page_title'));
        } elseif ($data->gateway_id == 103) {
            $page_title = $gatewayData->name;
            return view('user.payment.stripe', compact('track', 'page_title'));
        } elseif ($data->gateway_id == 104) {
            $page_title = $gatewayData->name;
            return view('user.payment.skrill', compact('page_title', 'gnl', 'gatewayData', 'data'));
        }
        elseif ($data->gateway_id == 105)
        {
            $page = $gatewayData->name;
            $post_params = [
                'MID' => $gatewayData->val1,
                'WEBSITE' => $gatewayData->val3,
                'CHANNEL_ID' =>  $gatewayData->val5,
                'INDUSTRY_TYPE_ID' => $gatewayData->val4,
                'ORDER_ID' => $data->id,
                'TXN_AMOUNT' => $data->usd,
                'CUST_ID' => $data->user->id,
                'CALLBACK_URL' => route('ipn.paytm')
            ];
            $post_params["CHECKSUMHASH"] = getChecksumFromArray($post_params, $gatewayData->val2);
            $form_action = $gatewayData->val6 . "?orderid=" . $data->id;
            return view('user.payment.paytm', compact('page','post_params', 'form_action', 'gnl'));
        }
        elseif ($data->gateway_id == 106)
        {
            $page = $gatewayData->name;
            $payeer_url = 'https://payeer.com/merchant';

            $m_shop	= $gatewayData->val1;
            $m_orderid = $data->id;
            $m_amount = $data->usd;
            $m_curr	= 'USD';
            $m_desc = base64_encode('Buy ICO');
            $m_key = $gatewayData->val2;

            $arHash = [$m_shop, $m_orderid, $m_amount, $m_curr, $m_desc, $m_key];

            $sign = strtoupper(hash('sha256', implode(":", $arHash)));

            return view('user.payment.payeer',compact('page', 'gnl','payeer_url','m_shop','m_orderid','m_amount','m_curr','m_desc','sign'));
        }

        elseif ($data->gateway_id == 501) {
            $page_title = $gatewayData->name;


             $baseUrl = "https://api.alternative.me";
			$endpoint = "/v2/ticker/";
			$httpVerb = "GET";
			$contentType = "application/json"; //e.g charset=utf-8
			$headers = array (
				"Content-Type: $contentType",

        );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $rate = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);
        	$coinrate  = $rate['data']['1'];
         	$amount = $coinrate['quotes']['USD'];
         	$btcrate = $amount['price'];



            $usd = $data->usd;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);


            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $blockchain_root = "https://blockchain.info/";
                $blockchain_receive_root = "https://api.blockchain.info/";
                $mysite_root = url('/');
                $secret = "ABIR";
                $my_xpub = $gatewayData->val2;
                $my_api_key = $gatewayData->val1;

                $invoice_id = $track;
                $callback_url = $mysite_root . "/ipnbtc?invoice_id=" . $invoice_id . "&secret=" . $secret;

                $resp = @file_get_contents($blockchain_receive_root . "v2/receive?key=" . $my_api_key . "&callback=" . urlencode($callback_url) . "&xpub=" . $my_xpub);

                if (!$resp) {
                    return redirect()->route('deposit')->with('alert', 'BLOCKCHAIN API HAVING ISSUE. PLEASE TRY LATER');
                }

                $response = json_decode($resp);
                $sendto = $response->address;

                $data['btc_wallet'] = $sendto;
                $data['btc_amo'] = $btc;
                $data->update();
            }
            $DepositData = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

            $bitcoin['amount'] = $DepositData->btc_amo;
            $bitcoin['sendto'] = $DepositData->btc_wallet;

            $var = "bitcoin:$DepositData->btc_wallet?amount=$DepositData->btc_amo";
            $bitcoin['code'] = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$var&choe=UTF-8\" title='' style='width:300px;' />";
            return view('user.payment.blockchain', compact('bitcoin', 'page_title'));
        } elseif ($data->gateway_id == 502) {
            $method = Gateway::find(502);
            $apiKey = $method->val1;
            $version = 2;
            $pin = $method->val2;
            $block_io = new BlockIo($apiKey, $pin, $version);
            $btcdata = $block_io->get_current_price(array('price_base' => 'USD'));
            if ($btcdata->status != 'success') {
                return back()->with('danger', 'Failed to Process');
            }
            $btcrate = $btcdata->data->prices[0]->price;

            $usd = $data->usd;
            $bcoin = round($usd / $btcrate, 8);

            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $ad = $block_io->get_new_address();

                if ($ad->status == 'success') {
                    $blockad = $ad->data;
                    $wallet = $blockad->address;
                    $data['btc_wallet'] = $wallet;
                    $data['btc_amo'] = $bcoin;
                    $data->update();
                } else {
                    return back()->with('danger', 'Failed to Process');
                }
            }

            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit Via " .$gatewayData->name;
            $varb = "bitcoin:" . $wallet . "?amount=" . $bcoin;
            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8\" title='' style='width:300px;' />";

            return view('user.payment.blockbtc', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 503) {
            $method = Gateway::find(503);
            $apiKey = $method->val1;
            $version = 2;
            $pin = $method->val2;
            $block_io = new BlockIo($apiKey, $pin, $version);
            $btcdata = $block_io->get_current_price(array('price_base' => 'USD'));
            if ($btcdata->status != 'success') {
                return back()->with('danger', 'Failed to Process');
            }
            $btcrate = $btcdata->data->prices[0]->price;

            $usd = $data->usd;
            $bcoin = round($usd / $btcrate, 8);


            if ($data->btc_wallet == "") {
                $ad = $block_io->get_new_address();

                if ($ad->status == 'success') {
                    $blockad = $ad->data;
                    $wallet = $blockad->address;
                    $data['btc_wallet'] = $wallet;
                    $data['btc_amo'] = $bcoin;
                    $data->update();
                } else {
                    return back()->with('danger', 'Failed to Process');
                }
            }

            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit Via ".$method->name;
            $varb = "litecoin:" . $wallet;
            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8\" title='' style='width:300px;' />";

            return view('user.payment.blocklite', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 504) {
            $method = Gateway::find(504);
            $apiKey = $method->val1;
            $version = 2;
            $pin = $method->val2;
            $block_io = new BlockIo($apiKey, $pin, $version);

            $dogeprice = file_get_contents("https://api.coinmarketcap.com/v1/ticker/dogecoin");
            $dresult = json_decode($dogeprice);
            $doge_usd = $dresult[0]->price_usd;

            $usd = $data->usd;
            $bcoin = round($usd / $doge_usd, 8);

            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $ad = $block_io->get_new_address();

                if ($ad->status == 'success') {
                    $blockad = $ad->data;
                    $wallet = $blockad->address;
                    $data['btc_wallet'] = $wallet;
                    $data['btc_amo'] = $bcoin;
                    $data->update();
                } else {
                    return back()->with('danger', 'Failed to Process');
                }
            }

            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit via ".$method->name;
            $varb = $wallet;

            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8\" title='' style='width:300px;' />";

            return view('user.payment.blockdog', compact('bcoin', 'wallet', 'qrurl', 'page_title'));
        } elseif ($data->gateway_id == 505) {
            $method = Gateway::find(505);
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $cps = new CoinPaymentHosted();
                $cps->Setup($method->val2, $method->val1);
                $callbackUrl = route('ipn.coinPay.btc');

                $req = array(
                    'amount' => $data->usd,
                    'currency1' => 'USD',
                    'currency2' => 'BTC',
                    'custom' => $data->trx,
                    'ipn_url' => $callbackUrl,
                );

                $result = $cps->CreateTransaction($req);
                if ($result['error'] == 'ok') {

                    $bcoin = sprintf('%.08f', $result['result']['amount']);
                    $sendadd = $result['result']['address'];

                    $data['btc_amo'] = $bcoin;
                    $data['btc_wallet'] = $sendadd;
                    $data->update();

                } else {
                    return back()->with('danger', 'Failed to Process');
                }

            }
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit via  ".$method->name;


            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=bitcoin:$wallet&choe=UTF-8\" title='' style='width:300px;' />";
            return view('user.payment.coinpaybtc', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 506) {
            $method = Gateway::find(506);
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $cps = new CoinPaymentHosted();
                $cps->Setup($method->val2, $method->val1);
                $callbackUrl = route('ipn.coinPay.eth');
                $req = array(
                    'amount' => $data->usd,
                    'currency1' => 'USD',
                    'currency2' => 'ETH',
                    'custom' => $data->trx,
                    'ipn_url' => $callbackUrl,
                );

                $result = $cps->CreateTransaction($req);
                if ($result['error'] == 'ok') {
                    $bcoin = sprintf('%.08f', $result['result']['amount']);
                    $sendadd = $result['result']['address'];

                    $data['btc_amo'] = $bcoin;
                    $data['btc_wallet'] = $sendadd;
                    $data->update();
                } else {
                    return back()->with('alert', 'Failed to Process');
                }
            }
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title =  "Deposit via  ".$method->name;

            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8\" title='' style='width:300px;' />";

            return view('user.payment.coinpayeth', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 507) {
            $method = Gateway::find(507);
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $cps = new CoinPaymentHosted();
                $cps->Setup($method->val2, $method->val1);
                $callbackUrl = route('ipn.coinPay.bch');

                $req = array(
                    'amount' => $data->usd,
                    'currency1' => 'USD',
                    'currency2' => 'BCH',
                    'custom' => $data->trx,
                    'ipn_url' => $callbackUrl,
                );
                $result = $cps->CreateTransaction($req);
                if ($result['error'] == 'ok') {
                    $bcoin = sprintf('%.08f', $result['result']['amount']);
                    $sendadd = $result['result']['address'];

                    $data['btc_amo'] = $bcoin;
                    $data['btc_wallet'] = $sendadd;
                    $data->update();
                } else {
                    return back()->with('danger', 'Failed to Process');
                }
            }
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit via  ".$method->name;
            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8\" title='' style='width:300px;' />";

            return view('user.payment.coinpaybch', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 508) {
            $method = Gateway::find(508);
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $cps = new CoinPaymentHosted();
                $cps->Setup($method->val2, $method->val1);
                $callbackUrl = route('ipn.coinPay.dash');

                $req = array(
                    'amount' => $data->usd,
                    'currency1' => 'USD',
                    'currency2' => 'DASH',
                    'custom' => $data->trx,
                    'ipn_url' => $callbackUrl,
                );
                $result = $cps->CreateTransaction($req);

                if ($result['error'] == 'ok') {
                    $bcoin = sprintf('%.08f', $result['result']['amount']);
                    $sendadd = $result['result']['address'];

                    $data['btc_amo'] = $bcoin;
                    $data['btc_wallet'] = $sendadd;
                    $data->update();
                } else {
                    return back()->with('danger', 'Failed to Process');
                }

            }
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit via ". $method->name;

            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8\" title='' style='width:300px;' />";

            return view('user.payment.coinpaydash', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 509) {

            $method = Gateway::find(509);
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {

                $cps = new CoinPaymentHosted();
                $cps->Setup($method->val2, $method->val1);
                $callbackUrl = route('ipn.coinPay.doge');

                $req = array(
                    'amount' => $data->usd,
                    'currency1' => 'USD',
                    'currency2' => 'DOGE',
                    'custom' => $data->trx,
                    'ipn_url' => $callbackUrl,
                );

                $result = $cps->CreateTransaction($req);
                if ($result['error'] == 'ok') {
                    $bcoin = sprintf('%.08f', $result['result']['amount']);
                    $sendadd = $result['result']['address'];

                    $data['btc_amo'] = $bcoin;
                    $data['btc_wallet'] = $sendadd;
                    $data->update();
                } else {
                    return back()->with('danger', 'Failed to Process');
                }

            }
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit via ".$method->name;

            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8\" title='' style='width:300px;' />";
            return view('user.payment.coinpaydoge', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 510) {

            $method = Gateway::find(510);
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {

                $cps = new CoinPaymentHosted();
                $cps->Setup($method->val2, $method->val1);
                $callbackUrl = route('ipn.coinPay.ltc');

                $req = array(
                    'amount' => $data->usd,
                    'currency1' => 'USD',
                    'currency2' => 'LTC',
                    'custom' => $data->trx,
                    'ipn_url' => $callbackUrl,
                );

                $result = $cps->CreateTransaction($req);
                if ($result['error'] == 'ok') {

                    $bcoin = sprintf('%.08f', $result['result']['amount']);
                    $sendadd = $result['result']['address'];

                    $data['btc_amo'] = $bcoin;
                    $data['btc_wallet'] = $sendadd;
                    $data->update();

                } else {
                    return back()->with('danger', 'Failed to Process');
                }
            }
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $page_title = "Deposit via " .$method->name;

            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8\" title='' style='width:300px;' />";
            return view('user.payment.coinpayltc', compact('bcoin', 'wallet', 'qrurl', 'page_title'));

        } elseif ($data->gateway_id == 512) {

            $method = Gateway::find(512);

            $usd = $data->usd;

            \CoinGate\CoinGate::config(array(
                'environment'               => 'sandbox', // sandbox OR live
                'auth_token'                => $method->val1
            ));


            $post_params = array(
                'order_id'          => $data->trx,
                'price_amount'      => $usd,
                'price_currency'    => 'USD',
                'receive_currency'  => 'USD',
                'callback_url'      => route('ipn.coingate'),
                'cancel_url'        => route('deposit'),
                'success_url'       => route('deposit'),
                'title'             => 'Deposit' . $data->trx,
                'description'       => 'Deposit'
            );

            $order = \CoinGate\Merchant\Order::create($post_params);

            if ($order)
            {

                return redirect($order->payment_url);
                exit();

            }
            else
            {
                return redirect()->route('deposit')->with('danger','Unexpected Error! Please Try Again');
                exit();
            }


        } elseif ($data->gateway_id == 513) {
            $amont = $data->amount;
            $usd = $data->usd;
            $method = Gateway::find(513);

           $user = Auth::user();
             $publicKey=$method->val2;
            $privateKey=$method->val3;
            $cps = new coinPayments();
            $cps->Setup($privateKey, $publicKey);
            $req = array(
                'amount' => $usd,
                'currency1' => 'USD',
                'currency2' => $request->currency,
                'buyer_email' => $user->email,
                'buyer_name' => $user->username,
                'item_name' => 'Instant Deposit',
                'custom' => $request->nothing,
                'item_number' => $request->code.$user->id,
                'address' => '',
                'ipn_url' => route('userDepositCrypto'),
            );
             $result = $cps->CreateTransaction($req);
             if ($result['error'] == 'ok') {



            return redirect($result['result']['status_url']);

        } else {

            print 'Error: '.$result['error']."\n";
        }


        }


    }


    public function cryptoStatus(Request $request)
    {
         $gateway = Gateway::find(513);

           $user = Auth::user();
             $cp_merchant_id = $gateway->val1;
             $cp_ipn_secret = $gateway->val3;

        $cp_debug_email = $settings->contact_email;
        function errorAndDie($error_msg) {
            global $cp_debug_email;
            if (!empty($cp_debug_email)) {
                $report = 'Error: '.$error_msg."\n\n";
                $report .= "POST Data\n\n";
                foreach ($_POST as $k => $v) {
                    $report .= "|$k| = |$v|\n";
                }
                mail($cp_debug_email, 'CoinPayments IPN Error', $report);
            }
            die('IPN Error: '.$error_msg);
        }
        if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
            errorAndDie('IPN Mode is not HMAC');
        }
        if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            errorAndDie('No HMAC signature sent.');
        }
        $request = file_get_contents('php://input');
        if ($request === FALSE || empty($request)) {
            errorAndDie('Error reading POST data');
        }
        if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
            errorAndDie('No or incorrect Merchant ID passed');
        }
        $hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));
        if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
            errorAndDie('HMAC signature does not match');
        }
        $txn_id = $_POST['txn_id'];
        $item_name = $_POST['item_name'];
        $item_number = $_POST['item_number'];
        $amount1 = floatval($_POST['amount1']);
        $amount2 = floatval($_POST['amount2']);
        $currency1 = $_POST['currency1'];
        $currency2 = $_POST['currency2'];
        $status = intval($_POST['status']);
        $status_text = $_POST['status_text'];
        $crypto = Crypto::whereTransaction_id($item_number)->first();
        $user = $crypto->user;
        $gateway = $crypto->gateway;
        $order_currency = $crypto->currency1;
        $order_total = $crypto->amount;
        if ($currency1 != $order_currency) {
            errorAndDie('Original currency mismatch!');
        }
        if ($amount1 < $order_total) {
            errorAndDie('Amount is less than order total!');
        }
        if ($status >= 100 || $status == 2) {

            if ($crypto->payment == 0 ){

                $crypto->status = $status;
                $crypto->payment = 1;
                $crypto->save();

                $deposit = Deposit::create([
                    'transaction_id' => $item_number,
                    'user_id' => $user->id,
                    'gateway_name' => $gateway->name,
                    'amount' => $request->amount,
                    'charge' => $crypto->charge,
                    'net_amount' => $crypto->charge,
                    'status' => 1,
                    'details' => 'Crypto Instant Deposit',
                ]);
                $user->profile->deposit_balance = $user->profile->deposit_balance + $crypto->amount;
                $user->profile->save();
            }
        } else if ($status < 0) {
            $crypto->status = $status;
            $crypto->save();
        } else {
            $crypto->status = $status;
            $crypto->save();
        }
        die('IPN OK');

    }

    //IPN Functions //////

    public function ipnpaypal()
    {

        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        $paypalURL = "https://ipnpb.paypal.com/cgi-bin/webscr?";
        
        $callUrl = $paypalURL . $req;
        $verify = file_get_contents($callUrl);
        if ($verify == "VERIFIED") {
            //PAYPAL VERIFIED THE PAYMENT
            $receiver_email = $_POST['receiver_email'];
            $mc_currency = $_POST['mc_currency'];
            $mc_gross = $_POST['mc_gross'];
            $track = $_POST['custom'];

            //GRAB DATA FROM DATABASE!!
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $gatewayData = Gateway::find(101);
            $amount = $data->usd;

            if ($receiver_email == $gatewayData->val1 && $mc_currency == "USD" && $mc_gross == $amount && $data->status == '0') {
                //Update User Data
                $this->userDataUpdate($data);
            }
        }

    }

    public function ipnperfect()
    {
        $gatewayData = Gateway::find(102);
        $passphrase = strtoupper(md5($gatewayData->val2));

        define('ALTERNATE_PHRASE_HASH', $passphrase);
        define('PATH_TO_LOG', '/somewhere/out/of/document_root/');
        $string =
            $_POST['PAYMENT_ID'] . ':' . $_POST['PAYEE_ACCOUNT'] . ':' .
            $_POST['PAYMENT_AMOUNT'] . ':' . $_POST['PAYMENT_UNITS'] . ':' .
            $_POST['PAYMENT_BATCH_NUM'] . ':' .
            $_POST['PAYER_ACCOUNT'] . ':' . ALTERNATE_PHRASE_HASH . ':' .
            $_POST['TIMESTAMPGMT'];

        $hash = strtoupper(md5($string));
        $hash2 = $_POST['V2_HASH'];

        if ($hash == $hash2) {

            $amo = $_POST['PAYMENT_AMOUNT'];
            $unit = $_POST['PAYMENT_UNITS'];
            $track = $_POST['PAYMENT_ID'];

            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            $gnl = GeneralSettings::first();

            if ($_POST['PAYEE_ACCOUNT'] == $gatewayData->val1 && $unit == "USD" && $amo == $data->usd && $data->status == '0') {
                //Update User Data
                $this->userDataUpdate($data);
            }
        }

    }

    public function ipnstripe(Request $request)
    {
        $track = Session::get('Track');
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        $this->validate($request,
            [
                'cardNumber' => 'required',
                'cardExpiry' => 'required',
                'cardCVC' => 'required',
            ]);

        $cc = $request->cardNumber;
        $exp = $request->cardExpiry;
        $cvc = $request->cardCVC;

        $exp = $pieces = explode("/", $_POST['cardExpiry']);
        $emo = trim($exp[0]);
        $eyr = trim($exp[1]);
        $cnts = round($data->usd, 2) * 100;

        $gatewayData = Gateway::find(103);
        $gnl = GeneralSettings::first();

        Stripe::setApiKey($gatewayData->val1);

        try {
            $token = Token::create(array(
                "card" => array(
                    "number" => "$cc",
                    "exp_month" => $emo,
                    "exp_year" => $eyr,
                    "cvc" => "$cvc"
                )
            ));

            try {
                $charge = Charge::create(array(
                    'card' => $token['id'],
                    'currency' => 'USD',
                    'amount' => $cnts,
                    'description' => 'item',
                ));

                if ($charge['status'] == 'succeeded') {
                    //Update User Data
                    $this->userDataUpdate($data);
                    return redirect()->route('deposit')->with('success', 'Deposit Successfull ');
                }
            } catch (Exception $e) {
                return redirect()->route('deposit')->with('danger', $e->getMessage());
            }

        } catch (Exception $e) {
            return redirect()->route('deposit')->with('danger', $e->getMessage());
        }

    }

      public function buystripe(Request $request)
    {
        $track = Session::get('Track');
        $auth = Auth::user();
        $data = Trx::whereUser_id($auth->id)->where('status', 0)->where('trx', $track)->orderBy('id', 'DESC')->first();
        $currency = Currency::whereId($data->currency_id)->first();
        $basic = GeneralSettings::first();

        $this->validate($request,
            [
                'cardNumber' => 'required',
                'cardExpiry' => 'required',
                'cardCVC' => 'required',
            ]);

        $cc = $request->cardNumber;
        $exp = $request->cardExpiry;
        $cvc = $request->cardCVC;

        $exp = $pieces = explode("/", $_POST['cardExpiry']);
        $emo = trim($exp[0]);
        $eyr = trim($exp[1]);
        $cnts = round($data->main_amo / $basic->rate, 2) * 100;

        $gatewayData = Gateway::find(103);
        $gnl = GeneralSettings::first();

        Stripe::setApiKey($gatewayData->val1);

        try {
            $token = Token::create(array(
                "card" => array(
                    "number" => "$cc",
                    "exp_month" => $emo,
                    "exp_year" => $eyr,
                    "cvc" => "$cvc"
                )
            ));

            try {
                $charge = Charge::create(array(
                    'card' => $token['id'],
                    'currency' => 'USD',
                    'amount' => $cnts,
                    'description' => 'item',
                ));

                if ($charge['status'] == 'succeeded') {
                    //Update User Data
                    $data->status = 1;
                    $data->save();
                    $amount = $data->main_amo;



                 Message::create([
                    'user_id' => $auth->id,
                    'title' => 'Coin Purchased',
                    'details' => 'Your cryptocurrency purchase of USD '.$amount.' was successful using Stripe Payment Gateway. Please wait while we verify your purchase. Your wallet will be credited once payment is confirmed by our server, Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);

                    return redirect()->route('home')->with('success', 'Payment Successfull ');
                }
            } catch (Exception $e) {
                return redirect()->route('hone')->with('danger', $e->getMessage());
            }

        } catch (Exception $e) {
            return redirect()->route('home')->with('danger', $e->getMessage());
        }

    }



   public function buypaystack(Request $request)
    {
        $auth = Auth::user();
        $data = Trx::whereUser_id($auth->id)->where('status', 0)->where('trx', $request->trx)->first();
        $currency = Currency::whereId($data->currency_id)->first();
         $basic = GeneralSettings::first();
         $data->status = 1;
         $data->save();

        $amount = $data->main_amo;


          Message::create([
                    'user_id' => $auth->id,
                    'title' => 'Coin Purchased',
                    'details' => 'Your cryptocurrency purchase of '.$basic->currency.''.$amount.' was successful using Paystack Payment Gateway. Please wait while we verify your purchase. Your wallet will be credited once payment is confirmed by our server, Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);

           Message::create([
                    'user_id' => $data->seller,
                    'title' => 'Coin Sold on Market Place',
                    'details' => 'You just sold '.$currency->name.' valued at '.$data->amount.'$ with transaction number '.$data->trx.' on your store with market code '.$data->marketcode.' Please treat as required. , Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);

        $txt = $data->amount . ' ' . $currency->symbol . ' Buy Amount  ';
        send_email($auth->email, $auth->username, 'Buy Amount', $txt);
        return redirect()->route('home')->with("success", "  Your coin purchase was successful");

    }

   public function marketpaystack(Request $request)
    {
        $auth = Auth::user();
        $data = Coinmarketpay::whereBuyer($auth->id)->where('status', 0)->where('trx', $request->trx)->first();
         $currency = Currency::whereId($data->coin)->first();
         $basic = GeneralSettings::first();
         $data->status = 1;
         $data->payment_id = $request->payid;
         $data->save();

        $amount = $data->amount;


          Message::create([
                    'user_id' => $auth->id,
                    'title' => 'Coin Purchased',
                    'details' => 'Your cryptocurrency purchase of '.$basic->currency.''.$amount.' was successful using Paystack Payment Gateway. Your transaction number is '.$data->trx.', Please wait while the seller send you your coin, Your fund will be refunded if seller does not reply promptly. Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);

         Message::create([
                    'user_id' => $data->seller,
                    'title' => 'Coin Sold on Market Place',
                    'details' => 'You just sold '.$currency->name.' valued at '.$data->amount.'$ with transaction number '.$data->trx.' on your store with market code '.$data->marketcode.' Please treat as required. , Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);


        $txt = $data->amount . ' ' . $currency->symbol . ' Buy Amount  ';
        send_email($auth->email, $auth->username, 'Buy Amount', $txt);
        return redirect()->route('home')->with("success", "  Your coin purchase from the market place was successful");

    }


   public function marketrave(Request $request)
    {
        $auth = Auth::user();
        $data = Coinmarketpay::whereBuyer($auth->id)->where('status', 0)->where('trx', $request->trx)->first();
         $currency = Currency::whereId($data->coin)->first();
         $basic = GeneralSettings::first();
         $data->status = 1;
         $data->payment_id = $request->payid;
         $data->save();

               $amount = $data->amount;


          Message::create([
                    'user_id' => $auth->id,
                    'title' => 'Coin Purchased',
                    'details' => 'Your cryptocurrency purchase of '.$basic->currency.''.$amount.' was successful using Rave Payment Gateway. Your transaction number is '.$data->trx.', Please wait while the seller send you your coin, Your fund will be refunded if seller does not reply promptly. Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);
        $txt = $data->enter_amount . ' ' . $currency->symbol . ' Buy Amount  ';
        send_email($auth->email, $auth->username, 'Buy Amount', $txt);
        return redirect()->route('home')->with("success", "  Your coin purchase from the market place was successful");

    }


   public function buyrave(Request $request)
    {
        $auth = Auth::user();
        $data = Trx::whereUser_id($auth->id)->where('status', 0)->where('trx', $request->trx)->first();
        $currency = Currency::whereId($data->currency_id)->first();
         $basic = GeneralSettings::first();
         $data->status = 1;
         $data->save();

        $amount = $data->main_amo;



          Message::create([
                    'user_id' => $auth->id,
                    'title' => 'Coin Purchased',
                    'details' => 'Your cryptocurrency purchase of '.$basic->currency.''.$amount.' was successful using Flutterwave Payment Gateway. Please wait while we verify your purchase. Your wallet will be credited once payment is confirmed by our server, Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);
        $txt = $data->enter_amount . ' ' . $currency->symbol . ' Buy Amount  ';
        send_email($auth->email, $auth->username, 'Buy Amount', $txt);
        return redirect()->route('home')->with("success", "  Your coin purchase was successful");

    }

 public function buywallet(Request $request)
    {
        $auth = Auth::user();
        $data = Trx::whereUser_id($auth->id)->where('status', 0)->where('trx', $request->trx)->first();
        $currency = Currency::whereId($data->currency_id)->first();
        $basic = GeneralSettings::first();
        $amount = $data->main_amo;
        
        if($amount > $auth->balance){
        return back()->with("alert", "You dont have enough fund in your deposit wallet.Please deposit more fund or try using another payment gateway");
        }
        
        $auth->balance = $auth->balance - $data->main_amo;
        $auth->save();
        $data->status = 1;
        $data->save();





          Message::create([
                    'user_id' => $auth->id,
                    'title' => 'Coin Purchased',
                    'details' => 'Your cryptocurrency purchase of '.$basic->currency.''.$amount.' was successful using your deposit wallet balnce. Please wait while we verify your purchase. Your wallet will be credited once payment is confirmed by our server, Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);
        $txt = $data->enter_amount . ' ' . $currency->symbol . ' Buy Amount  ';
        send_email($auth->email, $auth->username, 'Buy Amount', $txt);
        return redirect()->route('home')->with("success", "  Your coin purchase was successful");

    }


   public function buybank(Request $request)
    {
        $auth = Auth::user();
        $data = BuyMoney::whereUser_id($auth->id)->where('status', 0)->where('trx', $request->trx)->first();
        $currency = Currency::whereId($data->currency_id)->first();
         $basic = GeneralSettings::first();
         $data->status = 1;
         $data->name = $request->trxx;
          if($request->hasFile('image'))
            {
             $this->validate($request,
            [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ]);

                $data['image'] = uniqid().'.jpg';
                $request->image->move('uploads/payments',$data['image']);
            }

         $data->save();



        $amount = $data->enter_amount - 0;


        Trx::create([
        'user_id' => $auth->id,
        'amount' => $data->enter_amount,
        'main_amo' => round($auth->balance, $basic->decimal),
        'charge' => $data->buy_charge,
        'type' => '-',
        'action' => 'Purchase',
        'title' => ' Bought ' . $data->amount . ' ' . $currency->symbol,
         'trx' => $data->trx
          ]);

          Message::create([
                    'user_id' => $auth->id,
                    'title' => 'Coin Purchased',
                    'details' => 'Your cryptocurrency purchase of '.$basic->currency.''.$amount.' was successful using Bank Transfer. Please wait while we verify your purchase. Your wallet will be credited once payment is confirmed by our bank, Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);
        $txt = $data->enter_amount . ' ' . $currency->symbol . ' Buy Amount  ';
        send_email($auth->email, $auth->username, 'Buy Amount', $txt);
        return redirect()->route('home')->with("success", "  Your coin purchase was successful");

    }



    public function skrillIPN()
    {
		 $track = Session::get('Track');
        $skrill = Gateway::find(104);
        $concatFields = $_POST['merchant_id']
            . $_POST['transaction_id']
            . strtoupper(md5($skrill->val2))
            . $_POST['mb_amount']
            . $_POST['mb_currency']
            . $_POST['status'];

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $gnl = GeneralSettings::first();

        if (strtoupper(md5($concatFields)) == $_POST['md5sig'] && $_POST['status'] == 2 && $_POST['pay_to_email'] == $skrill->val1 && $data->status = '0') {
            //Update User Data
            $this->userDataUpdate($data);

        }
    }

    public function ipnPayTm(Request $request)
    {
        $gateway = Gateway::find(105);

        $paytm_merchant_key = $gateway->val2;
        $paytm_merchant_id = $gateway->val1;
        $transaction_status_url = $gateway->val7;

        if(verifychecksum_e($_POST, $paytm_merchant_key, $_POST['CHECKSUMHASH']) === "TRUE") {

            if($_POST['RESPCODE'] == "01"){
                // Create an array having all required parameters for status query.
                $requestParamList = array("MID" => $paytm_merchant_id, "ORDERID" => $_POST['ORDERID']);
                // $_POST['ORDERID'] = substr($_POST['ORDERID'], strpos($_POST['ORDERID'], "-") + 1); // just for testing
                $StatusCheckSum = getChecksumFromArray($requestParamList, $paytm_merchant_key);
                $requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
                $responseParamList = callNewAPI($transaction_status_url, $requestParamList);
                if($responseParamList['STATUS'] == 'TXN_SUCCESS' && $responseParamList['TXNAMOUNT'] == $_POST['TXNAMOUNT']) {
                    $ddd = Deposit::where('trx',$_POST['ORDERID'])->orderBy('id', 'DESC')->first();
                    $this->userDataUpdate($ddd);
                    $t = 'success';
                    $m = 'Transaction has been successful';
                } else  {
                    $t = 'alert';
                    $m = 'It seems some issue in server to server communication. Kindly connect with administrator';
                }
            } else {
                $t = 'alert';
                $m = $_POST['RESPMSG'];
            }
        } else {
            $t = 'alert';
            $m = "Security error!";
        }
        return redirect()->route('deposit')->with($t, $m);
    }

    public function ipnPayEer(Request $request)
    {

        if (isset($_GET['payeer']) && $_GET['payeer'] == 'result')
        {
            if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"]))
            {
                $err = false;
                $message = '';

                $gateway = Gateway::find(106);

                $sign_hash = strtoupper(hash('sha256', implode(":", array(
                    $_POST['m_operation_id'],
                    $_POST['m_operation_ps'],
                    $_POST['m_operation_date'],
                    $_POST['m_operation_pay_date'],
                    $_POST['m_shop'],
                    $_POST['m_orderid'],
                    $_POST['m_amount'],
                    $_POST['m_curr'],
                    $_POST['m_desc'],
                    $_POST['m_status'],
                    $gateway->val2
                ))));

                if ($_POST["m_sign"] != $sign_hash)
                {
                    $message .= " - do not match the digital signature\n";
                    $err = true;
                }

                if (!$err)
                {

                    $ddd = Deposit::find($_POST['m_orderid']);

                    $order_curr = 'USD';
                    $order_amount = round($ddd->usd, 2);

                    if ($_POST['m_amount'] != $order_amount)
                    {
                        $message .= " - wrong amount\n";
                        $err = true;
                    }

                    if ($_POST['m_curr'] != $order_curr)
                    {
                        $message .= " - wrong currency\n";
                        $err = true;
                    }

                    if (!$err)
                    {
                        switch ($_POST['m_status'])
                        {
                            case 'success':

                                $this->userDataUpdate($ddd);
                                $message = 'Sell Successfully Completed';
                                $err = false;

                                break;

                            default:
                                $message .= " - the payment status is not success\n";
                                $err = true;
                                break;
                        }
                    }
                }

                if ($err)
                {
                    return redirect()->route('deposit')->with('success', $message);
                }
                else
                {
                    return redirect()->route('deposit')->with('success', $message);
                }
            }
        }

    }

    public function purchaseVogue($trx, $type)
    {

        if ($type == 'error') redirect()->route('home')->with('alert', 'Transaction Failed, Ref: ' . $trx);
        return redirect()->route('home')->with('success', 'Transaction was successful, Ref: ' . $trx);

    }


      public function cardpay($id)
    {


        $ddd = Deposit::where('trx', $id)->first();
        $gateway = Gateway::find($ddd->gateway_id);

        $this->userDataUpdate($ddd);
        return redirect()->route('deposit')->with('success', 'Payment Successful');
        }


    public function ipnVoguePay(Request $request)
    {

        $request->validate([
            'transaction_id' => 'required'
        ]);

        $trx = $request->transaction_id;

        $req_url = "https://voguepay.com/?v_transaction_id=$trx&type=json";
        $data = file_get_contents($req_url);
        $data = json_decode($data);

        $merchant_id = $data->merchant_id;
        $total_paid = $data->total;
        $custom = $data->merchant_ref;
        $status = $data->status;
        $vogue = Gateway::find(108);

        if($status == "Approved" && $merchant_id == $vogue->val1){

            $ddd = Deposit::where('trx' , $custom)->first();
            $totalamo = $ddd->usd;

            if($totalamo == $total_paid)
            {
                $this->userDataUpdate($ddd);
            }
        }

    }



    public function ipnBchain()
    {
        $gatewayData = Gateway::find(501);

        $track = $_GET['invoice_id'];
        $secret = $_GET['secret'];
        $address = $_GET['address'];
        $value = $_GET['value'];
        $confirmations = $_GET['confirmations'];
        $value_in_btc = $_GET['value'] / 100000000;

        $trx_hash = $_GET['transaction_hash'];

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($data->status == 0) {
            if ($data->btc_amo == $value_in_btc && $data->btc_wallet == $address && $secret == "ABIR" && $confirmations > 2) {
                //Update User Data
                $this->userDataUpdate($data);
            }
        }

    }

    public function blockIpnBtc(Request $request)
    {

        $DepositData = Deposit::where('status', 0)->where('gateway_id', 502)->where('try', '<=', 100)->get();

        $method = Gateway::find(502);
        $apiKey = $method->val1;
        $version = 2;
        $pin = $method->val2;
        $block_io = new BlockIo($apiKey, $pin, $version);


        foreach ($DepositData as $data) {
            $balance = $block_io->get_address_balance(array('addresses' => $data->btc_wallet));
            $bal = $balance->data->available_balance;

            if ($bal > 0 && $bal >= $data->btc_amo) {
                //Update User Data
                $this->userDataUpdate($data);
            }
            $data['try'] = $data->try + 1;
            $data->update();
        }
    }

    public function blockIpnLite(Request $request)
    {

        $DepositData = Deposit::where('status', 0)->where('gateway_id', 503)->where('try', '<=', 100)->get();

        $method = Gateway::find(503);
        $apiKey = $method->val1;
        $version = 2;
        $pin = $method->val2;
        $block_io = new BlockIo($apiKey, $pin, $version);


        foreach ($DepositData as $data) {
            $balance = $block_io->get_address_balance(array('addresses' => $data->btc_wallet));
            $bal = $balance->data->available_balance;

            if ($bal > 0 && $bal >= $data->btc_amo) {
                //Update User Data
                $this->userDataUpdate($data);
            }
            $data['try'] = $data->try + 1;
            $data->update();
        }
    }

    public function blockIpnDog(Request $request)
    {
        $DepositData = Deposit::where('status', 0)->where('gateway_id', 504)->where('try', '<=', 100)->get();

        $method = Gateway::find(504);
        $apiKey = $method->val1;
        $version = 2;
        $pin = $method->val2;
        $block_io = new BlockIo($apiKey, $pin, $version);


        foreach ($DepositData as $data) {
            $balance = $block_io->get_address_balance(array('addresses' => $data->btc_wallet));
            $bal = $balance->data->available_balance;

            if ($bal > 0 && $bal >= $data->btc_amo) {
                //Update User Data
                $this->userDataUpdate($data);
            }
            $data['try'] = $data->try + 1;
            $data->update();
        }
    }

    public function ipnCoinPayBtc(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount2 = floatval($request->amount2);
        $currency2 = $request->currency2;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;
        if ($status >= 100 || $status == 2) {
            if ($currency2 == "BTC" && $data->status == '0' && $data->btc_amo <= $amount2) {
                $this->userDataUpdate($data);
            }
        }
    }

    public function ipnCoinPayEth(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount2 = floatval($request->amount2);
        $currency2 = $request->currency2;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;
        if ($status >= 100 || $status == 2) {
            if ($currency2 == "ETH" && $data->status == '0' && $data->btc_amo <= $amount2) {
                $this->userDataUpdate($data);
            }
        }
    }

    public function ipnCoinPayBch(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount2 = floatval($request->amount2);
        $currency2 = $request->currency2;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;
        if ($status >= 100 || $status == 2) {
            if ($currency2 == "BCH" && $data->status == '0' && $data->btc_amo <= $amount2) {
                $this->userDataUpdate($data);
            }
        }
    }

    public function ipnCoinPayDash(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount2 = floatval($request->amount2);
        $currency2 = $request->currency2;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;
        if ($status >= 100 || $status == 2) {
            if ($currency2 == "DASH" && $data->status == '0' && $data->btc_amo <= $amount2) {
                $this->userDataUpdate($data);
            }
        }
    }

    public function ipnCoinPayDoge(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount2 = floatval($request->amount2);
        $currency2 = $request->currency2;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;
        if ($status >= 100 || $status == 2) {
            if ($currency2 == "DOGE" && $data->status == '0' && $data->btc_amo <= $amount2) {
                $this->userDataUpdate($data);
            }
        }
    }

    public function ipnCoinPayLtc(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount2 = floatval($request->amount2);
        $currency2 = $request->currency2;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;
        if ($status >= 100 || $status == 2) {
            if ($currency2 == "LTC" && $data->status == '0' && $data->btc_amo <= $amount2) {
                $this->userDataUpdate($data);
            }
        }
    }

    public function ipnCoinGate()
    {
        $data = Deposit::where('trx',$_POST['order_id'])->orderBy('id', 'DESC')->first();

        if($_POST['status'] == 'paid' && $_POST['price_amount'] == $data->usd && $data->status == '0')
        {
            $this->userDataUpdate($data);
        }

    }

    public function ipnCoin(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount1 = floatval($request->amount1);
        $currency1 = $request->currency1;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;

        if ($currency1 == "BTC" && $amount1 >= $bcoin && $data->status == '0') {
            if ($status >= 100 || $status == 2) {
                //Update User Data
                $this->userDataUpdate($data);
            }
        }
    }

}
