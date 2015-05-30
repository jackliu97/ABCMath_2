<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
use \ABCMath\Course\ABCClassManager as ClassManager;
use \ABCMath\Course\ABCClass;
use \ABCMath\Template\Template;
use \ABCMath\Course\AssignmentManager;

class Grade_Dashboard extends CI_Controller
{
    protected $_template;

    public function __construct()
    {
        parent::__construct();

        if ($this->User_Model->check_login() == false) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

        if ($this->User_Model->check_permission('scaffolding') == false) {
            $this->editable = false;
        } else {
            $this->editable = true;
        }

        $this->_template = new Template(Template::FILESYSTEM);
        $this->semester_id = $this->session->userdata('semester_id');
    }

    public function grade($class_id = null)
    {
        $data = array();
        $class = new ABCClass();
        $class_manager = new ClassManager();
        $class_manager->semester_id = $this->semester_id;
        $class->id = $class_id;
        $class->load();
        $data['class'] = $class;
        $data['class_options'] = $class_manager->getAllClasses('options');

        $table_data = $class->getAllGrades2();

        $data['body_html'] = $this->_template->render(
            'Class/grades.twig',
            array(
                'grade_col_header' => json_encode($table_data['grade_col_header']),
                'grade_row_header' => json_encode($table_data['grade_row_header']),
                'grade_row_data' => json_encode($table_data['grade_row_data']),
                'grade_col_id_mapper' => json_encode($table_data['grade_col_id_mapper']),
                'grade_row_id_mapper' => json_encode($table_data['grade_row_id_mapper']),
                )
            );
            
        
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('dashboard/grade', $data);
        $this->load->view('footer', array(
                                        'handsontable' => true,
                                        'private_js' => array('dashboard/grades.js'),
                                        ));
    }

    public function save_delta(){
        $result = array('success'=>true);
        $delta = $this->input->post('delta');
        $col_mapper = $this->input->post('col_mapper');
        $row_mapper = $this->input->post('row_mapper');

        //delta format
        /*
    
        [
        '0' => row_number,
        '1' => col_number,
        '2' => previous_value,
        '3' => new_value
        ]
        */

        $grade_data = array(
            'student_id' => $row_mapper[$delta[0][0]],
            'assignment_id' => $col_mapper[$delta[0][1]],
            'grade_value' => $delta[0][3]
            );

        $manager = new AssignmentManager();
        $result = $manager->gradeOneAssignment($grade_data);


        $this->load->view('response/json', array('json' => $result));
    }

    public function process_grades()
    {
        $grade_data = $this->input->post('grade_data');

        $manager = new AssignmentManager();
        $result = $manager->gradeAssignments($grade_data);

        $this->load->view('response/json', array('json' => $result));
    }

    public function get_grades()
    {
        $class_id = $this->input->post('class_id');
        $return = array();

        $class = new ABCClass();
        $class->id = $class_id;

        try {
            $return['grade_data'] = $class->getAllGrades();
            $return['success'] = true;
        } catch (Exception $e) {
            $return['success'] = false;
            $return['message'] = $e->getMessage();
        }

        $this->load->view('response/json', array('json' => $return));
    }
}
