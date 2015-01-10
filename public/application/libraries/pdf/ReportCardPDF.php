<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'libraries/pdf/PDF.php');

/*
* All the logic that goes into creating the attendance PDF.
*/

class ReportCardPDF extends PDF{
	const MARGIN = 15;

	public $grades;
	public $student;
	public $class;
	public $types;
	public $lessons;
	public $attendance;

	public function __construct(){
		parent::__construct();
		$this->student 	= false;
		$this->grades 	= false;
		$this->class 	= false;
		$this->types 	= false;
		$this->lessons 	= false;
		$this->attendance = false;
	}

	public function Header(){

		$this->Image( base_url() . 'images/icons/abcmath_logo.jpg', self::MARGIN, 15, 20, 20);
		$this->Ln(5);
		$this->SetFont('Arial','B',12);
		$this->Cell(5);
		$this->Cell(0, 8, "ABCMath report card for {$this->student->first_name}  {$this->student->last_name}", 0, 1, 'C');

		$this->SetFont('Arial','B',10);
		$this->Cell(5);
		$this->Cell(0, 8, "{$this->class->term}", 0, 1, 'C');
		$this->Cell(0, 8, "{$this->class->external_id}", 0, 1, 'C');
	}

	public function Footer(){
		$this->SetFont('Arial', 'B', 5);
		$this->Text(self::MARGIN, 270, "ABCMath");
	}


	/**********
	* SETTERS *
	**********/

	public function SetClass($class){
		$this->class = $class;
	}

	public function SetStudent($student){
		$this->student = $student;
	}

	public function SetGrades($grades){
		$this->grades = $grades;
	}

	public function SetTypes($types){
		$this->types = $types;
	}

	public function SetLessons($lessons){
		$this->lessons = $lessons;
	}

	public function SetAttendance($attendance){
		$this->attendance = array();
		if(count($attendance)){
			foreach($attendance as $v){
				$this->attendance[$v->lesson_id]['present'] = $v->present;
				$this->attendance[$v->lesson_id]['tardy'] = $v->tardy; 
			}
		}
	}

	/**************
	* DRAW STUFF. *
	***************/

	public function DrawGradesHeader(){
		$this->SetFont('Arial','B',9);
		$this->Ln();
		$this->Cell(5);
		$this->Cell(40, 7, 'Lessons', 1, 0, 'C');

		foreach($this->types as $type){
			$this->Cell(20, 7, $type, 1, 0, 'C');
		}

		$this->Cell(20, 7, 'Attendance', 1, 1, 'C');

	}

	public function DrawGrades(){

		$grid = array();
		$average = array();

		foreach($this->lessons as $lesson){
			$grade_data = array();
			foreach($this->grades as $grade){
				if($grade->lesson_id == $lesson->id){
					$grade_data[$grade->assignment_type] = $grade;

					if(!array_key_exists($grade->assignment_type, $average)){
						$average[$grade->assignment_type]['total'] = $grade->grade;
						$average[$grade->assignment_type]['count'] = 1;
					}else{
						$average[$grade->assignment_type]['total'] += $grade->grade;
						$average[$grade->assignment_type]['count'] += 1;
					}
				}
			}
			$grid[$lesson->id . '__' . $lesson->lesson_date] = $grade_data;

			if(isset($this->attendance[$lesson->id])){
				$grid[$lesson->id . '__' . $lesson->lesson_date]['Attendance'] = $this->attendance[$lesson->id];
			}else{
				$grid[$lesson->id . '__' . $lesson->lesson_date]['Attendance'] = NULL;
			}
		}

		$week_num = 0;
		foreach($grid as $key=>$row){
			$week_num += 1;
			$lesson_info = explode('__', $key);
			$this->Cell(5);
			$this->Cell(40, 7, "(Week {$week_num}) {$lesson_info[1]}", 1, 0, 'C');

			foreach($this->types as $type){
				if(array_key_exists($type, $row)){
					$this->Cell(20, 7,$row[$type]->grade, 1, 0, 'C');
				}else{
					$this->Cell(20, 7,'', 1, 0, 'C');
				}
			}

			if($row['Attendance'] !==NULL){
				if($row['Attendance']['present'] == ''){
					$this->Cell(20, 7, 'Absent', 1, 1, 'C');
				}elseif($row['Attendance']['tardy'] == ''){
					$this->Cell(20, 7, 'Present', 1, 1, 'C');
				}else{
					$this->Cell(20, 7, 'Late', 1, 1, 'C');
				}
			}else{
				$this->Cell(20, 7, 'Absent', 1, 1, 'C');
			}
		}


		$this->Cell(5);
		$this->Cell(40, 7, "Average", 1, 0, 'C');
		
		foreach($this->types as $type){
			if(isset($average[$type])){
				$avg = number_format(($average[$type]['count'] === 0) ? 0 : ($average[$type]['total'] / $average[$type]['count']), 2);
			}else{
				$avg = '';
			}
			$this->Cell(20, 7, $avg, 1, 0, 'C');
		}

		$this->Cell(20, 7, '', 1, 1, 'C');
	}
}