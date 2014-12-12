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

use Symfony\Component\HttpFoundation\Response;
use Bdloc\AppBundle\Entity\Paiement;

class PaymentController extends Controller
{

    /**
     * @Route("/paiement/amende/{fineId}/{cout}", requirements={"cout" = "[\d\.]+"})
     */
    public function takeFinePaymentAction($fineId, $cout)
    {

        $user = $this->getUser();

        // Payer amendes
            // Récupère l'amende
        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");
        $fine = $fineRepo->find( $fineId );

            // récupérer paypalId de la carte de crédit
        $creditCardRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CreditCard");
        $creditCard = $creditCardRepo->findCreditCardWithUserId( $user->getId() );
        $ccppid = $creditCard->getPaypalId();

        // Utilisation du service PPUtility
        $ppu = $this->get('paypal_utility');
        $statut = $ppu->createPaymentUsingSavedCard($ccppid, $cout);

        if ($statut == "approved") {
                
            //Si Paiement Paypal validé
            $paiement = new Paiement();
            $paiement->setType("fine");
            $paiement->setAmount( $cout );
            $paiement->setUser( $user );  // On associe ce paiement à l'utilisateur concerné

            $fine->setDateModified(new \DateTime());
            $fine->setStatus("validé");

            // BDD
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($fine);  
            $em->persist($paiement); 
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Amende payée !'
            );
            return $this->redirect( $this->generateUrl("bdloc_app_account_showfinepaymentform") );
        }
        else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Problème lors de la transaction !'
            ); 
            return $this->redirect( $this->generateUrl("bdloc_app_account_showfinepaymentform") );
        }

        //return new Response("done");

    }

    /**
     * @Route("/paiement/renouvellement/{type}", requirements={"type" = "[A]|[M]"})
     */
    public function takePaymentAction($type)
    {

        $user = $this->getUser();

            // récupérer paypalId de la carte de crédit
        $creditCardRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CreditCard");
        $creditCard = $creditCardRepo->findCreditCardWithUserId( $user->getId() );
        $ccppid = $creditCard->getPaypalId();

        if ($type == "A") {
            $cout = $this->container->getParameter('prixAboA');
        }
        else if ($type == "M") {
            $cout = $this->container->getParameter('prixAboM');
        }
        // Utilisation du service PPUtility
        $ppu = $this->get('paypal_utility');
        $statut = $ppu->createPaymentUsingSavedCard($ccppid, $cout);

        if ($statut == "approved") {
                
            //Si Paiement Paypal validé
            $paiement = new Paiement();
            $paiement->setType("subscription");
            $paiement->setAmount( $cout );
            $paiement->setUser( $user );  // On associe ce paiement à l'utilisateur concerné

            if ($type == "A") {
                $user->setSubscriptionRenewal(new \DateTime("+1 year")); //date("Y-m-d", strtotime("+1 year"))
            }
            else if ($type == "M") {
                $user->setSubscriptionRenewal(new \DateTime("+1 month"));
            }
            // On repasse l'utilisateur en ROLE_USER
            $user->setRoles( array("ROLE_USER") );

            // BDD
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($user);  
            $em->persist($paiement); 
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Abonnement renouvelé !'
            );
            //return $this->redirect( $this->generateUrl("bdloc_app_book_catalogredirect") );
            return $this->redirect( $this->generateUrl("bdloc_app_user_login") );
        }
        else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Problème lors de la transaction !'
            ); 
            return $this->redirect( $this->generateUrl("bdloc_app_user_checksubscription") );
        }

    }



}
