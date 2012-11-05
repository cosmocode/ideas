<?php
/**
 * Override default loader to use TWIG for views
 */
class MY_Loader extends CI_Loader {
    /**
     * @var MY_Controller
     */
    protected $ci;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Constructor
     *
     * Initialize the TWIG environment
     */
    public function __construct() {
        parent::__construct();

        $this->ci =& get_instance();

        require_once APPPATH.'third_party/Twig/lib/Twig/Autoloader.php';
        Twig_Autoloader::register();
        log_message('debug', 'twig autoloader loaded');

        $loader     = new Twig_Loader_Filesystem(APPPATH.'views');
        $this->twig = new Twig_Environment($loader, (array) config_item('twig'));

        // register functions and filters for use in TWIG templates:
        $this->twig->addFunction('base_url', new Twig_Function_Function('base_url'));
        $this->twig->addFunction('current_url', new Twig_Function_Function('current_url'));
        $this->twig->addFunction('site_url', new Twig_Function_Function('site_url'));
        $this->twig->addFunction('set_value', new Twig_Function_Function('set_value'));
        $this->twig->addFunction('print_r', new Twig_Function_Function('print_r'));

        $this->twig->addFunction('pagination', new Twig_Function_Function('pagination', array('is_safe' => array('html'))));
        $this->twig->addFilter('prettytext', new Twig_Filter_Function('prettytext', array('pre_escape' => 'html', 'is_safe' => array('html'))));
        $this->twig->addFilter('shorten', new Twig_Filter_Function('shorten'));
    }

    public function view($view, $vars = array(), $return = FALSE) {
        $vars += array(
            /*
         '_company'  => $this->ci->company->current(),
         '_imaging'  => $this->ci->imaging,*/

            '_user'        => $this->ci->user->current,
            '_messages'    => $this->ci->messages,
            '_cachebuster' => filemtime(FCPATH.'index.php'),
            '_idea_states' => $this->ci->config->item('idea_states'),
        );

        $content = $this->twig->loadTemplate("$view.twig")->render($vars);
        if($return) {
            return $content;
        } else {
            // at this place we assume some HTML will be shown and the messages will be as well, so we clear
            // the flash session
            $this->ci->session->unset_userdata('msg');

            $this->ci->output->set_output($content);
            return true;
        }
    }

    public function model($model, $name = '', $db_conn = FALSE) {
        if(is_array($model)) {
            foreach($model as $babe) {
                $this->model($babe);
            }
            return;
        }

        if(!$name) $name = strtolower($model);
        $model = $model.'_model';
        return parent::model($model, $name, $db_conn);
    }
}
