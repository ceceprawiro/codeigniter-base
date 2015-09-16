<?php defined('BASEPATH') or die();

class Post extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->display('post/index');
    }
}