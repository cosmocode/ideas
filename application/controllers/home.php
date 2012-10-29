<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index()	{


        $this->load->model('idea');
        //$this->idea->add('Test', 'Some great idea', 'andi');

        $this->idea->vote(0,'andi',1);

        $ideas = $this->idea->fetch();
        $this->load->view('home', array('ideas' => $ideas));
	}
}
