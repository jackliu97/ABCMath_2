<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\Student\Student;
use \ABCMath\Student\StudentManager;
use \ABCMath\Db\Datatable;

class note_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->User_Model->check_login()) {
            $this->session->sess_destroy();
            header('Location: /login');
        }
    }

    public function save_notes()
    {
        $student_id = $this->input->post('student_id');
        $lesson_id = $this->input->post('lesson_id');
        $notes = $this->input->post('notes');
        $note_id = $this->input->post('note_id');
        $return = array(
            'success' => true,
            'students' => array(),
            );

        $student = new Student();
        $student->id = $student_id;
        $return = $student->saveNote($notes, $this->User_Model->get_user()->id, $note_id, $lesson_id);

        $this->load->view('response/json', array('json' => $return));
    }

    public function delete_note()
    {
        $note_id = $this->input->post('note_id');
        $student = new Student();

        $return = $student->deleteNote($note_id);

        $this->load->view('response/json', array('json' => $return));
    }

    public function get_one_note()
    {
        $note_id = $this->input->post('note_id');
        $return = array();
        $student = new Student();
        $notes = $student->getNote($note_id);
        $return['success'] = true;
        $return['notes'] = $notes['notes'];
        $return['notes_parsed'] = nl2br($notes['notes']);

        $this->load->view('response/json', array('json' => $return));
    }

    public function get_all_notes($student_id)
    {
        $result = array('success' => false, 'message' => '');

        $student = new Student();
        $student->setId($student_id);

        $dt = new Datatable();
        $dt->sql = $student->getAllNotesSQL();
        $dt->columns = array(    'note_id',
                                'creation_datetime',
                                'update_timestamp',
                                'email',
                                'notes', );

        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $note_id = $col;
                        $result['aaData'][$key][$k] =
                            "<button note_id='{$note_id}' class='btn remove_note glyphicon glyphicon-remove' style='width:45px;'>".
                            "</button>&nbsp;".
                            "<button note_id='{$note_id}' class='btn note_detail glyphicon glyphicon-chevron-down' style='width:45px;'>".
                            "</button>";
                    } else {
                        $result['aaData'][$key][$k] =
                            "<a note_id='{$note_id}' class='edit_note'>".
                            "{$result['aaData'][$key][$k]}</a>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

}
