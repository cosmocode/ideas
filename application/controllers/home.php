<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index()	{


        $this->load->model('idea');
        //$this->idea->add('Test', 'Some great idea', 'andi');

        $this->idea->vote(0,'andi',1);

        $res = $this->idea->fetch();

        print_r($res);


		$this->load->view('home');
	}
}
