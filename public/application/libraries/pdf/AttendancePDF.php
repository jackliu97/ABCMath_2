<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH.'libraries/pdf/PDF.php';

/*
* All the logic that goes into creating the attendance PDF.
*/

class AttendancePDF extends PDF
{
    const MAX_PAGE_WIDTH = 250;    //the maximum width (landscape) we are allowing the columns to exceed.
    const X_CHECKBOX_START = 40;    //the x-axis point where the checkbox and header date starts.
    const Y_CHECKBOX_START = 170;    //the y-axis point where we start drawing students and their checkboxes.

    public $class_data;
    public $lessons;
    public $students;

    public function __construct()
    {
        parent::__construct();
        $this->lessons = array();
        $this->students = array();
        $this->class_data = array();
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->RotatedText(190, 10, 'Attendance for '.$this->class_data->external_id, 270);

        $this->SetFont('Arial', 'B', 8);
        $this->RotatedText(187, 10, 'Class time: '.$this->class_data->start_time.' - '.$this->class_data->end_time, 270);
    }

    public function Footer()
    {
        $this->SetFont('Arial', 'B', 6);
        $this->RotatedText(10, 10, 'Attendance for '.$this->class_data->external_id, 270);
    }

    /**********
    * SETTERS *
    **********/

    public function SetClassData($class_data)
    {
        $this->class_data = $class_data;
    }

    public function SetLessons($lessons)
    {
        $this->lessons = $lessons;
    }

    /***************
    * DRAW METHODS *
    ***************/

    public function DrawHeaderRow()
    {
        if (!count($this->lessons)) {
            return false;
        }

        $delta_x = floor(self::MAX_PAGE_WIDTH / count($this->lessons)); //amount to increment to keep this in one page.
        $x = self::X_CHECKBOX_START;

        foreach ($this->lessons as $lesson) {
            $this->RotatedText(173, $x+2, $lesson->lesson_date, 315);
            $x += $delta_x;
        }
    }

    public function DrawStudentRow($student, $y)
    {
        if (!count($this->lessons)) {
            return false;
        }

        $x = self::X_CHECKBOX_START;
        $delta_x = floor(self::MAX_PAGE_WIDTH / count($this->lessons)); //amount to increment to keep this in one page.

        $this->RotatedText($y, 10, $student->last_name.', '.$student->first_name, 270);

        //checkboxes starts here.
        $this->Line($y-1, 10, $y-1, $x + 240);

        foreach ($this->lessons as $lesson) {
            $this->RotatedText($y, $x, '[   ]', 270);
            $x += $delta_x;
        }
    }
}
