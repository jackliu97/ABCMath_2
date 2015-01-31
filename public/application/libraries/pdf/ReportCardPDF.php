<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH.'libraries/pdf/PDF.php';

use \ABCMath\Course\ABCClass;
use \ABCMath\Student\Student;

/*
* All the logic that goes into creating the attendance PDF.
*/

class ReportCardPDF extends PDF
{
    const MARGIN = 15;

    public $grades;
    public $student;
    public $class;
    public $types;
    public $attendance;

    public function __construct()
    {
        parent::__construct();
        $this->student    = false;
        $this->grades     = false;
        $this->class      = false;
        $this->types      = false;
        $this->attendance = false;
    }

    public function Header()
    {
        //$this->Image(base_url().'images/icons/abcmath_logo.jpg', self::MARGIN, 15, 20, 20);
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(5);
        $this->Cell(0, 8, "ABCMath report card for {$this->student->first_name}  {$this->student->last_name}", 0, 1, 'C');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(5);
        $this->Cell(0, 8, "{$this->class->term_id}", 0, 1, 'C');
        $this->Cell(0, 8, "{$this->class->external_id}", 0, 1, 'C');
    }

    public function Footer()
    {
        $this->SetFont('Arial', 'B', 5);
        //$this->Text(self::MARGIN, 270, "ABCMath");
    }

    /**********
    * SETTERS *
    **********/

    public function SetClass(ABCClass $class)
    {
        $this->class = $class;
    }

    public function SetStudent(Student $student)
    {
        $this->student = $student;
    }

    public function SetTypes(array $types)
    {
        $this->types = $types;
    }

    public function SetGrades(array $grades)
    {
        $this->grades = $grades;
    }

    public function SetAttendance(array $attendance)
    {
        $this->attendance = $attendance;
    }

    /**************
    * DRAW STUFF. *
    ***************/

    public function DrawGradesHeader()
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Ln();
        $this->Cell(10);
        $this->Cell(15, 7, 'Lesson', 1, 0, 'C');
        $this->Cell(25, 7, 'Date', 1, 0, 'C');
        $this->Cell(20, 7, 'Attendance', 1, 0, 'C');
        $this->Cell(80, 7, 'Assignment', 1, 0, 'C');
        $this->Cell(20, 7, 'Score', 1, 1, 'C');
    }

    public function DrawGrades()
    {
        $this->SetFont('Arial', '', 9);

        if(!count($this->grades)){
            return false;
        }

        foreach($this->grades as $lesson_id => $lesson){
            $lesson_number = $lesson['lesson_number'];
            $lesson_date = $lesson['lesson_date'];
            $attendance = '';
            $lesson_shown = false;
            $attendance_shown = false;

            if($lesson['present'] == 1){
                $attendance = 'Present';
            }

            if($lesson['present'] == 2){
                $attendance = 'Absent';
            }

            if($lesson['tardy']){
                $attendance = 'Late';
            }

            foreach($this->types as $type){
                if(!isset($lesson[$type])){
                    continue;
                }

                foreach($lesson[$type] as $assignment){
                    $grade = '';
                    if($assignment['grade']){
                        $grade = $assignment['grade'] . ' / ' . $assignment['maximum_score'];
                    }else{
                        if($attendance === ''){
                            continue;
                        }
                    }

                    $this->Cell(10);

                    if($lesson_shown === false){
                        $this->Cell(15, 7, "#{$lesson_number}", 1, 0, 'C');
                        $this->Cell(25, 7, $lesson_date, 1, 0, 'C');
                        $this->Cell(20, 7, $attendance, 1, 0, 'C');
                        $lesson_shown = true;
                    }else{
                        $this->Cell(60, 7, '', 1, 0, 'C');
                    }

                    $this->Cell(
                        80, 
                        7, 
                        $type . ', ' . $assignment['name'],
                        1,
                        0,
                        'L');

                    $this->Cell(20, 7, $grade, 1, 1, 'C');
                }

            }
        }
    }
}
