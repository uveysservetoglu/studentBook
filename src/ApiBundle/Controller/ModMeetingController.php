<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\ModMeeting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ModMeetingController extends BaseController
{
    public function listAction()
    {
        if(!$this->get('panel.user')->ifRoll('ModMeeting','list')){
            return new JsonResponse('not.roll');
        }
        $request = Request::createFromGlobals();
        $param   = array(
            'page'          => $request->get('page')      != null ?  $request->get('page')  : 1,
            'sortname'      => $request->get('sortname')  != null ?  $request->get('sortname')  : 'id',
            'sortorder'     => $request->get('sortorder') != null ?  $request->get('sortorder') : 'asc',
            'query'         => $request->get('query'),
            'qtype'         => $request->get('qtype'),
            'rp'            => $request->get('rp'),
            'offset'        => $request->get('page') != 1 ? (($request->get('page')-1) * $request->get('rp')) : NULL
        );
        /** @var ModMeetingRepository $repo */
        $repo =$this->getRepo('ApiBundle:ModMeeting');
        $data = array(
            'data'     => $repo->getMeetingList($param),
            'page'     => $param['page'],
            'total'    => count($repo->findAll())
        );
        return new JsonResponse(
            $this->get('panel.flexi')->jsonMeeting($data)
        );
    }

    public function loadEditMeetingAction(){
        if(!$this->get('panel.user')->ifLogin()){
            return new RedirectResponse($this->generateUrl('panel.login'));
        }
        $request = Request::createFromGlobals();
        $repo = $this->getRepo('ApiBundle:ModMeeting')->findOneBy([
            'id'=>$request->get('id')[0]
        ]);
        $data = array(
            'id'=>$repo->getId(),
            'nameSurname'=>$repo->getNameSurname(),
            'alarm'=>$repo->getAlarm(),
            'date'=>$repo->getDate(),
            'phone'=>$repo->getPhone(),
            'subject'=>$repo->getSubject(),
            'content'=>$repo->getContent()
        );
        return new JsonResponse($data);
    }

    public function editAction(){
        if(!$this->get('panel.user')->ifRoll('ModUser','edit')){
            return new JsonResponse('not.roll');
        }
        $request = Request::createFromGlobals();
        $data = $request->request;
        $met = $this->getRepo('ApiBundle:ModMeeting')->find($data->get('id'));

        $met->setNameSurname($data->get('nameSurname'));
        $from =  new \DateTime( $data->get('alarm'));
        $met->setAlarm($from);
        $from =  new \DateTime( $data->get('date'));
        $met->setDate($from);
        $met->setPhone($data->get('phone'));
        $met->setSubject('Tango');
        $met->setContent($data->get('content'));
        $met->setUserId($this->session->get('authentication_data')['id']);
        $met->setStatus('a');
        $action = array('action'=>'update', 'data' => $met);
        $this->crudData($action);
        return new JsonResponse('success.insert');
    }

    public function insertAction(){

        if(!$this->get('panel.user')->ifRoll('ModMeeting','insert')){
            return new JsonResponse('not.roll');
        }
        $request = Request::createFromGlobals();
        if (empty($request->request)){
            return new JsonResponse('null.input');
        }


        $data = $request->request;
        $met = new ModMeeting();
        $met->setNameSurname($data->get('nameSurname'));
        $from =  new \DateTime( $data->get('alarm'));
        $met->setAlarm($from);
        $from =  new \DateTime( $data->get('date'));
        $met->setDate($from);
        $met->setPhone($data->get('phone'));
        $met->setSubject('Tango');
        $met->setContent($data->get('content'));
        $met->setUserId($this->session->get('authentication_data')['id']);
        $met->setStatus('a');

        $action = array('action'=>'insert', 'data' => $met);
        $this->crudData($action);
        return new JsonResponse('success.insert');
    }

    public function deleteAction(){
        if(!$this->get('panel.user')->ifRoll('ModMeeting','delete')){
            return new JsonResponse('not.roll');
        }

        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();
        $req = $request->get('sil');
        foreach ($req as $id){
            $userRepo = $em->getRepository('ApiBundle:ModMeeting')->find($id);
            $action = array('action'=>'delete', 'data' => $userRepo);
            $this->crudData($action);
        }

        return new JsonResponse('success.delete');

    }

    public function statusAction(){
        if(!$this->get('panel.user')->ifRoll('ModMeeting','edit')){
            return new JsonResponse('not.roll');
        }
        $request = Request::createFromGlobals();
        $editUser = $request->request;

        $getId = $editUser->get('id');
        foreach ($getId as $id){
            $repo = $this->getRepo('ApiBundle:ModMeeting')->find($id);

            $repo->setStatus($editUser->get('status')[0]);
            $action = array('action' => 'update', 'data' => $repo);
            $this->crudData($action);
        }
        return new JsonResponse('success.active');
    }

    public function getDayAction(){
        if(!$this->get('panel.user')->ifLogin()){
            return new RedirectResponse($this->generateUrl('panel.login'));
        }
        /**@var \ApiBundle\Repository\ModMeetingRepository $repo*/
        $repo = $this->getRepo('ApiBundle:ModMeeting');
        return new JsonResponse($repo->getDayMeeting());
    }
}
