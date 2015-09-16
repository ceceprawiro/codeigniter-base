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
 * @category    Config
 * @author      Edmundas Kondrašovas <as@edmundask.lt>
 * @author      Indra Ginn <indra.ginn@gmail.com>
 * @license     http://www.opensource.org/licenses/MIT
 * @version     0.8.5
 * @copyright   Copyright (c) 2012 Edmundas Kondrašovas <as@edmundask.lt>
 */

/*
| -------------------------------------------------------------------
| Environment options
| -------------------------------------------------------------------
|
| These are all twig-specific options that you can set. To learn more
| about each option, check the official documentation.
|
| NOTE: cache option works slightly differently than in Twig. In Twig
| you can either set the value to FALSE to disable caching, or set
| the path to where the cached files should be stored (which means
| caching would be enabled in that case). This is not entirely
| convenient if you need to switch between enabled or disabled
| caching for debugging or other reasons.
|
| Therefore, here the value can be either TRUE or FALSE. Cache
| directory can be set separately.

| debug:
|   Boolean (default = false)
|   When set to true, it automatically set "auto_reload" to true as
|   well
|
| charset:
|   String (default = 'utf-8')
|   The charset used by the templates.
|
| base_template_class:
|   String (default = 'Twig_Template')
|   The base template class to use for generated templates.
|
| cache:
|   Boolean (false) or String (default = false)
|   An absolute path where to store the compiled templates, or false
|   to disable compilation cache.
|
| auto_reload:
|   Boolean (default = null)
|   Whether to reload the template if the original source changed.
|   If you don't provide the autoreload option, it will be determined
|   automatically based on the debug value.
|
| strict_variables:
    Boolean, default = false
|   If set to false, Twig will silently ignore invalid variables
|   (variables and or attributes/methods that do not exist) and
|   replace them with a null value. When set to true, Twig throws an
|   exception instead.
|
| autoescape:
|   Boolean, default = true
|   If set to true, auto-escaping will be enabled by default for all
|   templates. As of Twig 1.8, you can set the escaping strategy to
|   use (html, js, false to disable). As of Twig 1.9, you can set the
|   escaping strategy to use (css, url, html_attr, or a PHP callback
|   that takes the template "filename" and must return the escaping
|   strategy to use -- the callback cannot be a function name to
|   avoid collision with built-in escaping strategies).
|
| optimizations:
|   Integer, default = -1
|   A flag that indicates which optimizations to apply
|   (-1 -- all optimizations are enabled; set it to 0 to disable).
|
*/

$config['twig']['environment']['debug']               = true;
$config['twig']['environment']['charset']             = 'utf-8';
$config['twig']['environment']['base_template_class'] = 'Twig_Template';
$config['twig']['environment']['cache']               = false;
$config['twig']['environment']['auto_reload']         = null;
$config['twig']['environment']['strict_variables']    = false;
$config['twig']['environment']['autoescape']          = true;
$config['twig']['environment']['optimizations']       = -1;


/*
| -------------------------------------------------------------------
| Themes Base Dir
| -------------------------------------------------------------------
|
| Directory where themes are located at. This path is relative to
| CodeIgniter's base directory OR module's base directory.
|
| For example:
| $config['themes_base_dir'] = 'themes/';
|
| It will actually mean that themes should be placed at:
|
| {APPPATH}/themes/ and {APPPATH}/modules/{some_module}/themes/.
|
| NOTE: modules do not necessarily need to be in {APPPATH}/modules/
| as Twiggy will figure out the paths by itself. That way you can
| package your modules with themes.
|
| Also, do not forget the trailing slash!
|
*/

$config['twig']['theme_dir'] = 'themes/';


/*
| -------------------------------------------------------------------
| Twig Cache Dir
| -------------------------------------------------------------------
|
| Path to the cache folder for compiled twig templates. It is
| relative to CodeIgniter's base directory.
|
*/

$config['twig']['cache_dir'] = APPPATH . 'cache/twig/';


/*
| -------------------------------------------------------------------
| Default theme
| -------------------------------------------------------------------
*/

$config['twig']['default_theme'] = 'default';


/*
| -------------------------------------------------------------------
| Default layout
| -------------------------------------------------------------------
*/

$config['twig']['default_layout'] = 'index';


/*
| -------------------------------------------------------------------
| Default template
| -------------------------------------------------------------------
*/

$config['twig']['default_template'] = 'index';


/*
| -------------------------------------------------------------------
| Template file extension
| -------------------------------------------------------------------
|
| This lets you define the extension for template files. It doesn't
| affect how Twiggy deals with templates but this may help you if you
| want to distinguish different kinds of templates.
|
| For example, for CodeIgniter you may use *.html.twig template files
| and *.html.jst for js templates.
|
*/
$config['twig']['file_extension'] = 'twig';


/*
| -------------------------------------------------------------------
| Syntax Delimiters
| -------------------------------------------------------------------
|
| If you don't like the default Twig syntax delimiters or if they
| collide with other languages (for example, you use handlebars.js
| in your templates), here you can change them.
|
| Ruby erb style:
|
|   'tag_comment'   => array('<%#', '#%>'),
|   'tag_block'     => array('<%', '%>'),
|   'tag_variable'  => array('<%=', '%>')
|
| Smarty style:
|
|    'tag_comment'  => array('{*', '*}'),
|    'tag_block'    => array('{', '}'),
|    'tag_variable' => array('{$', '}'),
|
*/

$config['twig']['delimiters'] = array(
    'tag_comment'   => array('{#', '#}'),
    'tag_block'     => array('{%', '%}'),
    'tag_variable'  => array('{{', '}}'),
);


/*
|--------------------------------------------------------------------------
| Auto-reigster functions
|--------------------------------------------------------------------------
|
| Here you can list all the functions that you want Twiggy to automatically
| register them for you.
|
| NOTE: only registered functions can be used in Twig templates.
|
*/

$config['twig']['functions'] = array(
    'base_url',
    'site_url',
    'lang',

    'form_open',
    'form_close',
    'form_label',
    'form_input',
    'form_password',
    'form_checkbox',
    'form_dropdown',
    'form_textarea',
    'form_button',
    'form_submit',
);