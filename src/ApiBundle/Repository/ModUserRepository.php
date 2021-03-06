<?php

namespace ApiBundle\Repository;

/**
 * modUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class ModUserRepository extends \Doctrine\ORM\EntityRepository
{

    public function getUser($param){
        $find= array();
        $filter = null;
        if(is_string($param)){
            $find=array('username',$param);
            $filter = 'u';
        }else{
            $find=array('id',$param);
            $filter ='u.id, u.nameSurname, u.birthday, u.mobil, u.email, u.address, u.job, u.website, u.groupId';
        }
        $dql = "SELECT ".$filter." FROM ApiBundle:ModUser u WHERE u.".$find[0]." = :".$find[0];
        return $this -> getEntityManager()
                     -> createQuery($dql)
                     -> setParameter($find[0],$find[1])
                     -> getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
    public function getUserList($param){
        $dql = "SELECT u.id, u.nameSurname, u.email, u.status, u.username FROM ApiBundle:ModUser u ";
        if($param['query'] !=null){
            $dql = $dql." WHERE u.".$param['qtype']." ='".$param['query']."'";
        }
        $dql = $dql. "ORDER BY  u.".$param['sortname']." ".$param['sortorder'];
        return $this -> getEntityManager()
            -> createQuery($dql)
            -> setMaxResults($param['rp'])
            -> setFirstResult($param['offset'])
            -> getResult();

    }


}
