<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class material extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->User_Model->check_login()) {
            $this->session->sess_destroy();
            header('Location: /login');
        }
        ABCMath\Permission\Navigation::$config['quicklink'] = false;
    }

    public function index()
    {
        $data = array();

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('footer');
    }
}
