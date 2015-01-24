<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class common extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->User_Model->check_login() == false) {
            $this->session->sess_destroy();
            header('Location: /login');
        }
    }

    public function set_semester_id()
    {
        $semester_id = $this->input->post('semester_id');
        $this->session->set_userdata('semester_id', $semester_id);
        $this->load->view('response/json', array('json' => array('success' => true)));

        return true;
    }
}