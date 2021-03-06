<?php

namespace Application\EpostBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NotesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EpostTagsRepository extends EntityRepository {

    public function myFindAll() {
        return $this->createQueryBuilder('a')
                 ->select('a,b')
                  ->leftJoin('a.posts', 'b')
                        ->getQuery()
            ->getResult();
        //->getResult();
    }

  
   
public function getTags()
{
    $blogTags = $this->createQueryBuilder('b')
                     ->select('b.tags')
                     ->getQuery()
                     ->getResult();

    $tags = array();
    foreach ($blogTags as $blogTag)
    {
   //     $tags = array_merge(explode(",", $blogTag['tags']), $tags);
            $tags = $blogTag['tags'];
    }

    foreach ($tags as &$tag)
    {
        $tag = trim($tag);
    }

    return $tags;
}

public function getTagWeights($tags,$max_height=100)
{
        //getPosts()
        $tagWeights=array();
        $aa=array();
        foreach ($tags as $tag)
    {
         $a=$tag->getPosts();
         $b=count($a);
        // if ($b > 0)
         $tagWeights[$tag->getName()] =$b;
      }
 // Shuffle the tags
   /* uksort($tagWeights, function() {
        return rand() > rand();
    }); */ 
      // ex max=40
     $max = max($tagWeights);
    // Max of 5 weights
    $multiplier = ($max > 5) ? 5 / $max : 1;
    foreach ($tagWeights as &$tag)
    {
        $tag = ceil(($tag/$max)*$max_height);
        
        $tag = ceil($tag * $multiplier);
       //  echo "tag=$tag  -- max=$max<br>"; 
    }
 //var_dump($tagWeights);
  return $tagWeights;
 
}

}
