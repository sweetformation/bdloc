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

        return $this->render("admin/back_office.html.twig", $params);
    }

    /**
     * @Route("/ajout/bd")
     */
    public function addBdAction()
    {
        $params = array();

        return $this->render("admin/add_bd.html.twig", $params);
    }

    /**
     * @Route("/ajout/serie")
     */
    public function addSerieAction()
    {
        $params = array();

        return $this->render("admin/add_serie.html.twig", $params);
    }

    /**
     * @Route("/ajout/amende")
     */
    public function addFineAction()
    {
        $params = array();

        return $this->render("admin/add_fine.html.twig", $params);
    }

    /**
     * @Route("/retour/bd")
     */
    public function bdBackAction()
    {
        $params = array();

        return $this->render("admin/bd_back.html.twig", $params);
    }

}
