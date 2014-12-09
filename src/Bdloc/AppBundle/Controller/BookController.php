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
     * @Route("/catalogue/{page}/{orderBy}/{orderDir}/{numPerPage}/{keywords}/{categories}/{availability}", defaults={"page" = 1, "orderBy"= "dateCreated", "orderDir"= "desc", "numPerPage"= 30, "keywords"= "", "categories"= "", "availability"= 0})
     */
    public function catalogAction($page, $orderBy, $orderDir, $numPerPage, $keywords, $categories, $availability)
    {
        // On récupère les données du formulaire
        $request = $this->getRequest();
        $page = $request->get('page');
        $orderBy = $request->get('orderBy');
        $orderDir = $request->get('orderDir');
        $numPerPage = $request->get('numPerPage');
        $keywords = $request->get('keywords');
        $categories = $request->get('categories');
        $availability = $request->get('availability');

        // on les place dans un array passé à bookRepo
        $variables = array(
            "page" => $page,
            "orderBy" => $orderBy,
            "orderDir" => $orderDir,
            "numPerPage" => $numPerPage,
            "keywords" => $keywords,
            "categories" => $categories,
            "availability" => $availability,
        );

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        //$books = $bookRepo->findBooksBySearch($page); 
        $books = $bookRepo->findBooksBySearch($variables); 

        $params['books'] = $books;
        $params['variables'] = $variables;
        // $params['categ'] = $categorie;

        // récupère le nombre d'items dans panier
        $user = $this->getUser();
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );
        // récupère les id des items dans panier
        $booksIdInCart = array();
        if ($cart != null) {
            $cartItems = $cart->getCartItems();
            foreach ($cartItems as $cartItem) {
                $booksIdInCart[] = $cartItem->getBook()->getId();
            }
        }

        $params['booksIdInCart'] = $booksIdInCart;
        
        return $this->render("book/catalog.html.twig", $params);
    }

    /**
     * @Route("/catalogue/details/{id}")
     */
    public function detailsAction($id) {

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->find($id);

        // récupère le nombre d'items dans panier
        $user = $this->getUser();
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );
        // récupère les id des items dans panier
        $booksIdInCart = array();
        if ($cart != null) {
            $cartItems = $cart->getCartItems();
            foreach ($cartItems as $cartItem) {
                $booksIdInCart[] = $cartItem->getBook()->getId();
            }
        }

        $params['booksIdInCart'] = $booksIdInCart;

        $params["book"] = $book;

        return $this->render("book/details.html.twig", $params);
    }

    /**
     * @Route("/cataloguefiltre/{page}/{orderBy}/{orderDir}/{numPerPage}/{keywords}/{categories}/{availability}")
     */
    public function catalogFiltreAction($page, $orderBy, $orderDir, $numPerPage, $keywords, $categories, $availability) 
    {
        // --------------------- FORMULAIRES FILTRES ---------------------
        $filterBook = new FilterBook();
        //$filterBook = new FilterBook($page, $orderBy, $orderDir, $numPerPage, $keywords, explode(",", $categories), $availability);
        
        $filterForm = $this->createForm(new FilterBookType(), $filterBook);
        $filterPaginationForm = $this->createForm(new FilterBookPaginationType(), $filterBook);
        //$filterForm = $this->createForm(new FilterBookType(), $filterBook, array(
        //    "action" => $this->generateUrl('bdloc_app_book_handlefilterbook'))
        //);
        //$filterPaginationForm = $this->createForm(new FilterBookPaginationType(), $filterBook, array(
        //    "action" => $this->generateUrl('bdloc_app_book_handlefilterbook'))
        //);

        // valeurs par défaut
        $page = 1;
        $orderBy= "dateCreated";
        $orderDir= "desc";
        $numPerPage= 10;
        $keywords= "none";
        $categories= array();
        $availability= 0;

        $request = $this->getRequest();
        $filterForm->handleRequest($request);
        $filterPaginationForm->handleRequest($request);

        /*if ($filterPaginationForm->isValid()) {
            dump($filterBook);
        }
        if ($filterForm->isValid()) {
            dump($filterBook);
        }*/

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $books = $bookRepo->findCatalogBooks($filterBook); 
        //$books = $bookRepo->findAll(); 

            // récupère le nombre d'items dans panier
            $user = $this->getUser();
            $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
            $cart = $cartRepo->findUserCurrentCart( $user );
            // récupère les id des items dans panier
            $booksIdInCart = array();
            if ($cart != null) {
                $cartItems = $cart->getCartItems();
                foreach ($cartItems as $cartItem) {
                    $booksIdInCart[] = $cartItem->getBook()->getId();
                }
            }

        $params = array(
            "books" => $books,
            "filterForm" => $filterForm->createView(),
            "filterPaginationForm" => $filterPaginationForm->createView(),
            "booksIdInCart" => $booksIdInCart
            );
       
        return $this->render("book/catalogfiltre.html.twig", $params);
    }

    /**
     * @Route("/cataloguefiltre/filtres")
     */
    public function handleFilterBookAction(Request $request)
    {
        $filterBook = new FilterBook();
        $filterForm = $this->createForm(new FilterBookType(), $filterBook);
        $filterForm->handleRequest($request);

        $params = $filterBook->getUrlParams();

        $url = $this->generateUrl('bdloc_app_book_catalogfiltre', $params);
        return $this->redirect($url);
    }


}
