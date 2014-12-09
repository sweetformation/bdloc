<?php

    namespace Bdloc\AppBundle\Service;


    class PPSubscription {

        private $paypal;

        public function __construct($paypal){
            $this->paypal = $paypal;
        }


        public function createPayment(){
             //see kmj/paypalbridgebundle
             $apiContext = $this->paypal->getApiContext();

            // ### CreditCard
            // A resource representing a credit card that can be
            // used to fund a payment.
            $card = new CreditCard();
            $card->setType("visa");
            $card->setNumber("4417119669820331");
            $card->setExpire_month("11");
            $card->setExpire_year("2018");
            $card->setCvv2("987");
            $card->setFirst_name("Joe");
            $card->setLast_name("Shopper");

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
                 print_r($result);

             } catch (\Paypal\Exception\PPConnectionException $pce) {
                 print_r( json_decode($pce->getData()) );
             }
        }

    }