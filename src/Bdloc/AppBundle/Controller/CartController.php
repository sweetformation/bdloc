<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bdloc\AppBundle\Entity\Cart;
use Bdloc\AppBundle\Entity\CartItem;

class CartController extends Controller
{
    /**
     * @Route("/ajout-bd/{book_id}")
     */
    public function addBookAction($book_id)
    {
        $params = array();

        // récupère l'utilisateur en session
        $user = $this->getUser();

        //  check dans table cart s'il a un panier en cours (statut = en cours, validé, vidé)
        //      si oui, récupérer le panier, sinon créer un panier
        //      puis l'hydrater et l'enregistrer
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );

        // récupère le book à ajouter
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->find( $book_id );
        //\Doctrine\Common\Util\Debug::dump($book);
        //die();
                
        if (!empty($cart)) {
            echo "panier en cours<br />";
            print_r($cart);
            $cartItem = new CartItem();
            $cartItem->setCart( $cart );
            $cartItem->setBook( $book );

            // update cart et sauvegarde cartItem
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($cartItem);  
            $em->flush();
        }
        else {
            echo "création de panier<br />";
            // Création de panier
            $cart = new Cart();
            $cartItem = new CartItem();
            $cart->setStatus( "en cours" );
            $cart->setUser( $user );
            $cartItem->setCart( $cart );
            $cartItem->setBook( $book );

            // sauvegarder en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($cart);  
            $em->persist($cartItem);  
            $em->flush();
        }
        
        $itemsNumber = $cartRepo->CountItemsNumberInCurrentCart( $cart->getId() );

        $this->get('session')->getFlashBag()->add(
            'notice',
            'BD ajoutée !'
        );

        $params['itemsNumber'] = $itemsNumber;

        return $this->render("cart/add_book.html.twig", $params);
    }

    /**
     * @Route("/supprime-bd/{book_id}")
     */
    public function removeBookAction($book_id)
    {
        $params = array();

        return $this->render("cart/remove_book.html.twig", $params);
    }

    /**
     * @Route("/")
     */
    public function recapAction()
    {
        $params = array();

        // récupère l'utilisateur en session
        $user = $this->getUser();

        //  check dans table cart s'il a un panier en cours (statut = en cours, validé, vidé)
        //      si oui, récupérer le panier, sinon afficher panier vide
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );
                
        if (!empty($cart)) {
            echo "panier en cours<br />";
            \Doctrine\Common\Util\Debug::dump($cart);
            $books = $cartRepo->findBooksInCurrentCart( $cart->getId() );
            \Doctrine\Common\Util\Debug::dump($books);
            die();

            $params['books'] = $books;
        }
        else {
            echo "panier vide<br />";
            $params['books'] = "";
        }
        
        return $this->render("cart/recap.html.twig", $params);
    }

    /**
     * @Route("/validation")
     */

    public function validateAction()
    {
        $params = array();
        
        return $this->render("cart/validate.html.twig", $params);
    }




}
