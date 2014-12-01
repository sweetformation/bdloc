<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PaymentController extends Controller
{
    /**
     * @Route("/paiement")
     */
    public function takeSubscriptionPaymentAction()
    {
        return $this->render("default/paiement.html.twig");
    }

    /**
     * @Route("/paiement/amende")
     */
    public function takeFinePaymentAction()
    {
        return $this->render("default/paiement.html.twig");
    }


}
