<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class CronJobController extends Controller
{
    /**
     * @Route("/fghcfxhcgjn")
     */
    public function checkCartTimingAction() {
        $user = $this->getUser();
        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $carts = $cartRepo->findAll();

        //if ($cart != null) {
        foreach ($carts as $cart) {
            if ($cart->getStatus() == "en cours" ) {
                $datetime1 = $cart->getDateModified();
                $datetime2 = new \DateTime("now");
                $interval = $datetime1->diff($datetime2);
                if ( $interval->format('%i') > 30 ){
                    // Maj statut panier
                    $cart->setStatus( "vidé" );

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($cart);

                    // gestion stock des books
                    $cartItems = $cart->getCartItems();
                    foreach ($cartItems as $cartItem) {
                        $book = $cartItem->getBook();
                        // remet le stock à jour
                        $book->setStock( $book->getStock() + 1 );
                        $em->persist($book);
                    }  
                    // Exécute en BDD
                    $em->flush();
                }
            }
        }
        return new Response("");
    }
    // Utilisation de Cron Job toutes les minutes par exemple qui appelle cette méthode.
    // Sous linux, facile à mettre en place = soit via méthode, soit carrément via commande
    // Sous Windows, se fait avec les scheduled task, mais compliqué...je m'arrête là.
}
