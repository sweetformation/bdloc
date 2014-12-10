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
     * @Route("/catalogue")
     */
    public function catalogRedirectAction()
    {
        // valeurs par défaut
        $params = array(
            "page" => 1,
            "orderBy" => "dateCreated",
            "orderDir" => "desc",
            "numPerPage" => 30,
            "keywords" => "none",
            "categories" => "all",
            "availability" => 0
        );

        $url = $this->generateUrl("bdloc_app_book_catalog", $params);
        return $this->redirect($url);
    }

    /**
     * @Route("/catalogue/{page}/{orderBy}/{orderDir}/{numPerPage}/{keywords}/{categories}/{availability}", requirements={"page" = "\d+"})
     */
    public function catalogAction($page, $orderBy, $orderDir, $numPerPage, $keywords, $categories, $availability)
    {
        if ($categories == "all")  {
            $categories_url = "all";
            $categories = array();
        }
        else {
            $categories_url = $categories;
            $categories = explode(',', $categories_url);
        }

        $request = $this->getRequest();
        // Si formulaire en POST 
        if ($request->getMethod() == 'POST') {
            // On récupère les données du formulaire
            $page = 1; //$request->request->get('page');
            $orderBy = $request->request->get('orderBy');
            $orderDir = $request->request->get('orderDir');
            $numPerPage = $request->request->get('numPerPage');
            $keywords = $request->request->get('keywords');
            if (empty($request->request->get('keywords'))) {
                $keywords = "none";
            }
            if (empty($request->request->get('categories'))) {
                $categories_url = "all";
                $categories = explode(',', $categories_url);
            } else {
                $categories_url = implode(',', $request->request->get('categories'));
                $categories = explode(',', $categories_url); 
            }
            if (!empty($request->request->get('availability'))) {
                $availability = $request->request->get('availability')[0];
            }

            // redirection
            $params = array(
                "page" => $page,
                "orderBy" => $orderBy,
                "orderDir" => $orderDir,
                "numPerPage" => $numPerPage,
                "keywords" => $keywords,
                "categories" => $categories_url,
                "availability" => $availability,
            );
            $url = $this->generateUrl("bdloc_app_book_catalog", $params);
            return $this->redirect($url);

        }

        // on les place dans un array passé à bookRepo
        $variables = array(
            "page" => $page,
            "orderBy" => $orderBy,
            "orderDir" => $orderDir,
            "numPerPage" => $numPerPage,
            "keywords" => $keywords,
            "categories" => $categories,
            "categories_url" => $categories_url,
            "availability" => $availability,
        );

        //print_r($variables);

        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $books = $bookRepo->findBooksBySearch($variables); 

        $nbPage = ceil($books->count() / $numPerPage); 
        $params['nbPage'] = $nbPage;
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
