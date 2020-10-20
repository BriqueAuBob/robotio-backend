<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Users\UserCollection;
use App\Http\Resources\Users\UserResource;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Support\Facades\Auth;

use\PayPal\Rest\ApiContext;
use \PayPal\Auth\OAuthTokenCredential;
use \PayPal\Api\Payer;
use \PayPal\Api\Amount;
use \PayPal\Api\Transaction;
use \PayPal\Api\RedirectUrls;
use \PayPal\Api\Payment;
use \PayPal\Api\PaymentExecution;

/**
 * @group User
 * Endpoint relative to users.
 */
class UserController extends Controller
{
    public function me()
    {
        return new UserResource( Auth::user() );
    }

    public function paypalRedirect(Request $request)
    {
        $apiContext = new ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                config("paypal.client_id"),
                config("paypal.client_secret")
            )
        );

        $payer = new Payer();
            $payer->setPaymentMethod('paypal');

        $amount = new Amount();
            $amount->setTotal($request->amount);
            $amount->setCurrency("EUR");

        $transaction = new Transaction();
            $transaction->setAmount($amount);

        $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(route("me.paypal.validate"))// "https://ro-bot.io/panel/payment/validate"
            ->setCancelUrl("https://ro-bot.io/panel/payment/cancel");

        $payment = new Payment();
            $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($apiContext);
            return redirect($payment->getApprovalLink());
        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getData();
            exit(1);
        }
    }

    public function paypalValidate(Request $request) {
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                config("paypal.client_id"),
                config("paypal.client_secret")
            )
        );

        $paymentId = $request->paymentId;
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
            $execution->setPayerId($request->payerID);

        try {
            $result = json_decode($payment);

            return (array)$result;
        } catch (Exception $ex) {
            redirect("https://google.com");
        }
    }
}
