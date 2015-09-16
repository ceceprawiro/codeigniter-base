<?php defined('BASEPATH') or die();

class MY_Controller extends MX_Controller
{
    public $option = array();
    public $route = array();
    public $url = array();
    public $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->_set_option();
        $this->_set_route();
    }

    private function _set_option()
    {
        $result = $this->db->get('options');
        $_option = $result ? $result->result() : array();
        foreach ($_option as $_option) {
            $this->option[$_option->option_key] = $_option->option_value;
        }

        $this->option['theme'] = isset($this->option['theme']) ? $this->option['theme'] : 'default';
        $this->theme($this->option['theme']);
    }

    private function _set_route()
    {
        if (method_exists($this->router, 'fetch_module')) $this->route['module'] = $this->router->fetch_module();
        $this->route['controller'] = $this->router->fetch_class();
        $this->route['action'] = $this->router->fetch_method();
    }

    private function _set_url()
    {
        $this->url['base'] = base_url();

        $this->url['self'] = base_url();
        if (isset($this->route['module'])) $this->url['self'] .= $this->route['module'].'/';
        $this->url['self'] .= $this->route['controller'].'/'.$this->route['action'].'/';

        $this->url['theme'] = base_url()."themes/{$this->option['theme']}/";
    }

    public function theme($theme)
    {
        if ($this->option['theme'] != $theme) $this->option['theme'] = $theme;

        $this->twig->theme($this->option['theme']);
    }

    public function display($template, $use_twig = true)
    {
        $this->_set_url();

        if ($use_twig) {
            $this->twig->set('route', $this->route);
            $this->twig->set('url', $this->url);

            $this->twig->display($template, $this->data);
        } else {
            $this->load->view($template, $this->data);
        }
    }
}