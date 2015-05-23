<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\File\FileManager;


class File extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (($this->User_Model->check_login() === false) || 
            ($this->User_Model->in_group('Administrator') === false)) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

    }

    public function index(){}

    public function download($id){
        $fm = new FileManager();
        $file_path = $fm->load($id);
        $file_name = array_pop(explode('/', $file_path));

        $this->load->helper('file');
        $this->load->helper('download');

        force_download(
            array_pop(explode('/', $file_path)),
            read_file($file_path)
            );
    }
}
