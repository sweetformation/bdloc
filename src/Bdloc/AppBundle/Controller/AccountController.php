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
//use Bdloc\AppBundle\Form\DropSpotType;
//use Bdloc\AppBundle\Form\CreditCardType;

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

        $editInfoForm = $this->createForm(new EditInfoType(), $user);

        $request = $this->getRequest();
        $editInfoForm->handleRequest($request);

        if ($editInfoForm->isValid()) {

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

        $editPasswordForm = $this->createForm(new EditPasswordType(), $user);

        $request = $this->getRequest();
        $editPasswordForm->handleRequest($request);

        if ($editPasswordForm->isValid()) {

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

        $params['editPasswordForm'] = $editPasswordForm->createView();
        
        return $this->render("account/edit_password.html.twig", $params);
    }

    /**
     * @Route("/modifier-point-relais")
     */
    public function editDropSpotAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();
        
        return $this->render("account/edit_dropspot.html.twig");
    }

    /**
     * @Route("/modifier-informations-paiement")
     */
    public function editPaymentInfoAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();
        
        return $this->render("account/edit_payment_info.html.twig");
    }

    /**
     * @Route("/consulter-historique-location")
     */
    public function historyAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();
        
        return $this->render("account/history.html.twig");
    }

    /**
     * @Route("/payer-amende")
     */
    public function showFinePaymentFormAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();
        
        return $this->render("account/show_fine_payment_form.html.twig");
    }

    /**
     * @Route("/se-desabonner")
     */
    public function unsubscribeAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();
        
        return $this->render("account/unsubscribe.html.twig");
    }

    

}