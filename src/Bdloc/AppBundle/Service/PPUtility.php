<?php

namespace Bdloc\AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use PayPal\Api\Amount;
use PayPal\Api\CreditCard as PaypalCreditCard;
use PayPal\Api\Payer; 
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;

class PPUtility {

    // On passe le service paypal via le constructor
    private $apiContext;

    // setter injection
    private $creditCard;
    private $prixAbo;

    // from constructor (inutilisé)
    private $prixAboM;
    private $prixAboA;



    public function setCreditCard($creditCard) {
        $this->creditCard = $creditCard;
    }

    public function setPrixAbo($prixAbo) {
        $this->prixAbo = $prixAbo;
    }

    public function __construct($paypal, $prixAboM, $prixAboA) {
        $this->apiContext = $paypal->getApiContext();
        $this->prixAboM = $prixAboM;
        $this->prixAboA = $prixAboA;
    }

    public function createPayment() {
        //$apiContext = $this->paypal->getApiContext();

        // ### CreditCard
        // A resource representing a credit card that can be
        // used to fund a payment.
        $card = new PaypalCreditCard();
        $card->setType($this->creditCard->getCreditCardType());
        $card->setNumber($this->creditCard->getCreditCardNumber());
        $card->setExpire_month($this->creditCard->getExpirationDate()->format("m"));
        $card->setExpire_year($this->creditCard->getExpirationDate()->format("Y"));
        $card->setCvv2($this->creditCard->getCodeCVC());
        $card->setFirst_name($this->creditCard->getCreditCardFirstName());
        $card->setLast_name($this->creditCard->getCreditCardLastName());

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
        $amount->setTotal($this->prixAbo);

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
            $resultat = $payment->create($this->apiContext);
            //echo("<br /><br />result =<br />");
            //print_r($resultat);

        } catch (\Paypal\Exception\PPConnectionException $pce) {
            print_r( json_decode($pce->getData()) );
        }

        $statut = $resultat->getState();
        //echo "<br /><br />statut = " . $statut;
        //die();
        return $statut;
    }

    public function registerCreditCard() {
        // from https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/vault/CreateCreditCard.php
        // # Create Credit Card Sample
        // You can store credit card details securely
        // with PayPal. You can then use the returned
        // Credit card id to process future payments.
        // API used: POST /v1/vault/credit-card

        //$apiContext = $this->paypal->getApiContext();

        // ### CreditCard
        // A resource representing a credit card that is 
        // to be stored with PayPal.
        $card = new PaypalCreditCard();
        $card->setType($this->creditCard->getCreditCardType());
        $card->setNumber($this->creditCard->getCreditCardNumber());
        $card->setExpire_month($this->creditCard->getExpirationDate()->format("m"));
        $card->setExpire_year($this->creditCard->getExpirationDate()->format("Y"));
        $card->setCvv2($this->creditCard->getCodeCVC());
        $card->setFirst_name($this->creditCard->getCreditCardFirstName());
        $card->setLast_name($this->creditCard->getCreditCardLastName());

        // ### Save card
        // Creates the credit card as a resource
        // in the PayPal vault. The response contains
        // an 'id' that you can use to refer to it
        // in future payments.
        // (See bootstrap.php for more on `ApiContext`)
        try {
            $paypalCC = $card->create($this->apiContext);
            //echo("<br /><br />ccpaypal =<br />");
            //print_r($paypalCC);

        } catch (\Paypal\Exception\PPConnectionException $pce) {
            print_r( json_decode($pce->getData()) );
        }

        $paypalCC_id = $card->getId();

        return $paypalCC_id;
    }

    /*public function getCreditCard() {
        // from https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/vault/CreateCreditCard.php
        // # Create Credit Card Sample
        // You can store credit card details securely
        // with PayPal. You can then use the returned
        // Credit card id to process future payments.
        // API used: POST /v1/vault/credit-card

        //$apiContext = $this->paypal->getApiContext();

        // ### CreditCard
        // A resource representing a credit card that is 
        // to be stored with PayPal.
        $card = new PaypalCreditCard();
        $card->setType($this->creditCard->getCreditCardType());
        $card->setNumber($this->creditCard->getCreditCardNumber());
        $card->setExpire_month($this->creditCard->getExpirationDate()->format("m"));
        $card->setExpire_year($this->creditCard->getExpirationDate()->format("Y"));
        $card->setCvv2($this->creditCard->getCodeCVC());
        $card->setFirst_name($this->creditCard->getCreditCardFirstName());
        $card->setLast_name($this->creditCard->getCreditCardLastName());

        // ### Save card
        // Creates the credit card as a resource
        // in the PayPal vault. The response contains
        // an 'id' that you can use to refer to it
        // in future payments.
        // (See bootstrap.php for more on `ApiContext`)
        try {
            $card = CreditCard::get($card->getId(), $this->apiContext);
            //echo("<br /><br />card =<br />");
            //print_r($card);

        } catch (\Paypal\Exception\PPConnectionException $pce) {
            print_r( json_decode($pce->getData()) );
        }

        return $card;
    }*/

}