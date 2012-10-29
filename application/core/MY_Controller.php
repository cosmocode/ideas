<?php
/**
 * Override the core CodeIgniter Controller
 *
 */
class MY_Controller extends CI_Controller {
    /**
     * @var array holds user messages
     */
    public $messages = array();


    /**
     * Constructor.
     */
    public function __construct(){
        parent::__construct();


        $msg = $this->session->userdata('msg');
        if ($msg) {
            $this->messages = unserialize($msg);
        }
    }


    /**
     * Set a message to be displayed
     *
     * @param string $text
     * @param int $type
     */
    public function msg($text,$type=0){
        if($type == -1){
            $type = 'error';
        }elseif($type == 0){
            $type = 'info';
        }elseif($type == 1){
            $type = 'success';
        }

        $this->messages[] = array(
            'text' => $text,
            'type' => $type
        );
        $this->session->set_userdata('msg',serialize($this->messages));
    }
}
