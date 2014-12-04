<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use PayPal\Api\Amount;
use PayPal\Api\CreditCard as PaypalCreditCard;
use PayPal\Api\Payer; 
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;
//use PayPal\Rest\ApiContext;
//use PayPal\Auth\OAuthTokenCredential;
//use PayPal\Api\Address;
//use PayPal\Api\Details;
//use PayPal\Api\Item;
//use PayPal\Api\ItemList;
//use PayPal\Api\RedirectUrls;
//
use Bdloc\AppBundle\Form\CreditCardType;
use Bdloc\AppBundle\Entity\CreditCard;

class PaymentController extends Controller
{
    /**
     * @Route("/paiement")
     */
    public function takeSubscriptionPaymentAction()
    {

        /*$pps = $this->get('paypal_subscription');
        $pps->takePayment();*/
        
        // Recréer un formulaire pour récupérer les données soumises par l'utilisateur
        $creditCard = new CreditCard();
        $creditCardForm = $this->createForm(new CreditCardType(), $creditCard);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($creditCard)
        $request = $this->getRequest();
        $creditCardForm->handleRequest($request);



        //see kmj/paypalbridgebundle
        $apiContext = $this->get('paypal')->getApiContext();

        // ### CreditCard
        // A resource representing a credit card that can be
        // used to fund a payment.
            $card = new PaypalCreditCard();
            $card->setType("visa");
            $card->setNumber("4417119669820331");
            $card->setExpire_month("11");
            $card->setExpire_year("2018");
            $card->setCvv2("987");
            $card->setFirst_name("Joe");
            $card->setLast_name("Shopper");
        //$card = new PaypalCreditCard();
        //$card->setType($creditCard->getCreditCardType());
        //$card->setNumber($creditCard->getCreditCardNumber());
        //$card->setExpire_month($creditCard->getExpirationDate()->format("m"));
        //$card->setExpire_year($creditCard->getExpirationDate()->format("Y"));
        //$card->setCvv2($creditCard->getCodeCVC());
        //$card->setFirst_name($creditCard->getCreditCardFirstName());
        //$card->setLast_name($creditCard->getCreditCardLastName());

        // ### FundingInstrument
        // A resource representing a Payer's funding instrument.
        // Use a Payer ID (A unique identifier of the payer generated
        // and provided by the facilitator. This is required when
        // creating or using a tokenized funding instrument)
        // and the `CreditCardDetails`
        $fi = new FundingInstrument();
        $fi->setCredit_card($card);

        // ### Payer
        // A resource representing a Payer that funds a payment
        // Use the List of `FundingInstrument` and the Payment Method
        // as 'credit_card'
        $payer = new Payer();
        $payer->setPayment_method("credit_card");
        $payer->setFunding_instruments(array($fi));

        // ### Amount
        // Let's you specify a payment amount.
        $amount = new Amount();
        $amount->setCurrency("EUR");
        $amount->setTotal("12.00");

        // ### Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it. Transaction is created with
        // a `Payee` and `Amount` types
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("This is the payment description.");

        // ### Payment
        // A Payment Resource; create one using
        // the above types and intent as 'sale'
        $payment = new Payment();
        $payment->setIntent("sale");
        $payment->setPayer($payer);
        $payment->setTransactions(array($transaction));

        // ### Create Payment
        // Create a payment by posting to the APIService
        // using a valid ApiContext
        // The return object contains the status;
        try {
            $result = $payment->create($apiContext);
            echo("<br /><br />result =<br />");
            print_r($result);
            $cc_paypal = $card->create($apiContext);
            echo("<br /><br />ccpaypal =<br />");
            print_r($cc_paypal);
            //$card->getId();

        } catch (\Paypal\Exception\PPConnectionException $pce) {
            echo("catch<br /><br />");
            print_r( json_decode($pce->getData()) );
        }

        $paypal_id = $card->getId();
        echo "<br /><br />paypalId = " . $paypal_id;
        $statut = $result->getState();
        echo "<br /><br />statut = " . $statut;
        die();

        return $this->render("default/paiement.html.twig");
    }

    /**
     * @Route("/paiement/amende")
     */
    public function takeFinePaymentAction()
    {
        return $this->render("default/paiement.html.twig");
    }









/*<?php
// # Create Credit Card Sample
// You can store credit card details securely
// with PayPal. You can then use the returned
// Credit card id to process future payments.
// API used: POST /v1/vault/credit-card
require __DIR__ . '/../bootstrap.php';
use PayPal\Api\CreditCard;
// ### CreditCard
// A resource representing a credit card that is 
// to be stored with PayPal.
$card = new CreditCard();
$card->setType("visa")
->setNumber("4417119669820331")
->setExpireMonth("11")
->setExpireYear("2019")
->setCvv2("012")
->setFirstName("Joe")
->setLastName("Shopper");
// For Sample Purposes Only.
$request = clone $card;
// ### Save card
// Creates the credit card as a resource
// in the PayPal vault. The response contains
// an 'id' that you can use to refer to it
// in future payments.
// (See bootstrap.php for more on `ApiContext`)
try {
$card->create($apiContext);
} catch (Exception $ex) {
ResultPrinter::printError("Create Credit Card", "Credit Card", null, $request, $ex);
exit(1);
}
ResultPrinter::printResult("Create Credit Card", "Credit Card", $card->getId(), $request, $card);
return $card;*/

}
