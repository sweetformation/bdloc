<?php

namespace Bdloc\AppBundle\Util;

class StringHelper {

    // Fonction qui génère aléatoirement une chaine de caractères (par défaut de 50 caractères)
    public function randomString($length = 50) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $string = "";
        for ($i=0; $i<$length;$i++) {
            $num = mt_rand(0, strlen($chars)-1);
            $string .= $chars[$num];
        }
        return $string;
    }

}