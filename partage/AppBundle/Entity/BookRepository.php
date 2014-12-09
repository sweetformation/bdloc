<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Bdloc\AppBundle\Entity\FilterBook;

class BookRepository extends EntityRepository
{

    public function findCatalogBooks(FilterBook $filterBook){

        $numPerPage = $filterBook->getNumPerPage();
        $page = $filterBook->getPage();
        $keywords =  $filterBook->getKeywords();

        $offset = ($page - 1) * $numPerPage;

        $qb = $this->createQueryBuilder('b');

        if(!empty($keywords) && $keywords != "none"){
            $qb->andWhere("b.title LIKE :keywords");
            $qb->setParameter("keywords", "%".$keywords."%");
        }
        //@todo: autres filtres

        $qb->join('b.illustrator', 'i')
            ->addSelect('i');
        //@todo: autres joins

        //@todo: page dynamique en fct l'url
        $qb->setFirstResult($offset)
            ->setMaxResults($numPerPage);

        $query = $qb->getQuery();
        $paginator = new Paginator($query);

        //echo $paginator->count();

        return $paginator;
    }

}
