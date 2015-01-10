<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class landing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->User_Model->check_login()) {
            $this->session->sess_destroy();
            header('Location: /login');
        }
    }

    public function index()
    {
        $this_group = $this->User_Model->get_groups();
        if (in_array('Teacher', $this_group)) {
            header('Location: /teacher_dashboard');
        } else {
            header('Location: /admin_dashboard');
        }
    }
}
