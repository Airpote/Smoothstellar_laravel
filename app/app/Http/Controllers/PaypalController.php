<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Trx;
/** Paypal Details classes **/
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConnectionException;

class PaypalController extends Controller
{
    private $api_context;
    private $txt_id;

    public function __construct()
    {
        $this->api_context = new ApiContext(
            new OAuthTokenCredential(config('paypal.client_id'), config('paypal.secret'))
        );
        $this->api_context->setConfig(config('paypal.settings'));
    }

    public function createPayment($id)
    {
        $request = Trx::where('status', 0)->where('trx', $id)->first();
        $pay_amount = $request->main_amo;
        $this->txt_id=$id;
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName('Paypal Payment')->setCurrency('USD')->setQuantity(1)->setPrice($pay_amount);

        $itemList = new ItemList();
        $itemList->setItems(array($item));

        $amount = new Amount();
        $amount->setCurrency('USD')->setTotal($pay_amount);

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)
        ->setDescription('');
        
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(route('confirm-payment'))
        ->setCancelUrl(url()->current());

        $payment = new Payment();
        $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)
        ->setTransactions(array($transaction));
        
        try {
            $payment->create($this->api_context);
        } catch (PayPalConnectionException $ex){
            return back()->withError('Some error occur, sorry for inconvenient');
        } catch (Exception $ex) {
            return back()->withError('Some error occur, sorry for inconvenient');
        }

        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        if(isset($redirect_url)) {
            return redirect($redirect_url);
        }

        return redirect()->back()->withError('Unknown error occurred'); 
    }
    public function confirmPayment(Request $request)
    {
        // If query data not available... no payments was made.
        if (empty($request->query('paymentId')) || empty($request->query('PayerID')) || empty($request->query('token')))
            return redirect('buybsecoin')->withError('Payment was not successful.');
        // We retrieve the payment from the paymentId.
        $payment = Payment::get($request->query('paymentId'), $this->api_context);
        // We create a payment execution with the PayerId
        $execution = new PaymentExecution();
        $execution->setPayerId($request->query('PayerID'));
        // Then we execute the payment.
        $result = $payment->execute($execution, $this->api_context);
        // Get value store in array and verified data integrity
        // $value = $request->session()->pull('key', 'default');
        // Check if payment is approved
        if ($result->getState() != 'approved')
            return redirect('buybsecoin')->withError('Payment was not successful.');

        $data = Trx::where('status', 0)->where('trx', $this->txt_id)->first();
        Trx::update($data->id,array(
            'status'=>'1',
            'amountpaid'=>$data->main_amo,
        ));
    return redirect('buybscoin')->withSuccess('Payment made successfully');
    }
}