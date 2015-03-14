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
        $header = '/login';

        if(in_array('Administrator', $this_group)){
            $header = '/admin_dashboard';
        }

        else if(in_array('Manager', $this_group)){
            $header = '/manager_dashboard';
        }

        else if(in_array('Teacher Assistance', $this_group)){
            $header = '/ta_dashboard';
        }

        else{
            //logout.
            $this->session->sess_destroy();
        }

        header("Location: {$header}");
    }
}
