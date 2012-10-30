<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {

    public function index(){

        if($this->input->post('go')){
            if($this->user->login(
                $this->input->post('login'),
                $this->input->post('password')
            )){
                //login successful, redirect home
                redirect();
            }else{
                $this->msg('Username or password was wrong. Please try again.',-1);
            }
        }

        $this->load->view('login');
    }


    public function logout(){
        $this->user->logout();
        $this->msg('You have been logged out.', 1);
        redirect();
    }
}