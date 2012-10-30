<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('idea');
    }

	public function index()	{
        $ideas = $this->idea->fetch(false, 'top');
        $this->load->view('home', array('ideas' => $ideas, 'order' => 'top'));
	}

    public function newest()	{
        $ideas = $this->idea->fetch(false, 'new');
        $this->load->view('home', array('ideas' => $ideas, 'order' => 'new'));
    }

}
