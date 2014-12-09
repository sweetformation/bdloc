<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bdloc\AppBundle\Entity\FilterBook;
use Bdloc\AppBundle\Form\FilterBookType;
use Bdloc\AppBundle\Form\FilterBookPaginationType;

class BookController extends Controller
{

    /**
    * Alias de la route catalogue (simule un formulaire de filtres vide en fait)
    * @Route("/catalogue/")
    */
    public function catalog(){
        return $this->redirect($this->generateUrl("bdloc_app_book_handlefilterbook"));
    }


    /**
     * @Route("/catalogue/{page}/{orderBy}/{orderDir}/{numPerPage}/{categories}/{availability}/{keywords}/")
     */
    public function showCatalogAction($page, $orderBy, $orderDir, $numPerPage, $keywords, $categories, $availability)
    {
        
        $serieRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Serie");
        
        $filterBook = new FilterBook($page, $orderBy, $orderDir, $numPerPage, $keywords, explode(",", $categories), $availability);

        $repo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $books = $repo->findCatalogBooks( $filterBook );

        $filterForm = $this->createForm(new FilterBookType(), $filterBook, array(
            "action" => $this->generateUrl("bdloc_app_book_handlefilterbook"))
        );
        $filterPaginationForm = $this->createForm(new FilterBookPaginationType(), $filterBook, array(
            "action" => $this->generateUrl("bdloc_app_book_handlefilterbook"))
        );

        $params = array(
            "books"                     => $books,
            "filterForm"                => $filterForm->createView(),
            "filterPaginationForm"      => $filterPaginationForm->createView()
        );
        return $this->render("bd/catalog.html.twig", $params);
    }


    /**
    * @Route("/catalogue/filtres/")
    */
    public function handleFilterBookAction(Request $request)
    {
        $filterBook = new FilterBook();
        $filterForm = $this->createForm(new FilterBookType(), $filterBook);
        $filterForm->handleRequest($request);

        $params = $filterBook->getUrlParams();

        $url = $this->generateUrl("bdloc_app_book_showcatalog", $params);
        return $this->redirect($url);
    }

    /**
    *
    * @Route("/details/{id}/")
    */
    public function detailsAction($id){
        $bookRepository = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepository->find($id);

        dump($book);

        $params = array("book" => $book);
        return $this->render("bd/details.html.twig", $params);
    }


}
