<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Bdloc\AppBundle\Entity\FilterBook;
use Bdloc\AppBundle\Form\FilterBookType;
use Bdloc\AppBundle\Form\FilterBookPaginationType;

class BookController extends Controller
{
    /**
     * @Route("/catalogue/{page}/{orderBy}/{orderDir}/{numPerPage}/{keywords}/{categories}/{availability}")
     */
    public function catalogAction($page, $orderBy, $orderDir, $numPerPage, $keywords, $categories, $availability) 
    {
        // --------------------- FORMULAIRES FILTRES ---------------------
        $filterBook = new FilterBook();
        $filterForm = $this->createForm(new FilterBookType(), $filterBook, array("action" => $this->generateUrl('bdloc_app_book_handlefilterbook')));
        $filterPaginationForm = $this->createForm(new FilterBookPaginationType(), $filterBook, array("action" => $this->generateUrl('bdloc_app_book_handlefilterbook')));

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $books = $bookRepo->findCatalogBooks($filterBook); 
        //$books = $bookRepo->findAll(); 

        $params = array(
            "books" => $books,
            "filterForm" => $filterForm->createView(),
            "filterPaginationForm" => $filterPaginationForm->createView()
            );
       
        return $this->render("book/catalog.html.twig", $params);
    }

    /**
     * @Route("/catalogue/filtres")
     */
    public function handleFilterBookAction(Request $request)
    {
        $filterBook = new FilterBook();
        $filterForm = $this->createForm(new FilterBookType(), $filterBook);
        $filterForm->handleRequest($request);

        $params = $filterBook->getUrlParams();

        $url = $this->generateUrl('bdloc_app_book_catalog', $params);
        return $this->redirect($url);
    }

    /**
     * @Route("/catalogue/details")
     */
    public function detailsAction()
    {
        return $this->render("book/details.html.twig");
    }


















    /**
     * @Route("/catalogue/{page}")
     */
    /*public function catalog1Action($page) //Ajouter le $categorie dans les variables
    {
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $books = $bookRepo->findBooksBySearch($page); //Ajouter la $categorie dans les variables

        $params['books'] = $books;
        $params['page'] = $page;
        // $params['categ'] = $categorie;
        
        return $this->render("book/catalog1.html.twig", $params);
    }*/


    /**
     * @Route("/catalogue/test")
     */
    public function catalog0Action() 
    {
        // --------------------- FORMULAIRES FILTRES ---------------------
        $filterBook = new FilterBook();
        $filterForm = $this->createForm(new FilterBookType(), $filterBook);

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        //$books = $bookRepo->findCatalogBooks($filterBook); 
        $books = $bookRepo->findAll(); 

        $request = $this->getRequest();
        $filterForm->handleRequest($request);

        if ($filterForm->isValid()) {
            \Doctrine\Common\Util\Debug::dump($filterForm);
        }

        $params = array(
            "books" => $books,
            "filterForm" => $filterForm->createView()
            );
       
        return $this->render("book/catalog0.html.twig", $params);
    }


}
