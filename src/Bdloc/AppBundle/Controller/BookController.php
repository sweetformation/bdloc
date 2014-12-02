<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BookController extends Controller
{
    /**
     * @Route("/catalogue/{page}")
     */
    public function catalogAction($page) //Ajouter le $categorie dans les variables
    {
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        

        $books = $bookRepo->findBooksBySearch($page); //Ajouter le $categorie dans les variables

        $params['books'] = $books;
        $params['page'] = $page;
        // $params['categ'] = $categorie;
        

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
