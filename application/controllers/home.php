<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index()	{

        if($this->user->login('adtest','a2mbFT6jrH9K')){
            $this->msg('logged in', 1);
        }else{
            $this->msg('not logged in', -1);
        }

        $this->load->model('idea');
        //$this->idea->add('Test', 'Some great idea', 'andi');

        $this->idea->vote(0,'andi',1);

        $ideas = $this->idea->fetch();
        $this->load->view('home', array('ideas' => $ideas));
	}
}
