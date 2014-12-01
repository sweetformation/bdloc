<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Bdloc\AppBundle\Form\RegisterType;
use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Util\StringHelper;
//use Bdloc\AppBundle\Form\ForgotPassword1Type;
//use Bdloc\AppBundle\Form\ForgotPassword2Type;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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

            // Pour loguer automatiquement qd on s'inscrit !
            // tiré de http://stackoverflow.com/questions/5886713/automatic-post-registration-user-authentication
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.context')->setToken($token);
                $this->get('session')->set('_security_main',serialize($token));


            // Vider le formulaire et empêche la resoumission des données
            return $this->redirect( $this->generateUrl("bdloc_app_user_register") );
            //
            // Redirection vers étape 2, choix du point relais
            //return $this->redirect( $this->generateUrl("bdloc_app_user_choosedropspot") );

        }

        $params['registerForm'] = $registerForm->createView();

        return $this->render("user/register.html.twig", $params);
    }

    /**
     * @Route("/abonnement/choix-point-relais")
     */
    public function chooseDropSpotAction() {

        $params = array();

        return $this->render("user/choose_drop_spot.html.twig", $params);
    }

    /**
     * @Route("/abonnement/choix-de-paiement")
     */
    public function showSubsriptionPaymentFormAction() {

        $params = array();

        return $this->render("user/show_subsription_payment_form.html.twig", $params);
    }

    /**
     * @Route("/mot-de-pass-oublie/etape1")
     */
    public function forgotPasswordStepOneAction() {

        $params = array();

        // --------------------- FORMULAIRE FORGOT PASSWORD 1 ---------------------
/*        $user = new User();
        $forgotPassword1Form = $this->createForm(new ForgotPassword1Type(), $user);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
        $request = $this->getRequest();
        $forgotPassword1Form->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($forgotPassword1Form->isValid()) {

            // on vérifie que l'email existe
            $userRepo = $this->getDoctrine()->getRepository("MCAppBundle:User");
            $userFound = $userRepo->findByEmail( $user->getEmail() );
            
            // si userFound on récupère la token correspondante et on envoie un email
            if ($userFound) {
                $params_message = array();
                $params_message['email'] = $userFound[0]->getEmail();
                $params_message['token'] = $userFound[0]->getToken();

                $links = $this->generateUrl("mc_app_user_forgotpasswordone", array("email" => $userFound[0]->getEmail(), "token" => $userFound[0]->getToken() ))

                //print_r($params_message);
                //die();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Nouveau mot de passe sur Tickets')
                    ->setFrom('admin@tickets.com')
                    ->setTo( $userFound[0]->getEmail() )
                    ->setContentType('text/html')
                    ->setBody($this->renderView('emails/forgot_password_email.html.twig', $params_message));
                $this->get('mailer')->send($message);

                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Un email de modification de mot de passe vous a été envoyé !'
                );

                // Vider le formulaire et empêche la resoumission des données
                return $this->redirect( $this->generateUrl("mc_app_user_forgotpasswordone") );
              
                // Redirection vers l'accueil
                //return $this->redirect( $this->generateUrl("mc_app_default_home") );
            }
            else {
                //die("personne");
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Email inconnu !'
                );
            }

        }

        $params['forgotPassword1Form'] = $forgotPassword1Form->createView();

        return $this->render("user/forgot_password1.html.twig", $params);*/
    }

    /**
     * @Route("/mot-de-pass-oublie/etape2/{email}/{token}")
     */
    public function forgotPasswordStepTwoAction($email, $token) {

        $params = array();

/*        // on récupère l'utilisateur pour vérifier que l'email et la token correspondent
        $userRepo = $this->getDoctrine()->getRepository("MCAppBundle:User");
        $userFound = $userRepo->findByEmail( $email );

        if ( $token == $userFound[0]->getToken() ) {

            // --------------------- FORMULAIRE FORGOT PASSWORD 2 ---------------------
            $user = new User();
            $forgotPassword2Form = $this->createForm(new ForgotPassword2Type(), $user);

            // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
            $request = $this->getRequest();
            $forgotPassword2Form->handleRequest($request);

            // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
            if ($forgotPassword2Form->isValid()) {
                //die("ok");

                // on régénère token et mot de passe hashé
                $stringHelper = new StringHelper(); 
                $user->setToken( $stringHelper->randomString(30) ); 

                // Hasher mot de passe
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $userFound[0]->getSalt());
                $user->setPassword($password);

                // on update en BDD
                $em = $this->getDoctrine()->getManager(); 
                $em->flush();
                
                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Votre mot de passe a été changé !'
                );

                // Vider le formulaire et empêche la resoumission des données
                return $this->redirect( $this->generateUrl("mc_app_user_forgotpasswordtwo") );
              
                // Redirection vers l'accueil
                //return $this->redirect( $this->generateUrl("mc_app_default_home") );


            }

            $params['forgotPassword2Form'] = $forgotPassword2Form->createView();

            return $this->render("user/forgot_password2.html.twig", $params);
        }
        else {
            // Redirection vers l'accueil
            return $this->redirect( $this->generateUrl("mc_app_default_home") );
        }*/
    }

}