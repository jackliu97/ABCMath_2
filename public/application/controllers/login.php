<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        /*
        * check home.
        */
        $data = array(
            'redirect' => $this->input->get('redirect'),
            );

        $this->load->view('header');
        $this->load->view('login', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('login/base.js'),
                                        ));
    }

    public function check_login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $remember = $this->input->post('remember');
        $success = false;
        $message = '';
        $redirect = $this->input->post('redirect');

        $result = $this->User_Model->authenticate($email, $password, $remember);

        if ($redirect == '') {
            $redirect = '/landing';
        }

        $result['redirect'] = $redirect;

        $this->load->view('response/json', array('json' => $result));
    }

    public function logout()
    {
        $this->User_Model->logout();
        header('Location: /login');
    }
}

/* End of file login.php */
