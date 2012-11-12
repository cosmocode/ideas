<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Vote extends MY_Controller {

    public function index() {
        $ideaIDs = $this->input->post('request');
        $this->load->model('idea');

        if($this->user->current) {
            $login = $this->user->current->login;
        } else {
            $login = '';
        }

        $result = array();

        $res = $this->idea->votes($ideaIDs, $login);
        if($res) foreach($res as $row) {
            $result[$row->idea] = array(
                'votes' => (int) $row->votes,
                'mine'  => (int) $row->myvote
            );
        }


        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    public function cast($ideaID) {
        $vote = (int) $this->input->post('vote');
        $this->load->model('idea');

        if(!$this->user->current) {
            // we have no user, so ignore the vote and restore current values
            $result = array(
                'idea'  => $ideaID,
                'votes' => 0,
                'mine'  => 0,
                'error' => 0
            );

            $res = $this->idea->votes(array($ideaID));
            if($res){
                $result = array_merge($result, (array) $result[0]);
            }
        } else {
            // cast the vote
            $login = $this->user->current->login;
            $votes = $this->idea->vote($ideaID, $login, $vote);

            $result = array(
                'idea'  => $ideaID,
                'votes' => $votes,
                'mine'  => $vote,
                'error' => 0
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }
}