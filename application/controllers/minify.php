<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Minify extends MY_Controller {

    protected $NO_COMPANY_OK = true;

    /**
     * Compile the Stylesheets from LESS files
     *
     * Uses the company specific color replacements
     */
    public function css() {
        require_once(APPPATH . 'third_party/lessphp/lessc.inc.php');

        // default cache info
        $cache = FCPATH . 'data/style.css';
        $time1 = @filemtime(FCPATH . 'index.php');
        $time2 = $time1;

        // check cache age
        $ctime = @filemtime($cache);
        if (!$this->config->item('cachecss') || $ctime < $time1 || $ctime < $time2) {
            // cache is stale, rebuild

            $lc            = new lessc();
            $lc->importDir = array(FCPATH . 'css');
            // $lc->setFormatter('compressed');
            try {
                $css = $lc->parse(" @import 'init.less'; ");
                file_put_contents($cache, $css);
            } catch (Exception $e) {
                show_error($e->getMessage());
            }
        } else {
            // load from cache
            $css = file_get_contents($cache);
        }

        $this->output->set_content_type('text/css')->set_output($css);
    }

    public function js($group = 'default') {

        $group = preg_replace('/[^a-z]+/', '', $group);

        $files = array(
            'default' => array(
                'jquery-1.8.2.min.js',
                'bootstrap.min.js',
            ),
        );

        // default cache info
        $cache = FCPATH . 'data/' . $group . '.js';
        $time1 = @filemtime(FCPATH . 'index.php');
        // check cache age
        $ctime = @filemtime($cache);
        if (!$this->config->item('cachejs') || $ctime < $time1) {
            // cache is stale, rebuild

            $js = '';
            foreach ($files[$group] as $file) {
                $js .= "\n\n/* ---------------- $file ---------------- */\n\n";
                $js .= file_get_contents(FCPATH . 'js/' . $file);
            }

            /* JSMin sucks... we need to replace it later
            try {
                if($this->config->item('minifyjs')) $js = JSMin::minify($js);
            } catch (Exception $e) {
                show_error($e->getMessage());
            }
            */

            file_put_contents($cache, $js);
        } else {
            // load from cache
            $js = file_get_contents($cache);
        }

        $this->output->set_content_type('text/javascript; charset="utf-8"')->set_output($js);
    }
}
