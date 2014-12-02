<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BookController extends Controller
{
    /**
     * @Route("/catalogue/{filter1}")
     */
    public function catalogAction($alphabetique, $categorie, $disponible, $nbBd, $recherche, $page)
    {
        return $this->render("default/catalogue.html.twig");
    }

    /**
     * @Route("/catalogue/details/{id}")
     */
    public function detailsAction($id)
    {
        return $this->render("default/details.html.twig");
    }

}
