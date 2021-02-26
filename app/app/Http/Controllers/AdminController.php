<?php

namespace App\Http\Controllers;

use App\Admin;
use App\BuyMoney;
use App\ExchangeMoney;
use App\Provider;
use App\SellMoney;
use App\Message;
use App\Trx;
use App\User;
use App\Coin;
use App\Currency;
use App\Stellar;
use App\Lib\BlockIo;
use Illuminate\Http\Request;
use Auth;
use App\GeneralSettings;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use File;
use Image;
use config;

use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\SetOptionsOp;

class AdminController extends Controller
{
	public function __construct(){
		$Gset = GeneralSettings::first();
		$this->sitename = $Gset->sitename;
		$this->middleware('auth:admin');
	}

	 public function createadmin()
    {
    $user = Auth::guard('admin')->user();
        if($user->createuser != 1){
         return back()->with('alert', 'You dont administrative right to access this page.');
         }
        $data['page_title'] = "Create Admin";
        $data['admin'] = Admin::latest()->get();
        return view('admin.staff.create', $data);
    }

     public function createadminpost(Request $request)
    {

                    Admin::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'password' => $request->password,
                    'manageuser' => $request->manageuser == 'on' ? '1' : '0',
                    'createuser' => $request->createuser == 'on' ? '1' : '0',

                    'viewuser' => $request->viewuser == 'on' ? '1' : '0',
                    'blockchain' => $request->blockchain == 'on' ? '1' : '0',
                    'purchase' => $request->purchase == 'on' ? '1' : '0',
                    'sales' => $request->sales == 'on' ? '1' : '0',
                    'withdraw' => $request->withdraw == 'on' ? '1' : '0',
                    'deposit' => $request->deposit == 'on' ? '1' : '0',
                    'transfer' => $request->transfer == 'on' ? '1' : '0',
                    'settings' => $request->settings == 'on' ? '1' : '0',
                    'frontend' => $request->settings == 'on' ? '1' : '0',
                    'settings' => $request->settings == 'on' ? '1' : '0',
                    'kyc' => $request->kyc == 'on' ? '1' : '0',
                ]);

        $notification = array('success' => 'New Administrative Staff Created Successfuly!', 'alert-type' => 'success');
        return back()->with($notification);
    }



    public function exchangeLog()
    {
        $data['exchange'] = ExchangeMoney::where('status', '!=',0)->latest()->get();
        $data['page_title'] = 'Manage Exchange Log';
        return view('admin.currency.exchange-list', $data);
	}

    public function exchangeInfo($id)
    {
        $get = ExchangeMoney::where('id',$id)->where('status','!=',0)->first();
        if($get)
        {
            $data['exchange'] = $get;
            $data['page_title'] = ' Exchange Log Details';
            return view('admin.currency.exchange-info', $data);
        }
        abort(404);
	}

    public function exchangeapprove($id)
    {
        $data = ExchangeMoney::find($id);
        $data->status= 2;
        $data->save();


        Message::create([
                    'user_id' => $data->user_id,
                    'title' => 'Exchange Approved',
                    'details' => 'Your cryptocurrency exchange with transaction number '.$data->transaction_number.' was approved. Your fund has been credited into your wallet as requested',
                    'admin' => 1,
                    'status' =>  0
                ]);



        $notification =  array('message' => 'Exchange Approved Successfully !!', 'alert-type' => 'success');
        return back()->with($notification);
	}

    public function exchangereject($id)
    {
        $data = ExchangeMoney::find($id);
        $data->status= -2;
        $data->save();


         Message::create([
                    'user_id' => $data->user_id,
                    'title' => 'Exchange Rejected',
                    'details' => 'Your cryptocurrency exchange was with transaction number '.$data->transaction_number.' rejected. Please send us a message to facilitate a refund if your money is not refunded in 24hours',
                    'admin' => 1,
                    'status' =>  0
                ]);



        $notification =  array('message' => 'Exhange Rejected Successfully !!', 'alert-type' => 'success');
        return back()->with($notification);
	}


	public function socialLogin()
    {
        $data['page_title'] = 'Manage Social Login';
        $data['providers'] = Provider::all();
        return view('admin.social-login.index', $data);
    }

    public function socialLoginUpd(Request $request)
    {
        $data =  Provider::find($request->id);
        $data->client_id =  $request->name;
        $data->client_secret =  $request->account;
        $data->save();

        $notification =  array('message' => 'Updated Successfully !!', 'alert-type' => 'success');
        return back()->with($notification);
    }


    public function dashboard()
    {
        $data['page_title'] = 'DashBoard';
        return view('admin.dashboard', $data);
    }


    public function changePassword()
    {
        $data['page_title'] = "Change Password";
        return view('admin.change_password',$data);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'password_confirmation' => 'required|same:new_password',
        ]);

        $user = Auth::guard('admin')->user();

        $oldPassword = $request->old_password;
        $password = $request->new_password;
        $passwordConf = $request->password_confirmation;

        if (!Hash::check($oldPassword, $user->password) || $password != $passwordConf) {
            $notification =  array('message' => 'Password Do not match !!', 'alert-type' => 'error');
            return back()->with($notification);
        }elseif (Hash::check($oldPassword, $user->password) && $password == $passwordConf)
        {
            $user->password = bcrypt($password);
            $user->save();
            $notification =  array('message' => 'Password Changed Successfully !!', 'alert-type' => 'success');
            return back()->with($notification);
        }
    }


    public function profile()
    {
        $data['admin'] = Auth::user();
        $data['page_title'] = "Profile Settings";
        return view('admin.profile',$data);
    }

    public function updateProfile(Request $request)
    {
        $data = Admin::find($request->id);
        $request->validate([
            'name' => 'required|max:20',
            'email' => 'required|max:50|unique:admins,email,'.$data->id,
            'mobile' => 'required',
        ]);

        $in = Input::except('_method','_token');
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = 'admin_'.time().'.jpg';
            $location = 'assets/admin/img/' . $filename;
            Image::make($image)->resize(300,300)->save($location);
            $path = './assets/admin/img/';
            File::delete($path.$data->image);
            $in['image'] = $filename;
        }
        $data->fill($in)->save();

        $notification =  array('message' => 'Admin Profile Update Successfully', 'alert-type' => 'success');
        return back()->with($notification);
    }


    public function blockchainwallet($id)
    {
        $auth = Auth::user();
        $data['page_title'] = "Blockchain Wallet";
        $data['id'] = $id;
        $data['coin'] = Coin::whereId($id)->first();
        $data['wallet'] = Coinwallet::whereCoin_id($id)->whereUser_id($auth->id)->get();
        return view('user.blockhain.index', $data);
     }

      public function createwallet($id)
	{

	    $coin = Coin::find($id);
	    $user = User::find(Auth::id());

	    $gnl = GeneralSettings::first();
    	if ($id == 1){
    	$key = $gnl->dogapi;
    	}
          if ($id == 2){
    	$key = $gnl->ltcapi;
    	}
          if ($id == 3){
    	$key = $gnl->btcapi;
         }


	    $baseUrl = "https://block.io";
        $endpoint = "/api/v2/get_new_address/?api_key=".$key."";
        $httpVerb = "GET";
        $contentType = "application/json"; //e.g charset=utf-8
        $headers = array (
            "Content-Type: $contentType",

        );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );

            curl_close($ch);

            if($content['status'] == "success") {
                     $response['address'] = $content['data']['address'];
                     $address =  $response['address'];
                     $network = $content['data']['network'];


					$wallet['address'] =  $address;
					$wallet['coin_id'] =  $id;
					$wallet['user_id'] =  Auth::id();
					$wallet['name'] =  $coin->name;
					$wallet['pending'] =  0.00;
					$wallet['balance'] =  0.00;
					Coinwallet::create($wallet);


            return back()->with('success', 'New '.$network.' Wallet Address Has Been Created & Activated.');


            }

            if($content['status']  == "fail") {
                     $response['error_message'] = $content['data']['error_message'];
                     $reply =  $response['error_message'];
            return back()->with('alert', ''.$reply.'');

            }


	}


	   public function viewwallets($id)
    {

        $user = User::find(Auth::id());
        $wallet = Coinwallet::whereAddress($id)->first();
         $coin = Coin::find($wallet->coin_id);
	    $gnl = GeneralSettings::first();
    	if ($coin->id == 1){
    	$key = $gnl->dogapi;
    	}
          if ($coin->id == 2){
    	$key = $gnl->ltcapi;
    	}
          if ($coin->id == 3){
    	$key = $gnl->btcapi;
         }

        $baseUrl = "https://api.coingate.com/v2";
        $endpoint = "/rates/merchant/BTC/USD";
        $httpVerb = "GET";
        $contentType = "application/json"; //e.g charset=utf-8
        $headers = array (
            "Content-Type: $contentType",

        );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );

            curl_close($ch);
           $btcrate = $content;

        $baseUrl = "https://block.io";
        $endpoint = "/api/v2/get_address_balance/?api_key=".$key."&addresses=".$id."";
        $httpVerb = "GET";
        $contentType = "application/json"; //e.g charset=utf-8
        $headers = array (
            "Content-Type: $contentType",

        );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );

            curl_close($ch);

            if($content['status'] == "success") {
             $bal = $content['data']['available_balance'];
             $pend =  $content['data']['pending_received_balance'];
              $wallet['balance'] =  $bal;
              $wallet['pending'] =  $pend;
	          $wallet->save();
	          $network = $content['data']['network'];
	          $address = $content['data']['balances'][0]['address'];

            }
            else { $network = 1;
                    $bal = 0;
                    $pend = 0; }

        $baseUrl = "https://block.io";
        $endpoint = "/api/v2/get_transactions/?api_key=".$key."&type=sent&addresses=".$id."";
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

            $strx = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);
        	 if($strx['status'] == "success") {
        	 $count = count($strx['data']['txs']);

        	 if ( $count > 0 ){

        	$date = $strx['data']['txs'][0]['time'];
        	$sdate = date("D,d-M.Y", $date); }
        	else{
        	$sdate =  "00-00-0000 00:00";        	}

            }

        $baseUrl = "https://block.io";
        $endpoint = "/api/v2/get_transactions/?api_key=".$key."&type=received&addresses=".$id."";
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

            $rtrx = json_decode(curl_exec( $ch ),true);
            $trtrx = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);
        	 if($rtrx['status'] == "success") {
        	 $count = count($trtrx['data']['txs']);
        	 if ( $count > 0 ){
        	$date = $rtrx['data']['txs'][0]['time'];
        	$rdate = date("D,d-M.Y", $date); }
        	else{
        	$rdate = "00-00-0000 00:00";
        	}
            }
        $rate = 350;

        return view('user.blockhain.wallet', compact('btcrate','address','sdate','rdate','rate','strx','rate','trtrx','rtrx','trx','network','pend','bal','bala','user','wallet', 'coin', 'logs','all','lastra','gnl'));
    }


       public function sendpreview(Request $request)
	{
        $gnl = GeneralSettings::first();
        $coin = Coin::find($request->coin);
    	if ($coin->id == 1){
    	$key = $gnl->dogapi;
    	}
          if ($coin->id == 2){
    	$key = $gnl->ltcapi;
    	}
          if ($coin->id == 3){
    	$key = $gnl->btcapi;
         }
        $baseUrl = "https://block.io";
        $endpoint = "/api/v2/get_network_fee_estimate/?api_key=".$key."&amounts=".$request->amount."&to_addresses=".$request->toid."";
        $httpVerb = "GET";
        $contentType = "application/json"; //e.g charset=utf-8
        $headers = array (
            "Content-Type: $contentType",

        );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );

            curl_close($ch);

             if($content['status']  == "fail") {
             $response['error_message'] = $content['data']['error_message'];
             $reply =  $response['error_message'];
             return back()->with('alert', ''.$reply.'');

            }

            if($content['status'] == "success") {
            $response['estimated_network_fee'] = $content['data']['estimated_network_fee'];
            $network = $content['data']['network'];
            $response['estimated_tx_size'] = $content['data']['estimated_tx_size'];
            $fee =  $response['estimated_network_fee'];
            $size =  $response['estimated_tx_size'];
             }
             $amount = $request->amount;
             $receiver = $request->toid;
             $sender = $request->sender;
             $user = User::find(Auth::id());
           return view('user.blockhain.preview-send', compact('id','network','size','user','prior','logo','fee','amount','receiver','sender','coin'));


	}



		 public function sendcoin(Request $request)
    {
        $gnl = GeneralSettings::first();
        $coin = Coin::find($request->coin);
    	if ($coin->id == 1){
    	$key = $gnl->dogapi;
    	}
          if ($coin->id == 2){
    	$key = $gnl->ltcapi;
    	}
          if ($coin->id == 3){
    	$key = $gnl->btcapi;
         }

        $pin = $gnl->apikey;
        $version = 2;
        $block_io = new BlockIo($apiKey, $pin, $version);

        $user = User::find(Auth::id());

        $fee = $block_io->get_network_fee_estimate(array('amounts' => $request->amount, 'to_addresses' => $request->toid));

        $tranfee = $fee->data->estimated_network_fee;

        $total =  $tranfee + $request->amount;

          $block_io->withdraw_from_addresses(array('amounts' => $request->amount, 'from_addresses' =>  $request->coin, 'to_addresses' => $request->toid));

            session()->flash('success', 'Coin Sent Successfully. ');

         return redirect()->route('home');


        }



        public function walletsent($id)
    {
        $user = User::find(Auth::id());
        $gnl = GeneralSettings::first();
        $wallet = Coinwallet::whereUser_id($user->id)->whereAddress($id)->first();
        $coin = Coin::find($wallet->coin_id);
    	if ($coin->id == 1){
    	$key = $gnl->dogapi;
    	}
          if ($coin->id == 2){
    	$key = $gnl->ltcapi;
    	}
          if ($coin->id == 3){
    	$key = $gnl->btcapi;
         }

        $baseUrl = "https://block.io";
        $endpoint = "/api/v2/get_transactions/?api_key=".$key."&type=sent&addresses=".$id."";
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

            $trx = json_decode(curl_exec( $ch ),true);
            $strx = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);
        	 if($trx['status'] == "success") {
        	$response['txs'] = $trx['data']['txs'];
        	$network = $trx['data']['network'];
        	$count = count($trx['data']['txs']);

            }

             return view('user.blockhain.sent', compact('count','strx','rate','trtrx','rtrx','trx','network','pend','bal','bala','user','wallets', 'coin', 'logs','all','lastra','gnl'));
    }


       public function walletreceived($id)
    {
        $user = User::find(Auth::id());
        $gnl = GeneralSettings::first();
        $wallet = Coinwallet::whereUser_id($user->id)->whereAddress($id)->first();
        $coin = Coin::find($wallet->coin_id);
    	if ($coin->id == 1){
    	$key = $gnl->dogapi;
    	}
          if ($coin->id == 2){
    	$key = $gnl->ltcapi;
    	}
          if ($coin->id == 3){
    	$key = $gnl->btcapi;
         }
        $baseUrl = "https://block.io";
        $endpoint = "/api/v2/get_transactions/?api_key=".$key."&type=received&addresses=".$id."";
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

            $trx = json_decode(curl_exec( $ch ),true);
            $strx = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);
        	 if($trx['status'] == "success") {
        	$response['txs'] = $trx['data']['txs'];
        	$network = $trx['data']['network'];
        	$count = count($trx['data']['txs']);

            }


        return view('user.blockhain.received', compact('count','strx','rate','trtrx','rtrx','rnetwork','trx','network','pend','bal','bala','user','wallets', 'coin', 'logs','all','lastra','gnl'));
    }
    public function logout()    {
		Auth::guard('admin')->logout();
		session()->flash('message', 'Just Logged Out!');
		return redirect('/admin');
    }

    public function create_wallet() {
        $data['private_key']=config('paypal.stellar_private_key');
        $data['public_key']=config('paypal.stellar_public_key');
        return view('admin.stellar.create_wallet',$data);
    }

    public function create_walletkey() {
        $keypair = Keypair::newFromRandom();

        $data = array(
            'private_key' => $keypair->getSecret() . PHP_EOL,
            'public_key' => $keypair->getPublicKey() . PHP_EOL
        ); 

        echo json_encode($data);
    }

    public function clear_wallet() {
        $env_update = $this->changeEnv([
            'STELLAR_PRIVATE_KEY' => "",
            'STELLAR_PUBLIC_KEY'   => ""
        ]);

        // Config::set('paypal.stellar_private_key', $request->private_key);
        // Config::set('paypal.stellar_public_key', $request->public_key);

        // config(['paypal.stellar_private_key' => $request->private_key, 'paypal.stellar_public_key' => $request->public_key]);

        echo json_encode($data);    
    }

    public function save_wallet(Request $request) {
        $env_update = $this->changeEnv([
            'STELLAR_PRIVATE_KEY' => $request->private_key,
            'STELLAR_PUBLIC_KEY'   => $request->public_key
        ]);

        // Config::set('paypal.stellar_private_key', $request->private_key);
        // Config::set('paypal.stellar_public_key', $request->public_key);

        // config(['paypal.stellar_private_key' => $request->private_key, 'paypal.stellar_public_key' => $request->public_key]);

        if($env_update){
            return redirect()->back()->with('message', 'Succussfully saved');
        } else {
            return redirect()->back()->withAlert('Succussfully saved');
        }
        
    }

    protected function changeEnv($data = array()){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            
            return true;
        } else {
            return false;
        }
    }

    public function create_coin() {
        $data['coin']=Currency::find(1)->symbol;
        $data['amount']=50000;
        return view('admin.stellar.create_coin',$data);
    }

    public function save_coin(Request $request) {
        $coin_name = $request->coin_name;
        $server = Server::publicNet();

        $issuingKeypair = config('paypal.stellar_public_key');
        $receivingKeypair = config('paypal.stellar_private_key');

        $asset = Asset::newCustomAsset($coin_name, $issuingKeypair);


        // First, the receiving account must add a trustline for the issuer
        try {
            $response = $server->buildTransaction($issuingKeypair)
            ->addChangeTrustOp($asset) // this will default to the maximum value
            ->submit($receivingKeypair);
        } catch (PostTransactionException $e) {
            // Details operation information can be retrieved from this exception
            $operationResults = $e->getResult()->getOperationResults();
        
            foreach ($operationResults as $result) {
                // Skip through the ones that worked
                if ($result->succeeded()) continue;
        
                // Print out the first failed one
                $err="Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
                return redirect()->back()->with('alert', $err);
            }
        }

        try {
            $response = $server->buildTransaction($issuingKeypair)
            ->addCustomAssetPaymentOp($asset, $request->coin_amount, $issuingKeypair) // this will default to the maximum value
            ->getTransactionEnvelope();
            $response->sign($receivingKeypair);
            $b64Tx = base64_encode($response->toXdr());
            $server->submitB64Transaction($b64Tx);
        } catch (PostTransactionException $e) {
            // Details operation information can be retrieved from this exception
            $operationResults = $e->getResult()->getOperationResults();
        
            foreach ($operationResults as $result) {
                // Skip through the ones that worked
                if ($result->succeeded()) continue;
        
                // Print out the first failed one
                $err="Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
                return redirect()->back()->with('alert', $err);
    
            }
        }
        $price=Currency::whereId(1)->first()->price;
        $data=new Stellar;
        $data['AssetCode']='SMC';
        $data['AssetID']=$issuingKeypair;
        $data['price']=$price;
        $data['status']=-3;
        $data['gateway_id']=300;
        $data['val1']='sign-btc-alt';
        $data->save();
        $data1=new Stellar;
        $data1['AssetCode']='XLM';
        $data1['AssetID']='##';
        $data1['status']=202;
        $data1['gateway_id']=300;
        $data1['val1']='sign-btc-alt';
        $data1->save();
        return redirect()->back()->with('message', 'Successfully trustline');
        
    }

    public function add_stellarwallet(){
        $data['private_key']=config('paypal.admin_private_key');
        $data['public_key']=config('paypal.admin_public_key');
        $data['paypal_id']=config('paypal.client_id');
        $data['paypal_secret']=config('paypal.secret');
        $data['bit']=Coin::find(3)->address;
        $data['lite']=Coin::find(2)->address;
        $data['doge']=Coin::find(1)->address;
        return view('admin.stellar.add_wallet',$data);
    }

    public function save_stellarwallet(Request $request) {
        if($request->payment==1){
            $this->validate($request, [
                'private_key'=>'required',
                'public_key' => 'required',
            ]);
    
            GeneralSettings::first()->update(array(
                'stellar_wallet'=>$request->public_key,
            ));
            $env_update = $this->changeEnv([
                'ISSURE_SECRET' => $request->private_key,
                'ISSURE_ID'   => $request->public_key
            ]);
            if($env_update){
                return redirect()->back()->with('message', 'Succussfully saved');
            } else {
                return redirect()->back()->withAlert('Failed saved');
            }     
        }
        elseif($request->payment==2){
            $this->validate($request, [
                'paypal_address'=>'required',
                'paypal_secret'=>'required',
            ]);
            $env_update = $this->changeEnv([
                'PAYPAL_CLIENT_ID' => $request->paypal_address,
                'PAYPAL_SECRET'   => $request->public_secret
            ]);
            if($env_update){
                return redirect()->back()->with('message', 'Succussfully saved');
            } else {
                return redirect()->back()->withAlert('Failed saved');
            }     
        }
        else{
            $this->validate($request, [
                'bit_key'=>'required',
                'lite_key' => 'required',
                'doge_key'=>'required',
            ]);
            Coin::find(1)->update(array(
                'address'=>$request->doge_key,
            ));
            Coin::find(2)->update(array(
                'address'=>$request->lite_key,
            ));
            Coin::find(3)->update(array(
                'address'=>$request->bit_key,
            ));
            return redirect()->back()->with('message', 'Successful saved');
        }    
    }

    public function view_assets(){
        $data['coins']=Stellar::orderBy('id')->get();
        return view('admin.stellar.assets',$data);
    }

    public function charge_smc(){
        $issuingKeypair = config('paypal.stellar_public_key');
        $receivingKeypair = config('paypal.admin_public_key');
        $issuingsecret = config('paypal.stellar_private_key');
        $asset = Asset::newCustomAsset('SMC', $issuingKeypair);
        $server=Server::publicNet();
        try {
            $response = $server->buildTransaction($issuingKeypair)
            ->addCustomAssetPaymentOp($asset, 1000, $receivingKeypair) // this will default to the maximum value
            ->getTransactionEnvelope();
            $response->sign($issuingsecret);
            $b64Tx = base64_encode($response->toXdr());
            $server->submitB64Transaction($b64Tx);
        } catch (PostTransactionException $e) {
            // Details operation information can be retrieved from this exception
            $operationResults = $e->getResult()->getOperationResults();
        
            foreach ($operationResults as $result) {
                // Skip through the ones that worked
                if ($result->succeeded()) continue;
        
                // Print out the first failed one
                $err="Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
                return redirect()->back()->with('alert', $err);
    
            }
        }
        return redirect()->back()->with('message','Successful 1000 SMC charged');
    }
    public function active_assets($id)
    {
        Stellar::whereId($id)->update(array(
            'status'=>1,
        ));
        return redirect()->back()->with('message','Success active asset.');
    }
    public function deactive_assets($id)
    {
        Stellar::whereId($id)->update(array(
            'status'=>0,
        ));
        return redirect()->back()->with('message','Success deactive asset.');
    }
    public function del_assets($id)
    {
        Stellar::whereId($id)->delete();
        return redirect()->back()->with('message','Success delete asset.');
    }

    public function get_balance(){
        $receivingKeypair = config('paypal.admin_public_key');
        $server=Server::publicNet();
        $account = $server->getAccount($receivingKeypair);
        foreach ($account->getBalances() as $balance) {
            $AssetID=$balance->getAssetIssuerAccountId();
            $code=$balance->getAssetCode();
            $coin_balance=$balance->getBalance();
            if($code='XML'){
                Stellar::where('AssetCode','XML')->update(array(
                    'balance'=>$coin_balance,
                ));
                continue;
            }
            $data=Stellar::where('AssetID',$AssetID)->where('AssetCode',$code)->first();
            if($data){
                Stellar::where('AssetID',$AssetID)->where('AssetCode',$code)->update(array(
                    'balance'=>$coin_balance,
                ));
            }
            else{
                $data=new Stellar;
                $data['AssetCode']=$code;
                $data['AssetID']=$AssetID;
                $data['balance']=$coin_balance;
                $data['gateway_id']='300';
                $data['status']='1';
                $data->save();
            }
        }
    }

    public function add_asset(Request $request)
    {
        // $this.get_balance();
        $status=Stellar::where('AssetCode','SMC')->first()->status;
        if($status==-3){
            $issuingKeypair = config('paypal.admin_public_key');
            $receivingKeypair = config('paypal.admin_private_key');
            $issuingKeypairs = config('paypal.stellar_public_key');
            $asset = Asset::newCustomAsset('SMC', $issuingKeypairs);
            $server=Server::publicNet();
            try {
                $response = $server->buildTransaction($issuingKeypair)
                ->addChangeTrustOp($asset) // this will default to the maximum value
                ->submit($receivingKeypair);
            } catch (PostTransactionException $e) {
                // Details operation information can be retrieved from this exception
                $operationResults = $e->getResult()->getOperationResults();
            
                foreach ($operationResults as $result) {
                    // Skip through the ones that worked
                    if ($result->succeeded()) continue;
            
                    // Print out the first failed one
                    $err="Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
                    return redirect()->back()->with('alert', $err);
                }
            }
            Stellar::where('AssetCode','SMC')->update(array(
                'status'=>'222',
            ));
        }
        $this->validate($request, [
            'public_key' => 'required',
            'private_key'=>'required',
            'assetid'=>'required',
            'assetcode'=>'required',
            'price'=>'required',
        ]);

        $server = Server::publicNet();

        //Account Exist
            try {
                $exists = $server->accountExists($request->public_key);
            
                if ($exists) {
                }
                else {
                    return back()->with("alert", "Account does not exist!  You need buy some balance Lumens");
                }
            }
            catch (\Exception $e) {
                return back()->with("alert", $e->getMessage());
            }

        //Trustline
            
        $asset = Asset::newCustomAsset($request->assetcode, $request->assetid);
            try {
                $response = $server->buildTransaction($request->public_key)
                ->addChangeTrustOp($asset) // this will default to the maximum value
                ->submit($request->private_key);
            } catch (PostTransactionException $e) {
                // Details operation information can be retrieved from this exception
                $operationResults = $e->getResult()->getOperationResults();
            
                foreach ($operationResults as $result) {
                    // Skip through the ones that worked
                    if ($result->succeeded()) continue;
            
                    // Print out the first failed one
                    $err="Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
                    return redirect()->back()->with('alert', $err);
                }
            }
        $data=new Stellar;
        $data['AssetCode']=$request->assetcode;
        $data['AssetID']=$request->assetid;
        $data['price']=$request->price;
        $data['status']=1;
        $data['gateway_id']=300;
        $data['val1']='sign-btc-alt';
        $data->save();
        return redirect()->back()->with('message','Success active asset.');
    }


    //paypal-transaction
    public function buyapprove($id)
    {
        $data = Trx::find($id);
        $user_id=$data->user_id;
        $received_address=User::whereId($user_id)->first()->stellarwallet;
        $issuingAccountID = config('paypal.stellar_public_key');
        $public=config('paypal.admin_public_key');
        $Secret=config('paypal.admin_privete_key');
        $asset = Asset::newCustomAsset('SMC', $issuingAccountID);
        $server=Server::publicNet();
        try {
            $response = $server->buildTransaction($public)
            ->addCustomAssetPaymentOp($asset, $data->get_amo, $received_address) // this will default to the maximum value
            ->getTransactionEnvelope();
            $response->sign($Secret);
            $b64Tx = base64_encode($response->toXdr());
            $server->submitB64Transaction($b64Tx);
        } catch (PostTransactionException $e) {
            // Details operation information can be retrieved from this exception
            $operationResults = $e->getResult()->getOperationResults();
        
            foreach ($operationResults as $result) {
                // Skip through the ones that worked
                if ($result->succeeded()) continue;
        
                // Print out the first failed one
                $err="Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
                return redirect()->back()->with('alert', $err);
    
            }
        }
        
        $basic = GeneralSettings::first();
        $data->status= 2;
        $data->save();
         Message::create([
                    'user_id' => $data->user_id,
                    'title' => 'Coin Purchase Approved',
                    'details' => 'Your cryptocurrency purchase with transaction number '.$data->trx.'  was approved. Thank you for choosing '.$basic->sitename.'',
                    'admin' => 1,
                    'status' =>  0
                ]);
        $notification =  array('message' => 'Approved Successfully !!', 'alert-type' => 'success');
        return back()->with($notification);
    }
    //end
    public function buyLog()
    {
        $data['exchange'] = Trx::whereType(1)->latest()->get();
        $data['page_title'] = 'Processed Purchase';
        return view('admin.currency.buy-list', $data);
    }
    public function pendingbuyLog()
    {
        $data['exchange'] = Trx::whereStatus(1)->whereType(1)->latest()->get();
        $data['page_title'] = 'Pending Purchase';
        return view('admin.currency.buy-list', $data);
    }
    public function declinedbuyLog()
    {
        $data['exchange'] = Trx::whereStatus(-2)->whereType(1)->latest()->get();
        $data['page_title'] = 'Declined Purchase';
        return view('admin.currency.buy-list', $data);
    }
    public function buyInfo($id)
    {
        $get = Trx::where('id',$id)->where('status','!=',0)->first();
        if($get)
        {
            $data['exchange'] = $get;
            $data['page_title'] = ' Buy Log Details';
            return view('admin.currency.buy-info', $data);
        }
        abort(404);
    }

    public function buyreject(Request $request)
    {

        $data = Trx::find($request->id);
        $basic = GeneralSettings::first();
        $user = User::findOrFail($data->user_id);



                      Message::create([
                    'user_id' => $data->user_id,
                    'title' => 'Purchase Rejected',
                    'details' => 'Your cryptocurrency purchase with transaction number '.$data->trx.' was rejected. Please send us a message for complaints or clarifications on purchase rejection',
                    'admin' => 1,
                    'status' =>  0
                ]);

                $msg =  ' Buy Declined ' . $data->main_amo . ' ' . $basic->currency;
                send_email($user->email, $user->username, 'Buy Amount return ', $msg);

                 $data->status= -2;

                  $data->save();

        $notification =  array('message' => 'Cryptocurrency Purchase Was Rejected Successfully !!', 'alert-type' => 'success');
        return back()->with($notification);
    }
    //send transaction


    public function sellLog()
    {
        $data['exchange'] = Trx::whereType(0)->latest()->get();
        $data['page_title'] = 'Processed Sales';
        return view('admin.currency.sell-list', $data);
    }
    public function pendingsellLog()
    {
        $data['exchange'] = Trx::whereStatus(1)->whereType(0)->latest()->get();
        $data['page_title'] = 'Pending Sales';
        return view('admin.currency.sell-list', $data);
    }
    public function declinedsellLog()
    {
        $data['exchange'] = Trx::whereStatus(-2)->whereType(0)->latest()->get();
        $data['page_title'] = 'Declined Sales';
        return view('admin.currency.sell-list', $data);
    }
    public function sellInfo($id)
    {
        $get = Trx::where('id',$id)->where('status','!=',0)->first();
        if($get)
        {
            $data['exchange'] = $get;
            $data['page_title'] = ' Sell Log Details';
            return view('admin.currency.sell-info', $data);
        }
        abort(404);
    }

    public function sellapprove($id)
    { 
        $basic = GeneralSettings::first();
        $admin_private_key=config('paypal.admin_private_key');
        $admin_public_key=config('paypal.admin_public_key');
        $coindata = Trx::find($id);
        $address=$coindata->wallet;
        if($coindata->remark=='BTC'|| $coindata->remark=='DOG'||$coindata->remark=='LTC'){
            $api=strtolower($coindata->currency)."api";
            $key=GeneralSettings::first()->$api;
            $baseUrl = "https://block.io";
            $endpoint = "/api/v2/get_network_fee_estimate/?api_key=".$key."&amounts=".$coindata->getamo."&to_addresses=".$address."";
            $httpVerb = "GET";
            $contentType = "application/json"; //e.g charset=utf-8
            $headers = array (
                "Content-Type: $contentType",

            );
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $content = json_decode(curl_exec( $ch ),true);
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );

                curl_close($ch);

                    if($content['status']  == "fail") {
                    $response['error_message'] = $content['data']['error_message'];
                    $reply =  $response['error_message'];
                    return back()->with('alert', ''.$reply.'');

                }

                if($content['status'] == "success") {
                    $response['estimated_network_fee'] = $content['data']['estimated_network_fee'];
                    $network = $content['data']['network'];
                    $response['estimated_tx_size'] = $content['data']['estimated_tx_size'];
                    $fee =  $response['estimated_network_fee'];
                    $size =  $response['estimated_tx_size'];
                    }
            Trx::where('trx', $trx)->update(array(
                'status' => '2',
            ));

        }
        else if($coindata->remark=='USD'){
            $coindata->status= 2;
            $coindata->save();
            Message::create([
                'user_id' => $data->user_id,
                'title' => 'Sales Approved',
                'details' => 'Your cryptocurrency sales with transaction number '.$coindata->trx.' has been approved. You fund has been credited to your account as required. Thank you for choosing us',
                'admin' => 1,
                'status' =>  0
            ]);

            $user = User::find($coindata->user_id);
            $msg =  ' Sell Amount  ' . $coindata->getamo . ' ' . USD;
            send_email($user->email, $user->username, 'Sell Amount  ', $msg);
            $notification =  array('message' => 'Sales Approved Successfully !!', 'alert-type' => 'success');
            return back()->with($notification);
        }
        else{
            $server=SERVER::publicNet();
            $code=substr($coindata->remark,0,3);
            $coindatas=Stellar::where('AssetCode',$code)->first();
            $asset = Asset::newCustomAsset($coindatas->AssetCode, $coindatas->AssetID);

            try {
                $response = $server->buildTransaction($admin_public_key)
                ->addCustomAssetPaymentOp($asset, $coindata->getamo, $address) // this will default to the maximum value
                ->getTransactionEnvelope();
                $response->sign($admin_private_key);
                $b64Tx = base64_encode($response->toXdr());
                $server->submitB64Transaction($b64Tx);
            } catch (PostTransactionException $e) {
                // Details operation information can be retrieved from this exception
                $operationResults = $e->getResult()->getOperationResults();
            
                foreach ($operationResults as $result) {
                    // Skip through the ones that worked
                    if ($result->succeeded()) continue;
            
                    // Print out the first failed one
                    $err="Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
                    return redirect()->back()->with('alert', $err);
        
                }
            }
            //end transaction
            Trx::where('trx', $trx)->update(array(
                'status' => '2',
            ));
        }

        Message::create([
            'user_id' => $coindata->user_id,
            'title' => 'Sales Approved',
            'details' => 'Your cryptocurrency sales with transaction number '.$coindata->trx.' has been approved. You fund has been credited to your account as required. Thank you for choosing us',
            'admin' => 1,
            'status' =>  0
        ]);

        $user = User::find($coindata->user_id);
        $msg =  ' Sell Amount  ' . $coindata->getamo . ' ' . $coindata->remark;
        send_email($user->email, $user->username, 'Sell Amount  ', $msg);
        $notification =  array('message' => 'Sales Approved Successfully !!', 'alert-type' => 'success');
        return back()->with($notification);
    }


    public function sellreject($id)
    {

        $data = Trx::find($id);
        $basic = GeneralSettings::first();

                    Message::create([
                    'user_id' => $data->user_id,
                    'title' => 'Sale Rejected',
                    'details' => 'Your cryptocurrency sales was rejected. Please send us a message to facilitate a refund if your money is not refunded in 24hours',
                    'admin' => 1,
                    'status' =>  0
                ]);

        $data->status= -2;
        $data->save();

        $notification =  array('message' => 'Rejected Successfully !!', 'alert-type' => 'success');
        return back()->with($notification);
    }
}
