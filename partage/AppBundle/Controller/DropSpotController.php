<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DropSpotController extends Controller
{

    /**
    *
    * @Route("/point-relais/coordonnees/")
    */
    public function findCoordinatesAction(){
        //Saveurs du Sud - 14 Av Félix Faure - 75015 Paris
        //Anwa - 105 Bis rue des Entrepreneurs - 75015 Paris
        //Mercerie Poncet - 15 rue Daumier - 75016 Paris

        $result = $this->container->get('bazinga_geocoder.geocoder')
                  ->geocode("14 Av Félix Faure - 75015 Paris");

        print_r($result);
        die();

    }

}
