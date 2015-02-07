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

    public function add_note(){

        $note_id = $this->input->post('note_id');
        $lesson_id = $this->input->post('lesson_id');
        $student_id = $this->input->post('student_id');

    }

    public function touch_note($lesson_id, $student_id){

        

    }

}
