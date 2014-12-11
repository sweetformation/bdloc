<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Bdloc\AppBundle\Entity\Cart;
use Bdloc\AppBundle\Entity\CartItem;

use Symfony\Component\HttpFoundation\Response;

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
        //\Doctrine\Common\Util\Debug::dump($cart);

        // récupère le book à ajouter
        $bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book = $bookRepo->find( $book_id );
        //\Doctrine\Common\Util\Debug::dump($book);

              
        if (empty($cart)) {
            // Création de panier
            $cart = new Cart();
            $cart->setStatus( "en cours" );
            $cart->setUser( $user );
            $cartItem = new CartItem();
            $cartItem->setCart( $cart );
            $cartItem->setBook( $book );

            // enlève une quantité au stock 
            $book->setStock( $book->getStock() - 1 );
            $bookStock = $book->getStock();

            // sauvegarde en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($cart);  
            $em->persist($cartItem);  
            $em->flush();
        
            $this->get('session')->getFlashBag()->add(
                'notice',
                'BD ajoutée à votre panier !'
            );
        }
        elseif ( count($cart->getCartItems()) < 10 ) {
            // Maj de panier en cours
            $cartItem = new CartItem();
            $cartItem->setCart( $cart );
            $cartItem->setBook( $book );

            // enlève une quantité au stock 
            $book->setStock( $book->getStock() - 1 );
            $bookStock = $book->getStock();

            // maj dateModified
            $cart->setDateModified( new \DateTime() );

            // update cart et sauvegarde cartItem
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($cart);  
            $em->persist($cartItem);  
            $em->flush();
        
            $message = $this->get('session')->getFlashBag()->add(
                'notice',
                'BD ajoutée à votre panier !'
            );
        }
        else {
            $message = $this->get('session')->getFlashBag()->add(
                'error',
                'Vous avez atteint le maximum de BD dans votre panier !'
            );
            $bookStock = $book->getStock();
        }
        $cart = $cartRepo->findUserCurrentCart( $user );
        $itemsNumber = count($cart->getCartItems());
        $params['itemsNumber'] = $itemsNumber;
        $params['message'] = $message;

        $params['bookStock'] = $bookStock;
        return $this->render("cart/add_book.html.twig", $params);

    }

    /**
     * @Route("/supprime-bd/{id}")
     */
    public function removeBookAction($id)
    {
        $params = array();
        // récupère l'utilisateur en session
        $user = $this->getUser();

        // enlève le cartItem 
        $cartItemRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");
        $cartItem = $cartItemRepo->find( $id );

        $book = $cartItem->getBook();
        // remet le stock à jour
        $book->setStock( $book->getStock() + 1 );

        $em = $this->getDoctrine()->getManager(); 
        $em->remove($cartItem);  
        $em->persist($book);  
        $em->flush();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );
        if (!empty($cart)) {
            $cart = $cartRepo->findBooksInCurrentCart( $cart->getId() );
        }
        $params['cart'] = $cart;
        
        return $this->render("cart/recap.html.twig", $params);
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
        //$cart = $cartRepo->findOneBy( array('user' => $user, 'status' => 'en cours' ));
                
        if (!empty($cart)) {
            $cart = $cartRepo->findBooksInCurrentCart( $cart->getId() );
            //\Doctrine\Common\Util\Debug::dump($cart);
        }

        $params['cart'] = $cart;
        
        return $this->render("cart/recap.html.twig", $params);
    }

    /**
     * @Route("/validation")
     */
    public function validateAction()
    {   
        //
        // Si user a une amende, le rediriger vers page amende!
        // @todo

        $params = array();
        $user = $this->getUser();
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );

        // Récupération des BDs
        $cart = $cartRepo->findBooksInCurrentCart( $cart->getId() );

        $params['cart'] = $cart;

        // récupération du nb dans panier
        $itemsNumber = count($cart->getCartItems());
        $params['itemsNumber'] = $itemsNumber;
      
        // Maj statut panier
        $cart->setStatus( "validé" );
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();
        
        return $this->render("cart/validate.html.twig", $params);
    }

    /**
     * @Route("/itemsNumber")
     */
    public function getItemsNumberInCurrentCartAction() {
        $user = $this->getUser();
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );
        $itemsNumber = 0;

        if ($cart != null) {
            $itemsNumber = count($cart->getCartItems());
        }
        //return $itemsNumber;
        return new Response($itemsNumber);

    }
    // Fonction qui va chercher le nombre d'éléments dans panier courant et qui est appelée en template twig dans le header




}
