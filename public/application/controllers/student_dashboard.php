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
        if (($this->User_Model->check_login() === false)) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

        $this->editable = false;

        if($this->User_Model->in_group('Administrator') || 
            $this->User_Model->in_group('Manager') ||
            $this->User_Model->in_group('Teacher Assistance')){
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

    public function students(){
        $data = array();

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('dashboard/all_students', $data);
        $this->load->view('footer', array(
                                        'private_js' => array(
                                            'dashboard/all_students.js'
                                            ),
                                        'datatable' => true,
                                        ));
    }

    public function get_students($type = '')
    {
        $method = "getAll{$type}StudentsSQL";        
        $student_manager = new StudentManager();
        $student_manager->semester_id = $this->semester_id;
        $dt = new Datatable();
        $dt->sql = $student_manager->{$method}(new DateTime('now'));
        $dt->columns = array(    'student_id',
                                'external_id',
                                'name',
                                'email',
                                'telephone',
                                'class_name', );
        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $student_id = $col;
                    }
                    $result['aaData'][$key][$k] =
                        "<a href='/student_dashboard/info/{$student_id}'>".
                        "{$result['aaData'][$key][$k]}</a>";
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function check_student()
    {
        $student_id = $this->input->post('student_id');
        $student = new Student();
        $student->setId($student_id);
        $return = array('success' => $student->load());

        $this->load->view('response/json', array('json' => $return));
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
