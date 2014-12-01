<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class AdminController extends Controller
{
    /**
     * @Route("/")
     */
    public function backOfficeAction()
    {
        $params = array();
/*
        $ticketRepo = $this->getDoctrine()->getRepository("MCAppBundle:Ticket");
        $tickets = $ticketRepo->findTicketsWithCategory();

        $params['tickets'] = $tickets;
        //\Doctrine\Common\Util\Debug::dump($tickets);*/

        return $this->render("admin/back_office.html.twig", $params);
    }

    /**
     * @Route("/ajout/bd")
     */
    public function addBdAction()
    {
        $params = array();
/*

        // --------------------- RECUPERER CATEGORIES ---------------------
        $categoryRepo = $this->getDoctrine()->getRepository("MCAppBundle:Category");
        $categories = $categoryRepo->findAll();

        $params['categories'] = $categories;

        // --------------------- FORMULAIRE CATEGORIE ---------------------
        $category = new Category();
        $categoryForm = $this->createForm(new CategoryType(), $category);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($category)
        $request = $this->getRequest();
        $categoryForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($categoryForm->isValid()) {

            // sauvegarder en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);  
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Votre categorie est bien enregistrée!'
            );

            // Vider le formulaire et empêche la resoumission des données
            return $this->redirect( $this->generateUrl("mc_app_admin_newcategory") );

        }

        $params['categoryForm'] = $categoryForm->createView();*/

        return $this->render("admin/add_bd.html.twig", $params);
    }

    /**
     * @Route("/ajout/serie")
     */
    public function addSerieAction()
    {
        $params = array();


        /*// --------------------- RECUPERER CATEGORIES ---------------------
        $categoryRepo = $this->getDoctrine()->getRepository("MCAppBundle:Category");
        $categories = $categoryRepo->findAll();

        $params['categories'] = $categories;

        // --------------------- FORMULAIRE CATEGORIE ---------------------
        $category = new Category();
        $categoryForm = $this->createForm(new CategoryType(), $category);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($category)
        $request = $this->getRequest();
        $categoryForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($categoryForm->isValid()) {

            // sauvegarder en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);  
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Votre categorie est bien enregistrée!'
            );

            // Vider le formulaire et empêche la resoumission des données
            return $this->redirect( $this->generateUrl("mc_app_admin_newcategory") );

        }

        $params['categoryForm'] = $categoryForm->createView();*/

        return $this->render("admin/add_serie.html.twig", $params);
    }

}
