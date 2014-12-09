<?php

namespace Bdloc\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * BookRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookRepository extends EntityRepository
{

    /*public function findBooksBySearch($page){

        $qb = $this->createQueryBuilder('b')
            ->addSelect('b')        
            //Jointure pour l'auteur
            ->join('b.illustrator', 'il')
            ->addSelect('il')     
            //Jointure pour le coloriste
            ->join('b.colorist', 'co')
            ->addSelect('co')       
            //Jointure pour le scénariste
            ->join('b.scenarist', 'sc')
            ->addSelect('sc')
            //Jointure pour les catégories
            ->join('b.serie', 'cat')
            ->addSelect('cat');

        // $qb->setFirstResult(0)
        //     ->setMaxResults(30);

        // $query = $qb->getQuery();

        // $paginator = new Paginator($query);
        // return $paginator;
        
        // $page = 1;
        $numPerPage = 20; //limit

        //JE SAIS PAS CE QUE C'EST MAIS CA MARCHE !
        $request = Request::createFromGlobals();
        //JE SAIS PAS CE QUE C'EST MAIS CA MARCHE !
        $request->query->get('page');

        //JE SAIS PAS CE QUE C'EST MAIS CA MARCHE PAS !
        //$request->query->get('categorie');

        //AFFICHAGE AVEC LA PAGINATION pour le premier résultat
        $qb->setFirstResult(($page-1) * $numPerPage)
        //LE NOMBRE DE POST PAR PAGE 
            ->setMaxResults($numPerPage);
         
        return new Paginator($qb);

    }*/

    public function findBooksBySearch($variables){

        $page = $variables['page'];
        $numPerPage = $variables['numPerPage'];
        $keywords = $variables['keywords'];
        $orderBy = $variables['orderBy'];
        $orderDir = $variables['orderDir'];
        $categories = $variables['categories'];
        $availability = $variables['availability'][0];
        //print_r($categories);

        $qb = $this->createQueryBuilder('b');

        $qb->addSelect('b')
            ->join('b.illustrator', 'il')
            ->join('b.colorist', 'co')
            ->join('b.scenarist', 'sc')
            ->addSelect('il', 'co', 'sc')
            ->join('b.serie', 'cat')
            ->addSelect('cat');

        if (!empty($keywords)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    "b.title LIKE :keywords",
                    "il.lastName LIKE :keywords",
                    "co.lastName LIKE :keywords",
                    "sc.lastName LIKE :keywords",
                    "il.firstName LIKE :keywords",
                    "co.firstName LIKE :keywords",
                    "sc.firstName LIKE :keywords"
                )
            );
            //$qb->andWhere("b.title LIKE :keywords");
            $qb->setParameter("keywords", "%".$keywords."%");
        }
        
        if (!empty($categories) && count($categories) != 0) {
            for ($i=0; $i<count($categories); $i++) {
                $qb->orWhere("cat.style = :cat".$i);
                $qb->setParameter("cat".$i, $categories[$i]);
            }
        }

        if ($availability == 1) {
            $qb->andWhere("b.stock >= :stock");
            $qb->setParameter("stock", 1);
        }        

        $request = Request::createFromGlobals();
        $request->query->get('page');

        if (!empty($orderBy) && !empty($orderDir)){
            $qb->orderBy("b.".$orderBy, $orderDir);
        }

        $qb->setFirstResult(($page-1) * $numPerPage)
            ->setMaxResults($numPerPage);

        $query = $qb->getQuery();

        $paginator = new Paginator($query);
        //dump($paginator);
        //echo($paginator->count()); 
        $nbPage = ceil($paginator->count() / $numPerPage);  
        //echo "-" .$nbPage;      

        return $paginator;

    }

    public function findCatalogBooks(FilterBook $filterBook) {

        //print_r($filterBook);
        $numPerPage = $filterBook->getNumPerPage();
        $page = $filterBook->getPage();
        $keywords = $filterBook->getKeywords();
        $categories = $filterBook->getCategories();
        $availability = $filterBook->getAvailability();
        $orderBy = $filterBook->getOrderBy();
        $orderDir = $filterBook->getOrderDir();


        $offset = ($page - 1) * $numPerPage;

        $qb = $this->createQueryBuilder('b');

        if (!empty($keywords) && $keywords != "none") {
            $qb->andWhere("b.title LIKE :keywords");
            $qb->setParameters("keywords", "%".$keywords."%");
        }
        
        /*if (!empty($categories) && count($categories) != 0) {
            for ($i=0; $i<count($categories); $i++) {
                $qb->orWhere("cat.style = :cat.$i");
                $qb->setParameters("cat".$i, $categories[$i]);
            }
        }

        if ($availability == 1) {
            $qb->andWhere("b.stock >= :stock");
            $qb->setParameters("stock", 1);
        }*/

        $qb->addSelect('b')
            ->join('b.illustrator', 'il')
            ->join('b.colorist', 'co')
            ->join('b.scenarist', 'sc')
            ->addSelect('il', 'co', 'sc')
            ->join('b.serie', 'cat')
            ->addSelect('cat');

        $qb->setFirstResult($offset)
            ->setMaxResults($numPerPage);

        $query = $qb->getQuery();

        $paginator = new Paginator($query);

        echo($paginator->count());
        die();

        return $paginator;

    }

    public function getSelectDispo() {
        $query = $this->createQueryBuilder('b')
                      ->groupBy('b.stock')
                      ->orderBy('b.stock', 'ASC');
     
        return $query;
    }
}