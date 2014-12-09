<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{

    /**
     * @Route("/catalogue/{page}")
     */
    public function catalogAction($page) //Ajouter le $categorie dans les variables
    {
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $books = $bookRepo->findBooksBySearch($page); //Ajouter la $categorie dans les variables

        $params['books'] = $books;
        $params['page'] = $page;
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


}
