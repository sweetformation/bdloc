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
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");

        $books = $bookRepo->findBooksBySearch();

        $params['books'] = $books;

        return $this->render("default/catalog.html.twig", $params);
    }

    /**
     * @Route("/catalogue/details")
     */
    public function detailsAction()
    {
        return $this->render("default/details.html.twig");
    }

}
