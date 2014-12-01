<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CartController extends Controller
{
    /**
     * @Route("/panier/{id}/ajout/bd")
     */
    public function addBookAction($id)
    {
        return $this->render("default/panier.html.twig");
    }

    /**
     * @Route("/panier/{id}/supprime/bd")
     */
    public function removeBookAction($id)
    {
        return $this->render("default/panier.html.twig");
    }

    /**
     * @Route("/panier/{id}/")
     */
    public function recapAction($id)
    {
        return $this->render("default/panier.html.twig");
    }

    /**
     * @Route("/panier/{id}/supprime/bd")
     */

    public function validateAction($id)
    {
        return $this->render("default/panier.html.twig");
    }

}
