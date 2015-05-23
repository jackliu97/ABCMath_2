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

    const HEADER_CELL_HEIGHT = 4;
    const BODY_CELL_HEIGHT = 4;

    const HEADER_FONT = 7;
    const BODY_FONT = 7;

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
        $this->logo_url   = false;
    }

    public function Header()
    {   
        if($this->logo_url !== false){
            $this->Image($this->logo_url, self::MARGIN, 15, 20, 20);
        }
        
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(5);
        $this->Cell(0, 8, "ABCMath report card for {$this->student->first_name}  {$this->student->last_name}", 0, 1, 'C');

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(5);
        $semister = $this->class->getSemester();
        $this->Cell(0, 8, $semister['description'], 0, 1, 'C');
        $this->Cell(0, 8, $this->class->description, 0, 1, 'C');
    }

    public function Footer()
    {
        $this->SetFont('Arial', 'B', 5);
        //$this->Text(self::MARGIN, 270, "ABCMath");
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
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
        $this->SetFont('Arial', 'B', self::HEADER_FONT);
        $this->Ln();
        $this->Cell(10);
        $this->Cell(15, self::HEADER_CELL_HEIGHT, 'Lesson', 1, 0, 'C');
        $this->Cell(25, self::HEADER_CELL_HEIGHT, 'Date', 1, 0, 'C');
        $this->Cell(20, self::HEADER_CELL_HEIGHT, 'Attendance', 1, 0, 'C');
        $this->Cell(80, self::HEADER_CELL_HEIGHT, 'Assignment', 1, 0, 'C');
        $this->Cell(20, self::HEADER_CELL_HEIGHT, 'Score', 1, 1, 'C');
    }

    public function DrawGrades()
    {
        $this->SetFont('Arial', '', self::BODY_FONT);

        if(!count($this->grades)){
            return false;
        }

        $now = new DateTime();

        foreach($this->grades as $lesson_id => $lesson){
            $lesson_number = $lesson['lesson_number'];
            $lesson_date = new DateTime($lesson['lesson_date']);
            $lesson_date_formatted = $lesson_date->format('M j, Y');

            //only need to show reports for lesson up to today.
            if($now <= $lesson_date){
                continue;
            }

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
                    }



                    $this->Cell(10);

                    if($lesson_shown === false){
                        $this->Cell(15, self::BODY_CELL_HEIGHT, "#{$lesson_number}", 1, 0, 'C');
                        $this->Cell(25, self::BODY_CELL_HEIGHT, $lesson_date_formatted, 1, 0, 'C');
                        $this->Cell(20, self::BODY_CELL_HEIGHT, $attendance, 1, 0, 'C');
                        $lesson_shown = true;
                    }else{
                        $this->Cell(60, self::BODY_CELL_HEIGHT, '', 1, 0, 'C');
                    }

                    $this->Cell(
                        80, 
                        self::BODY_CELL_HEIGHT, 
                        $type . ', ' . $assignment['name'],
                        1,
                        0,
                        'L');

                    $this->Cell(20, self::BODY_CELL_HEIGHT, $grade, 1, 1, 'C');
                }

            }
        }
    }
}
