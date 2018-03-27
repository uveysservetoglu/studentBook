<?php

namespace ApiBundle\Repository;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;

/**
 * ModMeetingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ModMeetingRepository extends \Doctrine\ORM\EntityRepository
{
    public function getMeetingList($param){
        $dql = "SELECT m.id, m.nameSurname, m.alarm, m.phone, m.subject, m.status  FROM ApiBundle:ModMeeting m ";
        if($param['query'] !=null){
            $dql = $dql." WHERE m.".$param['qtype']." ='".$param['query']."'";
        }
        $dql = $dql. "ORDER BY  m.".$param['sortname']." ".$param['sortorder'];
        return $this -> getEntityManager()
            -> createQuery($dql)
            -> setMaxResults($param['rp'])
            -> setFirstResult($param['offset'])
            -> getResult();

    }
    public function getDayMeeting(){
        $time = new \DateTime();
        $nowTime = $time->format('H:i:s \O\n Y-m-d');
        $timeArray = explode(' On ',$nowTime);
        $dql = "SELECT m.id, m.nameSurname, m.content, m.phone, m.subject, m.status,m.alarm  FROM ApiBundle:ModMeeting m WHERE m.alarm <=  '".$timeArray[1]."' AND m.status ='a' ORDER BY m.alarm ASC, m.status ASC ";
        return $this -> getEntityManager()
            -> createQuery($dql)
            -> getResult();
    }
}
