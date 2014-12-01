<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BookController extends Controller
{
    /**
     * @Route("/catalogue")
     */
    public function catalogAction()
    {
        return $this->render("default/catalogue.html.twig");
    }

    /**
     * @Route("/catalogue/details")
     */
    public function detailsAction()
    {
        return $this->render("default/details.html.twig");
    }

}
