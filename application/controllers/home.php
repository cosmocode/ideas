<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

    // how many hits per page
    const PERPAGE = 15;

    public function __construct() {
        parent::__construct();
        $this->load->model('idea');
    }

    public function index($offset = 0, $order = '') {
        $found = 0;
        $ideas = $this->idea->fetch(
            false,
            $order,
            Home::PERPAGE,
            $offset,
            $found
        );

        $this->load->view(
            'home',
            array(
                 'ideas'  => $ideas,
                 'order'  => $order,
                 'limit'  => Home::PERPAGE,
                 'found'  => $found,
            )
        );
    }

    public function newest($offset = 0) {
        $this->index($offset, 'new');
    }

    public function search($offset=0) {
        $found = 0;
        $ideas = $this->idea->search(
            $this->input->get('q'),
            Home::PERPAGE,
            $offset,
            $found
        );

        $this->load->view(
            'home',
            array(
                 'ideas'  => $ideas,
                 'order'  => 'search',
                 'query'  => $this->input->get('q'),
                 'limit'  => Home::PERPAGE,
                 'found'  => $found
            )
        );
    }
}
