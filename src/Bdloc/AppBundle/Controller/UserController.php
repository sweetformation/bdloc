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

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;

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

            // on termine l'hydratation de notre objet User avant enregistrement (dates, salt, token, roles, isEnabled)
            // dates prises en charge par Doctrine en lifecycle callbacks avec @ORM\PrePersist et @ORM\PreUpdate
            $user->setAddress( explode(',', $user->getAddress())[0] );
            $user->setRoles( array("ROLE_USER") );
            $user->setIsEnabled( 0 );  // on le passe à 1 en fin d'enregistrement, après étape 3 abonnement

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


            // Vider le formulaire et empêche la resoumission des données
            //return $this->redirect( $this->generateUrl("bdloc_app_user_register") );
            //
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
        }*/

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
            //
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

        // Demande à SF d'injecter les données du formulaire dans notre entité ($creditCard)
        $request = $this->getRequest();
        $creditCardForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($creditCardForm->isValid()) {

            die("pret pour enregistrement");

            // Update User
            $user->setIsEnabled( 1 );  // on le passe à 1 en fin d'enregistrement, après étape 3 abonnement
            
            // update en bdd pour CreditCardType
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($creditCard);  
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

            // Vider le formulaire et empêche la resoumission des données
            return $this->redirect( $this->generateUrl("bdloc_app_user_showsubsriptionpaymentform") );
            //
            // Redirection vers catalogue
            //return $this->redirect( $this->generateUrl("bdloc_app_book_catalog") );

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