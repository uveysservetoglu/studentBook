<?php

namespace PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ModMeetingController extends BaseController
{
    public function listAction(){
        if(!$this->get('panel.user')->ifLogin()){
            return new RedirectResponse($this->generateUrl('panel.login'));
        }

        $this->init( 'meetingList','ModMeeting');
        $this->var['title']=$this->var['lang']['grid']['modName'];

        if(!$this->get('panel.user')->ifRoll('ModMeeting','list')){
            $this->var['error']= array(
                'status'  => true,
                'message' => $this->var['lang']['genel']['tr']['not_roll']
            );
            return $this->render('PanelBundle:Default:index.html.twig', ['var' => $this->var]);
        }
        return $this->render('PanelBundle:Default:index.html.twig', ['var' => $this->var]);
    }

    public function meetingAction(){

        if(!$this->get('panel.user')->ifLogin()){
            return new RedirectResponse($this->generateUrl('panel.login'));
        }

        $this->init( 'meeting','ModMeeting');
        $this->var['title']=$this->var['lang']['grid']['modName'];;

        if(!$this->get('panel.user')->ifRoll('ModUser','list')){
            $this->var['error']= array(
                'status'  => true,
                'message' => $this->var['lang']['not_roll']
            );
            return $this->render('PanelBundle:Default:index.html.twig', ['var' => $this->var]);
        }
        return $this->render('PanelBundle:Default:index.html.twig', ['var' => $this->var]);
    }
}
