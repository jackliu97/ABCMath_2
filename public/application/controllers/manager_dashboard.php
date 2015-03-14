<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
use \ABCMath\Course\ABCClassManager;
use \ABCMath\Student\Student;
use \ABCMath\Student\StudentManager;
use \ABCMath\Db\Datatable;

class Manager_Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (($this->User_Model->check_login() === false) || 
            ($this->User_Model->in_group('Manager') === false)) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

        $this->semester_id = $this->session->userdata('semester_id');
    }

    public function index()
    {
        $data = array();

        $user = Sentry::getUser();
        $data['user_email'] = $user->email;

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('dashboard/manager', $data);
        $this->load->view('footer', array(
                                        'private_js' => array(
                                            'dashboard/manager.js',
                                            'dashboard/all_students.js'
                                            ),
                                        'datatable' => true,
                                        ));
    }
}
