<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SerieRepository
 *
 */
class SerieRepository extends EntityRepository
{
    public function getSelectList() {
        $query = $this->createQueryBuilder('s')
                      ->groupBy('s.style')
                      ->orderBy('s.style', 'ASC');
     
        return $query;
    }
}