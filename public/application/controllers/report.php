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

        $this->Classes_Model->class_id = $class_id;
        $student_ids = $this->Classes_Model->select_students_id_only();

        if(count($student_ids)){

            foreach($student_ids as $student_id){

                $this->Students_Model->id = $student_id;
                $student = $this->Students_Model->select_one();
                $attendance = $this->Students_Model->get_attendance($class_id);


                $this->Assignments_Model->student_id = $student_id;
                $this->Assignments_Model->class_id = $class_id;
                $grades = $this->Assignments_Model->get_grades_by_student_class();
                $this->Classes_Model->class_id = $class_id;
                $class = $this->Classes_Model->select_one();
                $types = $this->Common_Model->assignment_type_dropdown_options();
                $this->Lessons_Model->class_id = $class_id;
                $lessons = $this->Lessons_Model->select_all();


                $this->load->library('pdf/ReportCardPDF');
                $this->reportcardpdf->SetStudent($student);
                $this->reportcardpdf->SetClass($class);
                $this->reportcardpdf->SetGrades($grades);
                $this->reportcardpdf->SetTypes($types);
                $this->reportcardpdf->SetLessons($lessons);
                $this->reportcardpdf->SetAttendance($attendance);

                $this->reportcardpdf->AddPage();
                $this->reportcardpdf->DrawGradesHeader();
                $this->reportcardpdf->DrawGrades();
            }

            $this->reportcardpdf->Output();

        }

    }
}


