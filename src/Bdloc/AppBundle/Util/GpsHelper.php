<?php

namespace Bdloc\AppBundle\Util;

class GpsHelper {

    // fonction qui retourne un tableau des coordonnées gps depuis une adresse
    function getCoordinates($address){
        $address = urlencode($address);
        $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=" . $address;
        $response = file_get_contents($url);
        $json = json_decode($response,true);
     
        $lat = $json['results'][0]['geometry']['location']['lat'];
        $lng = $json['results'][0]['geometry']['location']['lng'];
        
        $coord = array(
            "lat" => $lat,
            "lng" => $lng
        );

        return $coord;
    }

}