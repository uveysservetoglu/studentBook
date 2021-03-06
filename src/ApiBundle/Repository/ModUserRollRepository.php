<?php

namespace ApiBundle\Repository;

/**
 * ModUserRollRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModUserRollRepository extends \Doctrine\ORM\EntityRepository
{

    public function getRoll($roll, $mod){

        $dql = "SELECT r FROM ApiBundle:ModUserRoll r WHERE r.modId ='".$mod."' ";
        $key = null;
        if(is_string($roll)){
            $dql =  $dql .'AND r.'.($key ='rollName').' = :'.$key;
        }
        else if(is_numeric($roll)){
            $dql =  $dql .'AND r.'.($key ='id').' = :'.$key;
        }else
        {
            return false;
        }
        return $this -> getEntityManager()
            -> createQuery($dql)
            -> setParameter($key,$roll)
            -> getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    }
}
