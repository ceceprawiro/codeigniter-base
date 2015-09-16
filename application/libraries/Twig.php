<?php defined('BASEPATH') or die();

/**
 * Twig - Twig template engine implementation for CodeIgniter
 *
 * Modified from Twiggy by Edmundas Kondrašovas.
 *
 * Twig is not just a simple implementation of Twig template engine
 * for CodeIgniter. It supports themes, layouts, templates for regular
 * apps and also for apps that use HMVC (module support).
 *
 * @package     CodeIgniter
 * @subpackage  Twig
 * @category    Libraries
 * @author      Edmundas Kondrašovas <as@edmundask.lt>
 * @author      Indra Ginn <indra.ginn@gmail.com>
 * @license     http://www.opensource.org/licenses/MIT
 * @version     0.8.5
 * @copyright   Copyright (c) 2012 Edmundas Kondrašovas <as@edmundask.lt>
 */

class Twig
{
    /**
     * Reference to code CodeIgniter instance.
     * @var codeIgniter object
     */
    private $_CI;

    /**
     * Configuration for this Twig library
     * @var array
     */
    private $_config;

    /**
     * Locations for templates
     * @var Array
     */
    private $_template_locations;

    /**
     * Holds for Twig_Loader_Filesystem object
     * @var Twig_Loader_Filesystem object
     */
    private $_twig_loader;

    /**
     * Holds for Twig configuration object
     * @var Twig_Environment object
     */
    private $_twig;

    /**
     * Theme name
     * @var String
     */
    private $_theme;

    /**
     * Layout filename
     * @var String
     */
    private $_layout;

    /**
     * Template filename
     * @var String
     */
    private $_template;

    /**
     * Data to pass to template
     * @var Array of mixed
     */
    private $_data = array();

    /**
     * Holds for Twig global variables
     * @var Array of mixed
     */
    private $_global = array();

    /**
     * The constructor
     */
    public function __construct()
    {
        // Get reference to CodeIgniter Instance
        $this->_CI =& get_instance();

        // Get configuration
        $this->_CI->config->load('twig');
        $this->_config = $this->_CI->config->item('twig');

        // Register Twig auto-loader
        /**
         * If you are not using Composer, use the Twig built-in autoloader:
         * require_once '/path/to/lib/Twig/Autoloader.php';
         */
        Twig_Autoloader::register();

        // Setup template location for default theme
        $this->_set_template_locations($this->_config['default_theme']);

        // Specify where to look for templates
        try {
            $this->_twig_loader = new Twig_Loader_Filesystem($this->_template_locations);
        } catch (Twig_Error_Loader $e) {
            log_message('error', 'Twig: Failed to load the default theme');
            show_error($e->getRawMessage());
        }

        // Initialize Twig environment
        try {
            $this->_config['environment']['cache'] = $this->_config['environment']['cache'] ? $this->_config['cache_dir'] : false;
            $this->_twig = new Twig_Environment($this->_twig_loader, $this->_config['environment']);
        } catch (Twig_Error $e) {
            log_message('error', 'Twig: Failed to initialize environment');
            show_error($e->getRawMessage());
        }

        // Register CI standard functions
        $this->_register_ci_functions();

        // Initialize defaults
        $this->theme($this->_config['default_theme'])
             ->layout($this->_config['default_layout'])
             ->template($this->_config['default_template']);
    }

    /**
     * Set template locations
     * @access private
     * @param  string  $theme Name of theme to load
     * @return void
     */
    private function _set_template_locations($theme)
    {
        // Check if HMVC is installed.
        // NOTE: there may be a simplier way to check it but this seems good enough.
        if (method_exists($this->_CI->router, 'fetch_module')) {
            $module = $this->_CI->router->fetch_module();

            // Only if the current page is served from a module we need to add extra template locations.
            if (! empty($module)) {
                foreach (Modules::$locations as $location => $offset) {
                    if (is_dir($location.$module.'/views/'))
                        // /application/modules/<module name>/views/
                        $this->_template_locations[] = $location.$module.'/views/';
                }
            }
        }

        if (! is_null($this->_template_locations)) {
            // No duplications
            $this->_template_locations = array_unique(array_filter($this->_template_locations, function($element)
            {
                if (strpos($element, $this->_config['theme_dir']) === false) return $element;
            }));
        }

        if (! is_array($this->_template_locations))
            $this->_template_locations = array();

        // /application/views/
        $this->_template_locations[] = APPPATH.'views/';

        // /themes/<theme name>
        array_unshift($this->_template_locations, FCPATH.$this->_config['theme_dir'].$theme);

        $this->_theme = $theme;

        // Reset the paths if needed.
        if (is_object($this->_twig_loader))
            $this->_twig_loader->setPaths($this->_template_locations);
    }

    /**
     * Register CI standard functions
     * @access private
     * @return Object Instance of this class
     */
    private function _register_ci_functions()
    {
        if (count($this->_config['functions']) > 0) {
            foreach ($this->_config['functions'] as $function)
                $this->_twig->addFunction(new Twig_SimpleFunction($function, $function));
        }

        return $this;
    }

    /**
     * Load theme
     * @access public
     * @param  String $theme Name of theme to load
     * @return Object        Instance of this class
     */
    public function theme($theme)
    {
        if (! is_dir(realpath($this->_config['theme_dir'].$theme))) {
            log_message('error', 'Twig: requested theme '.$theme .' has not been loaded because it does not exist.');
            show_error("Theme does not exist in {$this->_config['theme_dir']}{$theme}.");
        }

        if ($theme != $this->_theme) {
            $this->_theme = $theme;
            $this->_set_template_locations($theme);
        }

        return $this;
    }

    /**
     * Set layout file
     * @access public
     * @param  String $layout         Layout filename
     * @param  String $file_extension Layout file extension (default = null, use file extension defined in config file)
     * @return Object                 Instance of this class
     */
    public function layout($layout, $file_extension = null)
    {
        if (is_null($file_extension))
            $file_extension = $this->_config['file_extension'];

        $this->_layout = $layout;
        $this->_twig->addGlobal('_layout', '_layouts/'. $this->_layout.'.'.$file_extension);

        return $this;
    }

    /**
     * Set template file
     * @access public
     * @param  String $template       Template filename
     * @return Object                 Instance of this class
     */
    public function template($template)
    {
        $this->_template = $template;

        return $this;
    }

    /**
     * Set syntax delimiters
     * @access public
     * @param  Array $delimiters Syntax delimiter
     * @return Object            Instance of this class
     */
    public function set_delimiters($delimiters = null)
    {
        try {
            if (is_null($delimiters))
                $delimiters = $this->_config['delimiters'];

            $this->_twig->setLexer(new Twig_Lexer($this->_twig, $delimiters));
        } catch (Twig_Error $e) {
            log_message('error', 'Twig: Failed to change delimiters');
            show_error($e->getRawMessage());
        }

        return $this;
    }

    /**
     * Load template
     * @access private
     * @return Twig_TemplateInterface A template instance representing the given template name
     */
    private function _load()
    {
        return $this->_twig->loadTemplate($this->_template.'.'.$this->_config['file_extension']);
    }

    /**
     * Render template
     * @access public
     * @param  String $template Template filename (default = null, use template describe in _template property of this class)
     * @param  Array  $data     Extra variables to pass to template
     * @return String           The rendered template (compiled HTML)
     */
    public function render($template = null, $data = array())
    {
        if (! is_null($template))
            $this->template($template);

        try {
            $data = array_merge($this->_data, $data);
            $this->_load()->render($data);
        } catch (Twig_Error_Loader $e) {
            log_message('error', "Twig: failed render $template.");
            show_error($e->getRawMessage());
        }
    }
    /**
     * Display rendered template (compiled HTML)
     * @access public
     * @param  String $template Template filename (default = null, use template describe in _template property of this class)
     * @param  Array  $data     Extra variables to pass to template
     * @return void
     */
    public function display($template = null, $data = array())
    {
        // Set delimiters
        $this->set_delimiters($this->_config['delimiters']);

        if (! is_null($template))
            $this->template($template);

        try {
            $data = array_merge($this->_data, $data);
            $this->_load()->display($data);
        } catch (Twig_Error_Loader $e) {
            log_message('error', "Twig: failed render $template.");
            show_error($e->getRawMessage());
        }
    }





    /**
     * Get current theme
     * @access public
     * @return String Name of the currently loaded theme
     */
    public function get_theme()
    {
        return $this->_theme;
    }

    /**
     * Get current layout filename
     * @access public
     * @return String Name of the currently used layout file
     */
    public function get_layout()
    {
        return $this->_layout;
    }

    /**
     * Get current template filename
     * @access public
     * @return String Name of the loaded template file
     */
    public function get_template()
    {
        return $this->_template;
    }

    /**
     * Set data
     * @access public
     * @param  Mixed   $variable Variable name or array of variable names with value
     * @param  Mixed   $value    Data (default = null)
     * @param  Boolean $global   Mark variables as global variable (default = false)
     * @return Object            Instance of this class
     */
    public function set($variable, $value = null, $global = false)
    {
        if (is_array($variable)) {
            foreach ($variable as $variable_name => $variable_value)
                $this->set($variable_name, $variable_value, $global);
        } else {
            if ($global) {
                $this->_twig->addGlobal($variable, $value);
                $this->_global[$variable] = $value;
            } else {
                $this->_data[$variable] = $value;
            }
        }

        return $this;
    }

    /**
     * Unset a particular variable
     * @access public
     * @param  Mixed  $variable Variable to unset
     * @return Object           Instance of this class
     */
    public function unset_data($variable)
    {
        if (isset($this->_data[$variable]))
            unset($this->_data[$variable]);

        if (isset($this->_global[$variable]))
            unset($this->_global[$variable]);

        return $this;
    }

    /**
     * Registers a function
     * @access public
     * @param  String $function_name The function name
     * @return Object                Instance of this class
     */
    public function register_function($function_name)
    {
        // As of Twig 1.x, use Twig_SimpleFunction
        // see: http://twig.sensiolabs.org/doc/deprecated.html#functions
        $this->_twig->addFunction(new Twig_SimpleFunction($function_name, $function_name));

        return $this;
    }

    /**
     * Registers filter
     * @access public
     * @param  String $filter_name The filter ame
     * @return Object              Instance of this class
     */
    public function register_filter($filter_name)
    {
        // As of Twig 1.x, use Twig_SimpleFunction
        // see: http://twig.sensiolabs.org/doc/deprecated.html#filters
        $this->_twig->addFilter(new Twig_SimpleFilter($filter_name, $filter_name));

        return $this;
    }
}