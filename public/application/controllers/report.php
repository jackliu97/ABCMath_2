<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \ABCMath\Course\ABCClassManager;
use \ABCMath\Course\ABCClass;
use \ABCMath\Student\Student;
use \ABCMath\Student\StudentManager;
use \ABCMath\Course\Lesson;
use \ABCMath\Course\LessonManager;
use \ABCMath\Course\Assignment;
use \ABCMath\Course\AssignmentManager;

class Report extends CI_Controller {
    
    public $school_id;
    
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

        $this->semester_id = $this->session->userdata('semester_id');
    }
    
    public function index(){
        header('Location:' . site_url('students/list_view'));
    }

    /*
    * Print a single student's report card.
    */

    public function report_card($class_id, $student_id){

        $student = Student::get($student_id);
        $class = ABCClass::get($class_id);
        $grades = $class->getAllGradesForReport( $student_id );
        $assignment = new AssignmentManager();
        $types = $assignment->getAssignmentTypes();

        $this->load->library('pdf/ReportCardPDF');
        $this->reportcardpdf->SetStudent($student);
        $this->reportcardpdf->SetClass($class);
        $this->reportcardpdf->SetGrades($grades);
        $this->reportcardpdf->SetTypes($types);

        $this->reportcardpdf->AddPage();
        $this->reportcardpdf->DrawGradesHeader();
        $this->reportcardpdf->DrawGrades();
        $this->reportcardpdf->Output();

    }


    /*
    * Export all reports for a single class.
    */
    public function bulk_report_cards($class_id){

        $class = ABCClass::get($class_id);
        $student_manager = new StudentManager();
        $student_manager->class_id = $class_id;
        $student_manager->loadStudentsByClass();

        if(!count($student_manager->students)){
            return false;
        }

        foreach($student_manager->students as $student){
            $grades = $class->getAllGradesForReport( $student->id );
            $assignment = new AssignmentManager();
            $types = $assignment->getAssignmentTypes();

            $this->load->library('pdf/ReportCardPDF');
            $this->reportcardpdf->logo_url = base_url().'images/abcmath_logo.jpg';
            $this->reportcardpdf->SetStudent($student);
            $this->reportcardpdf->SetClass($class);
            $this->reportcardpdf->SetGrades($grades);
            $this->reportcardpdf->SetTypes($types);

            $this->reportcardpdf->AddPage();
            $this->reportcardpdf->DrawGradesHeader();
            $this->reportcardpdf->DrawGrades();

        }

        $this->reportcardpdf->Output();
    }
}


