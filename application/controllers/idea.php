<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Idea extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('idea');
    }


    public function add(){
        if(!$this->user->current) show_error('You need to be logged in to access this action');

        $subject     = trim($this->input->post('subject'));
        $description = trim($this->input->post('description'));

        if($this->input->post('save')){
            if(empty($subject) || empty($description)){
                $this->msg('Please fill in a subject and a detailed description',-1);
            }else{
                $id = $this->idea->add($subject, $description, $this->user->current->login);
                if($id !== false){
                    $this->msg('Your idea was added.',1);
                    redirect('idea/show/'.$id);
                }else{
                    $this->msg('Something went wrong, please try again',-1);
                }
            }
        }


        $this->load->view('idea-add', compact($subject, $description));
    }

    public function show($ideaID){
        $idea = $this->idea->get($ideaID);
        if(!$idea) show_404();

        $this->load->view('idea-show', array('idea' => $idea));
    }
}