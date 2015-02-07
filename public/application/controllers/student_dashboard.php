<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
use \ABCMath\Student\Student;
use \ABCMath\Student\StudentManager;
use \ABCMath\Db\Datatable;
use \ABCMath\Template\Template;

class Student_Dashboard extends CI_Controller
{

    protected $_template;
    public function __construct()
    {
        parent::__construct();
        if ($this->User_Model->check_permission('scaffolding') == false) {
            $this->editable = false;
        } else {
            $this->editable = true;
        }
        $this->_template = new Template(Template::FILESYSTEM);
        $this->semester_id = $this->session->userdata('semester_id');
    }

    public function info($student_id)
    {
        $data = array();
        $student = new Student();
        $student->setId($student_id);
        $student->load();

        $data['student'] = $student;
        $data['editable'] = $this->editable;
        $data['note_modal'] = $this->_template->render(
            'Modal/note.twig',
            array('modal_title'=>'Notes for ' . $student->first_name));

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('dashboard/student', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('dashboard/student.js'),
                                        'datatable' => true,
                                        ));
    }

    public function get_classes($student_id)
    {
        $result = array('success' => false, 'message' => '');
        $student = new Student();
        $student->semester_id = $this->semester_id;
        $student->setId($student_id);
        $dt = new Datatable();
        $dt->sql = $student->getAllClassesSQL();

        $dt->columns = array(    'id',
                                'description',
                                'subject',
                                'teacher',
                                'start_time',
                                'end_time',
                                'days', );

        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $class_id = $col;
                    }

                    $result['aaData'][$key][$k] =
                            "<a href='/class_dashboard/info/{$class_id}' class='pointer'>".
                            "{$result['aaData'][$key][$k]}</a>";
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
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

    public function all_students()
    {
        $student_manager = new StudentManager();
        $all_students = $student_manager->getAllStudentsInfoForDashboard();
        $return = array(
            'success' => true,
            'students' => array(),
            );

        foreach ($all_students as $student) {
            $return['students'][] = array(
                        'label' => $student['student_name'],
                        'value' => $student['student_id'],
                        );
        }

        $this->load->view('response/json', array('json' => $return));
    }
}
