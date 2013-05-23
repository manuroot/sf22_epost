<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\EpostBundle\Repository;

use Application\EpostBundle\Entity\EpostCategories;
use Doctrine\ORM\EntityRepository;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class EpostRepository extends EntityRepository {

    public function myFind() {

        $query = $this->createQueryBuilder('a')
                ->leftJoin('a.proprietaire', 'b')
                ->leftJoin('a.categorie', 'c')
                ->leftJoin('a.idStatus', 'd')
                ->add('orderBy', 'a.id DESC')
                ->getQuery();
        return $query;
    }

    public function myFindActif() {
//true ==>1 post actif (form case cochéé)
        // false ==> 0 case decochée
        $query = $this->createQueryBuilder('a')
                ->where('a.isvisible = true')
                ->leftJoin('a.proprietaire', 'b')
                ->leftJoin('a.categorie', 'c')
                ->leftJoin('a.idStatus', 'd')
                ->add('orderBy', 'a.id DESC')
                ->getQuery();
        return $query;
    }

    public function myFindAll($user_id) {

        $query = $this->createQueryBuilder('a')
                ->add('orderBy', 'a.id DESC')
                ->where('a.proprietaire = :proprietaire')
                ->leftJoin('a.proprietaire', 'b')
                ->leftJoin('a.categorie', 'c')
                ->leftJoin('a.idStatus', 'd')
                ->setParameter('proprietaire', $user_id)
                ->getQuery();

        return $query;

        /* $query = $this->createQueryBuilder('a')
          ->add('orderBy', 'a.id DESC');

          if (isset($user_id)){
          $query=$query->where('a.proprietaire = :proprietaire')
          ->setParameter('proprietaire', $user_id);


          }
          ->getQuery(); */
    }

    public function myFindOtherAll($user_id, $group_id) {
        // A ameliorer
        $query = $this->createQueryBuilder('a')
                   ->select('a,b,c,d,e,t,f,g')
             
                ->where('a.proprietaire <> :proprietaire')
                ->setParameter('proprietaire', $user_id)
                ->leftJoin('a.proprietaire', 'b')
                ->andWhere('b.idgroup = :groupid')
                ->setParameter('groupid', $group_id)
                ->leftJoin('a.categorie', 'c')
                ->leftJoin('a.idStatus', 'd')
                  ->leftJoin('a.globalnote', 'e')
                 ->leftJoin('a.tags', 't')
                ->leftJoin('a.imageMedia', 'f')
              ->leftJoin('a.comments', 'g')
                ->add('orderBy', 'a.id DESC')
                ->getQuery();
        return $query;
    }

    /*
     * 
     *        //    ->leftJoin('u.produit', 'a')
      //  ->leftJoin('a.proprietaire', 'v')
      ->where('u.proprietaire = :proprietaire')
      ->setParameter('proprietaire', $user_id)
     */
    /* public function myFindAll($user_id) {

      $query = $this->createQueryBuilder('a')
      ->where('a.demandeur = :demandeur')
      ->setParameter('demandeur', $user_id)
      //->orderBy('p.price', 'ASC')
      ->getQuery();
      return $query;

      } */

    public function findaByYear($year) {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id,p.createdAt');
        $arr = array();

        foreach ($qb->getQuery()->getResult() as $d) {
            $year = $d['createdAt']->format('Y');
            //  echo "year=$year<br>";
            //  $cat=$d['category'];
            if (!(isset($arr["$year"])))
                $arr["$year"] = 0;
            $arr["$year"] = $arr["$year"] + 1;
        }
        return ($arr);
    }

    public function getLatestBlogs($limit = null) {
        $qb = $this->createQueryBuilder('b')
                ->select('b')
                ->addOrderBy('b.createdAt', 'DESC');

        if (false === is_null($limit))
            $qb->setMaxResults($limit);

        return $qb->getQuery()
                        ->getResult();
    }

    /* criteres a passer:
     * array(
     *  'proprietaire'=>
     *  'categorie' =>
     *  'tag'=>
     *     * 
     * )
     * 
     * ex:
     * $criteria=array('author'=>$idauthor)
     */

    public function getMyPager(array $criteria) {

        /* $query = $this->createQueryBuilder('a')
          ->add('orderBy', 'a.id DESC')
          ->where('a.proprietaire = :proprietaire')
          ->leftJoin('a.proprietaire', 'b')
          ->leftJoin('a.categorie', 'c')
          ->leftJoin('a.idStatus', 'd')
          ->setParameter('proprietaire', $criteria['author'])
          ->getQuery();

          return $query; */
        /*
> $query = $query->getResult();
>
> $adapter = $this->get('knp_paginator.adapter');
> $adapter->setQuery($query)
        */
        $parameters = array();
        $query = $this->createQueryBuilder('a')
                ->select('a,b,c,d,e,f,u')
                ->add('orderBy', 'a.id DESC')
                //->where('a.proprietaire = :proprietaire')
                ->leftJoin('a.proprietaire', 'b')
              //  ->leftJoin($join, $alias, $conditionType)
                ->leftJoin('a.categorie', 'c')
                ->leftJoin('a.idStatus', 'd')
                ->leftJoin('a.globalnote', 'e')
                ->leftJoin('a.imageMedia', 'f')
              //  ->leftJoin('a.tags', 't')
                ->leftJoin('a.comments', 'u')
                ->groupby('a.name')
                ;

        // ->setParameter('proprietaire', $criteria['author']);
        /* ->getQuery(); */
        // $parameters['proprietaire'] = $criteria['author'];
        if (isset($criteria['author'])) {
            //  print_r($criteria);exit(1);
            $query->andwhere('a.proprietaire = :proprietaire');
            $parameters['proprietaire'] = $criteria['author'];
        }


        if (isset($criteria['non-author'])) {
            //  print_r($criteria);exit(1);
            $query->andWhere('a.proprietaire <> :user_id');
            $parameters['user_id'] = $criteria['non-author'];
        }


        if (isset($criteria['group'])) {
            $query->andWhere('b.idgroup = :group_id');
            $parameters['group_id'] = $criteria['group'];
        }

         if (isset($criteria['open_status'])) {
            $query->andWhere('d.nom = :open_status');
            $parameters['open_status'] = $criteria['open_status'];
        }
        if (isset($criteria['categorie']) && $criteria['categorie'] instanceof EpostCategories) {
            $query->andWhere('a.categorie = :categoryid');
            $parameters['categoryid'] = $criteria['categorie']->getId();
        }
        
          if (isset($criteria['tag'])) {
               $query->leftJoin('a.tags', 't');
            $query->andWhere('t.id = :tag');
            //   ->groupby('a.name');
  $parameters['tag'] = (string) $criteria['tag'];
     //       $parameters['tag'] = 'tag1';
        }
        $query->setParameters($parameters);
        //>getQuery();
        //  print_r($query->getQuery());
        //  exit(1);
         return $query->getQuery();
        //return $query->getQuery()->getResult();
    }

     public function getMyPagerStandard(array $criteria) {

      
        
        $parameters = array();
        $query = $this->createQueryBuilder('a')
                ->select('a,b,c,d,e,f')
                ->add('orderBy', 'a.id DESC')
                //->where('a.proprietaire = :proprietaire')
                ->leftJoin('a.proprietaire', 'b')
              //  ->leftJoin($join, $alias, $conditionType)
                ->leftJoin('a.categorie', 'c')
                ->leftJoin('a.idStatus', 'd')
                ->leftJoin('a.globalnote', 'e')
                ->leftJoin('a.imageMedia', 'f')
                //->leftJoin('a.tags', 't')
             //   ->leftJoin('a.comments', 'u')
                ->groupby('a.name')
                ;

       if (isset($criteria['author'])) {
            //  print_r($criteria);exit(1);
            $query->andwhere('a.proprietaire = :proprietaire');
            $parameters['proprietaire'] = $criteria['author'];
        }


        if (isset($criteria['non-author'])) {
            //  print_r($criteria);exit(1);
            $query->andWhere('a.proprietaire <> :user_id');
            $parameters['user_id'] = $criteria['non-author'];
        }


        if (isset($criteria['group'])) {
            $query->andWhere('b.idgroup = :group_id');
            $parameters['group_id'] = $criteria['group'];
        }

        if (isset($criteria['categorie']) && $criteria['categorie'] instanceof EpostCategories) {
            $query->andWhere('a.categorie = :categoryid');
            $parameters['categoryid'] = $criteria['categorie']->getId();
        }
        if (isset($criteria['tag'])) {
            //    $query->leftJoin('a.tags', 't');
            $query->andWhere('t.id =:tag');
            //   ->groupby('a.name');

            $parameters['tag'] = (string) $criteria['tag'];
        }
        $query->setParameters($parameters);
        //>getQuery();
        //  print_r($query->getQuery());
        //  exit(1);
         return $query->getQuery();
        //return $query->getQuery()->getResult();
    }

    public function getPublicationDateQueryParts($date, $step, $alias = 'p') {
        return array(
            'query' => sprintf('%s.createdAt >= :startDate AND %s.createdAt < :endDate', $alias, $alias),
            'params' => array(
                'startDate' => new \DateTime($date),
                'endDate' => new \DateTime($date . '+1 ' . $step)
            )
        );
    }

}