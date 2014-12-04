<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Entity\CreditCard;
use Bdloc\AppBundle\Util\StringHelper;

use Bdloc\AppBundle\Form\RegisterType;
use Bdloc\AppBundle\Form\DropSpotType;
use Bdloc\AppBundle\Form\CreditCardType;

use Bdloc\AppBundle\Entity\Paiement;

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
        $user = $this->getUser();
        
        return $this->render("account/edit_info.html.twig");
    }

    /**
     * @Route("/modifier-mot-de-passe")
     */
    public function editPasswordAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();
        
        return $this->render("account/edit_password.html.twig");
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