<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('idea');
    }

    public function index($page=1, $order='')	{
        $limit = 15; // how many hits per page

        $found = 0;
        $ideas = $this->idea->fetch(
            false,
            $order,
            $limit,
            ($page-1)*$limit,
            $found
        );


        $this->load->view('home',
                          array(
                               'ideas'  => $ideas,
                               'order'  => $order,
                               'limit'  => $limit,
                               'found'  => $found,
                          ));
	}

    public function newest($page=1) {
        $this->index($page, 'new');
    }

    public function search() {
        $ideas = $this->idea->search($this->input->get('q'));
        $this->load->view('home',
                          array(
                               'ideas' => $ideas,
                               'order' => 'search',
                               'query' => $this->input->get('q')
                          ));
    }
}
