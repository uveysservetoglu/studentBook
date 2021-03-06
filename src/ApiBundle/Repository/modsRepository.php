<?php

namespace ApiBundle\Repository;

/**
 * modsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class modsRepository extends \Doctrine\ORM\EntityRepository
{
    public function getMods($param)
    {
        $dql = "SELECT m FROM ApiBundle:mods m";
        $key = null;
        if(is_string($param)){
            $dql =  $dql .' WHERE m.'.($key ='modName').' = :'.$key;
        }
        else if(is_numeric($param)){
            $dql =  $dql .' WHERE m.'.($key ='id').' = :'.$key;
        }else
        {
            return false;
        }
        return $this -> getEntityManager()
            -> createQuery($dql)
            -> setParameter($key,$param)
            -> getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
