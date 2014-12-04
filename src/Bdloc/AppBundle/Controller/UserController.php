<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Bdloc\AppBundle\Form\RegisterType;
use Bdloc\AppBundle\Form\DropSpotType;
use Bdloc\AppBundle\Form\CreditCardType;
use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Entity\CreditCard;
use Bdloc\AppBundle\Util\StringHelper;
use Bdloc\AppBundle\Util\GpsHelper;
use Bdloc\AppBundle\Form\ForgotPasswordStepOneType;
use Bdloc\AppBundle\Form\ForgotPasswordStepTwoType;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

//use PayPal\Rest\ApiContext;
//use PayPal\Auth\OAuthTokenCredential;
//use PayPal\Api\Payment;

/*use PayPal\Api\Amount;
use PayPal\Api\CreditCard as PaypalCreditCard;
use PayPal\Api\Payer; 
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;*/

use Bdloc\AppBundle\Entity\Paiement;

class UserController extends Controller
{
    /**
     * @Route("/login")
     */
    public function loginAction(Request $request) {

        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        $params = array(
            // last username entered by the user
            'last_username' => $lastUsername,
            'error'         => $error,
        );

        return $this->render("user/login.html.twig", $params);
    }

    /**
     * @Route("/abonnement/inscription")
     */
    public function registerAction() {

        $params = array();

        // --------------------- FORMULAIRE INSCRIPTION ---------------------
        $user = new User();
        $registerForm = $this->createForm(new RegisterType(), $user, array('validation_groups' => array('registration', 'Default')));

        // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
        $request = $this->getRequest();
        $registerForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($registerForm->isValid()) {

            // on termine l'hydratation de notre objet User avant enregistrement (dates, salt, token, roles, isEnabled, subscriptionType)
            // dates prises en charge par Doctrine en lifecycle callbacks avec @ORM\PrePersist et @ORM\PreUpdate
            $user->setAddress( explode(',', $user->getAddress())[0] );
            $user->setRoles( array("ROLE_USER") );
            $user->setIsEnabled( 0 );  // on le passe à 1 en fin d'enregistrement, après étape 3 abonnement
            $user->setSubscriptionType("0");
            $user->setSubscriptionRenewal(new \DateTime());

            // salt (tjs avant de hasher le mdp!!) & token avec notre propre classe
            $stringHelper = new StringHelper();
            $user->setSalt( $stringHelper->randomString() ); 
            $user->setToken( $stringHelper->randomString(30) ); 

            // Hasher mot de passe
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            // sauvegarder en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);  
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Bienvenue !'
            );

            // Mettre en session l'id de l'utilisateur
            $this->get('session')->set('id', $user->getId());

            // Redirection vers étape 2, choix du point relais
            return $this->redirect( $this->generateUrl("bdloc_app_user_choosedropspot") );

        }

        $params['registerForm'] = $registerForm->createView();

        return $this->render("user/register.html.twig", $params);
    }

    /**
     * @Route("/abonnement/choix-point-relais")
     */
    public function chooseDropSpotAction() {

        $params = array();

        // Script pour ajout gps coordinates dans bdd
        /*$gpsCoord = new GpsHelper();
        // récupère tous les points relais
        $dropSpotRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:DropSpot");
        $dropSpots = $dropSpotRepo->findAll();

        foreach ($dropSpots as $dropSpot) {
            $adresse = $dropSpot->getAddress() . ", Paris";
            $coords = $gpsCoord->getCoordinates( $adresse );
            $dropSpot->setLatitude($coords["lat"]);
            $dropSpot->setLongitude($coords["lng"]);
            
            // update en bdd pour DropSpotType
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($dropSpot);  
            $em->flush();
        }
        die();*/
        
        // --------------------- FORMULAIRE POINT RELAIS ---------------------
        // Récupère l'id utilisateur
        $id = $this->get('session')->get('id');

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find( $id );

        //\Doctrine\Common\Util\Debug::dump($user);

        if (empty($user)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Utilisateur non trouvé !'
            );     
            return $this->redirect( $this->generateUrl("bdloc_app_user_register") );       
        }

        // récupère l'adresse de l'utilisateur
        $add_user = $user->getAddress();

        $dropspotForm = $this->createForm(new DropSpotType(), $user);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
        $request = $this->getRequest();
        $dropspotForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($dropspotForm->isValid()) {

            // update en bdd pour DropSpotType
            $em = $this->getDoctrine()->getManager(); 
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Point relais ajouté !'
            );

            // Vider le formulaire et empêche la resoumission des données
            //return $this->redirect( $this->generateUrl("bdloc_app_user_choosedropspot") );
            
            // Redirection vers étape 3, choix du paiement
            return $this->redirect( $this->generateUrl("bdloc_app_user_showsubsriptionpaymentform") );

        }

        $params['dropspotForm'] = $dropspotForm->createView();

        // Récupération des coord gps des points relais
        $dropSpotRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:DropSpot");
        $dropSpots = $dropSpotRepo->findAll();
        foreach ($dropSpots as $dropSpot) {
            $dropTab["nom"] = $dropSpot->getName();
            $dropTab["lat"] = $dropSpot->getLatitude();
            $dropTab["lng"] = $dropSpot->getLongitude();
            $dropTab["add"] = $dropSpot->getAddress();
            $dropTab["zip"] = $dropSpot->getZip();
            $params['dropSpots'][] = $dropTab;
        }

        $params['add_user'] = $add_user;
        //print_r($params);

        return $this->render("user/choose_drop_spot.html.twig", $params);
    }

    /**
     * @Route("/abonnement/choix-de-paiement")
     */
    public function showSubsriptionPaymentFormAction() {

        $params = array();

        // --------------------- FORMULAIRE PAIEMENT ---------------------
        // Récupère l'id utilisateur
        $id = $this->get('session')->get('id');

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find( $id );

        //\Doctrine\Common\Util\Debug::dump($user);

        if (empty($user)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Utilisateur non trouvé !'
            );     
            return $this->redirect( $this->generateUrl("bdloc_app_user_register") );       
        }

        $creditCard = new CreditCard();
        $creditCardForm = $this->createForm(new CreditCardType(), $creditCard);
        //$creditCardForm = $this->createForm(new CreditCardType(), $creditCard, array("action" => $this->generateUrl("bdloc_app_payment_takesubscriptionpayment")));

        // Demande à SF d'injecter les données du formulaire dans notre entité ($creditCard)
        $request = $this->getRequest();
        $creditCardForm->handleRequest($request);


        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($creditCardForm->isValid()) {

            // On récupère le prix avec le bouton radio rajouté manuellement dans le form!
            $typeAbo = $creditCardForm["abonnement"]->getData();
            if ($typeAbo == "A") {
                $prixAbo = $this->container->getParameter('prixAboA');;
            }
            else if ($typeAbo == "M") {
                $prixAbo = $this->container->getParameter('prixAboM');;
            }
            //echo "<br /><br />prixAbo = " . $prixAbo;
            //die();
            
            // Utilisation du service PPUtility
            $ppu = $this->get('paypal_utility');
            $ppu->setCreditCard($creditCard);
            $ppu->setPrixAbo($prixAbo);
            $statut = $ppu->createPayment();
            $paypalCC_id = $ppu->registerCreditCard();

/*        // -----------------------------------------------------------------------------------------------
        // ------------------------------------------ PAYPAL ---------------------------------------------
        // -----------------------------------------------------------------------------------------------
            //see kmj/paypalbridgebundle
            $apiContext = $this->get('paypal')->getApiContext();

            // ### CreditCard
            // A resource representing a credit card that can be
            // used to fund a payment.
            $card = new PaypalCreditCard();
            $card->setType($creditCard->getCreditCardType());
            $card->setNumber($creditCard->getCreditCardNumber());
            $card->setExpire_month($creditCard->getExpirationDate()->format("m"));
            $card->setExpire_year($creditCard->getExpirationDate()->format("Y"));
            $card->setCvv2($creditCard->getCodeCVC());
            $card->setFirst_name($creditCard->getCreditCardFirstName());
            $card->setLast_name($creditCard->getCreditCardLastName());

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
            $amount->setTotal($prixAbo);

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
                $resultat = $payment->create($apiContext);
                //echo("<br /><br />result =<br />");
                //print_r($resultat);
                $cc_paypal = $card->create($apiContext);
                //echo("<br /><br />ccpaypal =<br />");
                //print_r($cc_paypal);

            } catch (\Paypal\Exception\PPConnectionException $pce) {
                print_r( json_decode($pce->getData()) );
            }

            $paypalCC_id = $card->getId();
            //echo "<br /><br />paypalId = " . $paypalCC_id;
            $statut = $resultat->getState();
            //echo "<br /><br />statut = " . $statut;
            //die();
        // -----------------------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------*/


            if ($statut == "approved") {
                
                //Si Paiement Paypal validé
                $paiement = new Paiement();
                $paiement->setType("subscription");
                $paiement->setAmount( $prixAbo );
                $paiement->setUser( $user );  // On associe ce paiement à l'utilisateur concerné

                // Update User
                $user->setIsEnabled( 1 );  // on le passe à 1 en fin d'enregistrement, après étape 3 abonnement
                $user->setSubscriptionType($typeAbo);
                //echo "<br />ok pour subscriptiontype";
                //$user->setSubscriptionRenewal(date("Y-m-d", strtotime("+1 month")));
                if ($typeAbo == "A") {
                    $user->setSubscriptionRenewal(new \DateTime("+1 year")); //date("Y-m-d", strtotime("+1 year"))
                }
                else if ($typeAbo == "M") {
                    $user->setSubscriptionRenewal(new \DateTime("+1 month"));
                }
                //echo "<br />ok pour renewal";
                
                // On associe la carte de crédit à l'utilisateur
                $creditCard->setUser( $user );

                // On récupère les infos de paypal
                $creditCard->setPaypalId( $paypalCC_id );
                //echo "<br />ok pour paypalId";
                //echo "<br />getExpirationDate = <br />";
                //var_dump($creditCard->getExpirationDate());
                $creditCard->setValidUntil( $creditCard->getExpirationDate() );  //->format("Y-m-d")
                //echo "<br />ok pour validUntil";
                //echo "<br />getvalidUntil = <br />";
                //var_dump($creditCard->getValidUntil());



                // update en bdd pour CreditCardType
                $em = $this->getDoctrine()->getManager(); 
                //echo "<br />manager choppé";
                $em->persist($creditCard);  
                $em->persist($paiement);
                //echo "<br />persist x2 ok";  
                $em->flush();

                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Abonnement validé !'
                );

                // Pour loguer automatiquement qd on s'inscrit ! ATTENTION A FAIRE A l'ETAPE 3 !!!!!!!!!!!
                // 
                // tiré de http://stackoverflow.com/questions/5886713/automatic-post-registration-user-authentication
                    /*$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->get('security.context')->setToken($token);
                    $this->get('session')->set('_security_main',serialize($token));*/

                // tiré de http://stackoverflow.com/questions/9550079/how-to-programmatically-login-authenticate-a-user
                    $token = new UsernamePasswordToken($user, $user->getPassword(), "secured_area", $user->getRoles());
                    $this->get("security.context")->setToken($token);

                    // déclenche l'évènement de login
                    $event = new InteractiveLoginEvent($request, $token);
                    $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                // Redirection vers catalogue
                //return $this->redirect( $this->generateUrl("bdloc_app_book_catalog") );
                return $this->redirect( $this->generateUrl("bdloc_app_default_home") );
            }
            else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Problème lors de la transaction !'
                ); 
                return $this->redirect( $this->generateUrl("bdloc_app_user_showsubsriptionpaymentform") );
            }

        }

        $params['creditCardForm'] = $creditCardForm->createView();
        return $this->render("user/show_subsription_payment_form.html.twig", $params);
    }

    /**
     * @Route("/mot-de-pass-oublie/etape1")
     */
    public function forgotPasswordStepOneAction() {

        $params = array();

        // --------------------- FORMULAIRE FORGOT PASSWORD 1 ---------------------
        $user = new User();
        $forgotPasswordStepOneForm = $this->createForm(new ForgotPasswordStepOneType(), $user);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
        $request = $this->getRequest();
        $forgotPasswordStepOneForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($forgotPasswordStepOneForm->isValid()) {

            // on vérifie que l'email existe
            $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
            $userFound = $userRepo->findOneByEmail( $user->getEmail() );
            
            // si userFound on récupère la token correspondante et on envoie un email
            if ($userFound) {
                $params_message = array();
                //$params_message['email'] = $userFound->getEmail();
                //$params_message['token'] = $userFound->getToken();

                $links = $this->generateUrl("bdloc_app_user_forgotpasswordsteptwo", array("email" => $userFound->getEmail(), "token" => $userFound->getToken()), true);
                $params_message['links'] = $links;
                //print_r($params_message);
                //die();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Nouveau mot de passe sur BDLOC')
                    ->setFrom('admin@bdloc.com')
                    ->setTo( $userFound->getEmail() )
                    ->setContentType('text/html')
                    ->setBody($this->renderView('emails/forgot_password_email.html.twig', $params_message));
                $this->get('mailer')->send($message);

                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Un email de modification de mot de passe vous a été envoyé !'
                );

                // Vider le formulaire et empêche la resoumission des données
                //return $this->redirect( $this->generateUrl("bdloc_app_user_forgotpasswordstepone") );
              
                // Redirection vers l'accueil
                return $this->redirect( $this->generateUrl("bdloc_app_default_home") );
            }
            else {
                //die("personne");
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Email inconnu !'
                );
            }

        }

        $params['forgotPasswordStepOneForm'] = $forgotPasswordStepOneForm->createView();

        return $this->render("user/forgot_password_step_one.html.twig", $params);
    }

    /**
     * @Route("/mot-de-pass-oublie/etape2/{email}/{token}")
     */
    public function forgotPasswordStepTwoAction($email, $token) {

        $params = array();

        // on récupère l'utilisateur pour vérifier que l'email et la token correspondent
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $userFound = $userRepo->findOneByEmail( $email );

        if ( $token === $userFound->getToken() ) {

            // --------------------- FORMULAIRE FORGOT PASSWORD 2 ---------------------
            //$user = new User();
            $forgotPasswordStepTwoForm = $this->createForm(new ForgotPasswordStepTwoType(), $userFound);

            // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
            $request = $this->getRequest();
            $forgotPasswordStepTwoForm->handleRequest($request);

            // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
            if ($forgotPasswordStepTwoForm->isValid()) {
                //die("ok");

                // on régénère token et mot de passe hashé
                $stringHelper = new StringHelper(); 
                $userFound->setToken( $stringHelper->randomString(30) ); 

                // Hasher mot de passe
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($userFound);
                $password = $encoder->encodePassword($userFound->getPassword(), $userFound->getSalt());
                $userFound->setPassword($password);

                // on update en BDD
                $em = $this->getDoctrine()->getManager(); 
                $em->flush();
                
                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Votre mot de passe a été changé !'
                );
              
                // Redirection vers l'accueil
                return $this->redirect( $this->generateUrl("bdloc_app_default_home") );


            }

            $params['forgotPasswordStepTwoForm'] = $forgotPasswordStepTwoForm->createView();

            return $this->render("user/forgot_password_step_two.html.twig", $params);
        }
        else {
            // Redirection vers l'accueil
            return $this->redirect( $this->generateUrl("bdloc_app_default_home") );
        }
    }

}