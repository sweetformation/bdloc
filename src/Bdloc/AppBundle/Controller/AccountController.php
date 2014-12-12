<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Entity\CreditCard;
use Bdloc\AppBundle\Util\StringHelper;

use Bdloc\AppBundle\Form\EditInfoType;
use Bdloc\AppBundle\Form\EditPasswordType;

use Bdloc\AppBundle\Security\ChangePassword;
use Bdloc\AppBundle\Form\ChangePasswordType;
use Bdloc\AppBundle\Form\DropSpotType;
use Bdloc\AppBundle\Form\CreditCardType;
use Bdloc\AppBundle\Form\CreditCardChangeType;
use Bdloc\AppBundle\Form\UnsubscribeType;

//use Bdloc\AppBundle\Entity\Paiement;

class AccountController extends Controller
{
    /**
     * @Route("/")
     */
    public function homeAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();

        $params['user'] = $user;

        return $this->render("account/home.html.twig", $params);
    }

    /**
     * @Route("/modifier-informations-personnelles")
     */
    public function editInfoAction()
    {
        // récupère l'utilisateur en session
        $user_session = $this->getUser();
        //\Doctrine\Common\Util\Debug::dump($user_session);
        
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find( $user_session->getId() );

        $editInfoForm = $this->createForm(new EditInfoType(), $user, array('validation_groups' => array('editinfo', 'Default')));

        $request = $this->getRequest();
        $editInfoForm->handleRequest($request);

        if ($editInfoForm->isValid()) {

            // update en bdd
            $em = $this->getDoctrine()->getManager(); 
            $em->flush();
            // pour recharger BDD
            //$em->refresh( $user );
            $user = $userRepo->refreshUser($user);

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Modification(s) prise(s) en compte !'
            );
            
            // Redirection vers accueil compte
            return $this->redirect( $this->generateUrl("bdloc_app_account_home") );

        }

        $params['editInfoForm'] = $editInfoForm->createView();
        
        return $this->render("account/edit_info.html.twig", $params);
    }

    /**
     * @Route("/modifier-mot-de-passe")
     */
    public function editPasswordAction()
    {
        // récupère l'utilisateur en session
        $user_session = $this->getUser();
        //\Doctrine\Common\Util\Debug::dump($user_session);

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find( $user_session->getId() );


        //$changePassword = new ChangePassword();
        //$changePasswordForm = $this->createForm(new ChangePasswordType(), $changePassword);

        //$request = $this->getRequest();
        //$changePasswordForm->handleRequest($request);

        $currentPassword = $user->getPassword();

        $editPasswordForm = $this->createForm(new EditPasswordType(), $user);

        $request = $this->getRequest();
        $editPasswordForm->handleRequest($request);

        if ($editPasswordForm->isValid()) {
        //if ($changePasswordForm->isValid()) {

            //echo $changePasswordForm['oldPassword'];
            //echo $changePasswordForm['newPassword'];
            //die();
            $oldmdp = $editPasswordForm['currentpassword']->getData();
            // Hasher mot de passe
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $oldmdpHash = $encoder->encodePassword($oldmdp, $user->getSalt());

            if ($oldmdpHash === $currentPassword) {

                $stringHelper = new StringHelper();
                $user->setSalt( $stringHelper->randomString() ); 
                $user->setToken( $stringHelper->randomString(30) ); 

                // Hasher mot de passe
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);

                // update en bdd
                $em = $this->getDoctrine()->getManager(); 
                $em->flush();

                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Modification(s) prise(s) en compte !'
                );
                // Redirection vers accueil compte
                return $this->redirect( $this->generateUrl("bdloc_app_account_home") );
            }
            else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Mot de passe actuel invalide !'
                );
                // Redirection vers accueil compte
                return $this->redirect( $this->generateUrl("bdloc_app_account_editpassword") );
            }
        }

        $params['editPasswordForm'] = $editPasswordForm->createView();
        //$params['changePasswordForm'] = $changePasswordForm->createView();
        
        return $this->render("account/edit_password.html.twig", $params);
    }

    /**
     * @Route("/modifier-point-relais")
     */
    public function editDropSpotAction()
    {
        // récupère l'utilisateur en session
        $user_session = $this->getUser();

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find( $user_session->getId() );

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
            //$em->persist($user);
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Point relais modifié !'
            );

            // Vider le formulaire et empêche la resoumission des données
            //return $this->redirect( $this->generateUrl("bdloc_app_user_choosedropspot") );
            
            // Redirection vers étape 3, choix du paiement
            return $this->redirect( $this->generateUrl("bdloc_app_account_home") );

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
        
        return $this->render("account/edit_dropspot.html.twig", $params);
    }

    /**
     * @Route("/modifier-informations-paiement")
     */
    public function editPaymentInfoAction()
    {
        // récupère l'utilisateur en session
        $user_session = $this->getUser();

        $creditCardRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CreditCard");
        $creditCard = $creditCardRepo->findCreditCardWithUserId( $user_session->getId() );

        // Utilisation du service PPUtility
        $ppu = $this->get('paypal_utility');
        $ppu->setCreditCard($creditCard);
        $card = $ppu->getCreditCard();
        //print_r($card);

        $creditCard->setCreditCardType($card->getType());
        $creditCard->setCreditCardNumber($card->getNumber());
        //$creditCard->setExpirationDate($card->getValidUntil());
        $creditCard->setCreditCardLastName($card->getLast_name());
        $creditCard->setCreditCardFirstName($card->getFirst_name());

        $creditCardChangeForm = $this->createForm(new CreditCardChangeType(), $creditCard);

        $request = $this->getRequest();
        $creditCardChangeForm->handleRequest($request);

        if ($creditCardChangeForm->isValid()) {

                // On récupère les infos de paypal
                $paypalCC_id = $ppu->registerCreditCard();
                $creditCard->setPaypalId( $paypalCC_id );
                //$creditCard->setValidUntil( $creditCard->getExpirationDate() ); 

                // update en bdd pour CreditCard
                $em = $this->getDoctrine()->getManager(); 
                $em->persist($creditCard);   
                $em->flush();
        }

        $params['creditCardChangeForm'] = $creditCardChangeForm->createView();
        
        return $this->render("account/edit_payment_info.html.twig", $params);
    }

    /**
     * @Route("/consulter-historique-location")
     */
    public function historyAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $carts = $cartRepo->findUserValidatedCarts( $user );
        
        $userCarts = array();
        foreach ($carts as $cart) {
            $cart = $cartRepo->findBooksInCurrentCart( $cart->getId() );
            $userCarts[] = $cart;
        }

        $params['carts'] = $userCarts;

        return $this->render("account/history.html.twig", $params);
    }

    /**
     * @Route("/payer-amende")
     */
    public function showFinePaymentFormAction()
    {
        $params = array();
        $user = $this->getUser();

        // Récupère les amendes
        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");
        $fines = $fineRepo->findUserFines( $user );

        // Récupère les paiements
        $paymentRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Paiement");
        $payments = $paymentRepo->findUserPayments( $user );

        $params['fines'] = $fines;
        $params['payments'] = $payments;

        return $this->render("account/show_fine_payment_form.html.twig", $params);
    }

    /**
     * @Route("/se-desabonner")
     */
    public function unsubscribeAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();
        $params = array();

        // Si user a une amende, le rediriger vers page amende!
        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");
        $fines = $fineRepo->findUserFines( $user );

        if (!empty($fines)) {
            $url = $this->generateUrl("bdloc_app_account_showfinepaymentform");
            return $this->redirect($url);
        }

        $byeForm = $this->createForm(new UnsubscribeType(), $user);
        $request = $this->getRequest();
        $byeForm->handleRequest($request);

        if ($byeForm->isValid()) {
            // récupération du message
            $message = $byeForm["message"]->getData();
            $params_message = array(
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
                "firstName" => $user->getFirstName(),
                "lastName" => $user->getLastName(),
                "message" => $message
            );

            // Envoyer un mail à l'admin
            $messageMail = \Swift_Message::newInstance()
                ->setSubject('Désabonnement sur BDloc')
                ->setFrom('admin@bdloc.com')
                ->setTo( 'sweetformation@yahoo.fr' )
                ->setContentType('text/html')
                ->setBody($this->renderView('emails/deliberately_unsubscribe_email.html.twig', $params_message));
            $this->get('mailer')->send($messageMail);
            
            // User->setIsEnabled à 0 !
            $user->setIsEnabled(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $em->refresh( $user );

            // rediriger vers default home
            return $this->redirect($this->generateUrl("bdloc_app_default_home"));

        }

        $params['byeForm'] = $byeForm->createView();

        return $this->render("account/unsubscribe.html.twig", $params);
    }

    

}