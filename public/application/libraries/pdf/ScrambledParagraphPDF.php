<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'libraries/pdf/PDF.php');

/*
* All the logic that goes into creating the attendance PDF.
*/

class ScrambledParagraphPDF extends PDF{
	const MARGIN = 15;

	public $question;
	public $questionHeader;
	public $answer;

	public function __construct(){
		parent::__construct();
		$this->SetAutoPageBreak(true);
		$this->question = array();
		$this->examHeader = 'Scrambled Paragraph';
		$this->className = '';
		$this->answer = array();
	}

	public function Header(){
		$this->SetFont('Arial','B',80);
		$this->SetTextColor('225', '204', '204');
		$this->RotatedText(20, 40, 'ABCMath Academy', 305);
	}


	/**********
	* SETTERS *
	**********/

	public function examHeader(){
		$this->AddPage();
		$this->Ln(5);
		$this->SetFont('Arial','B',12);
		$this->Cell(5);
		$this->Cell(0, 8, $this->examHeader, 0, 1, 'C');

		$this->SetFont('Arial','',10);
		$this->Cell(0, 4, $this->className, 0, 1, 'L');
		$this->Cell(0, 4, "Name: ", 0, 1, 'L');
		$this->Ln(5);
	}

	public function SetQuestion($question){
		$this->question = $question;
	}

	/**************
	* DRAW STUFF. *
	***************/

	public function DrawQuestionBody(){
		$this->SetLeftMargin(15);
		$this->SetRightMargin(15);
		$this->SetFont('Arial','',9);
		foreach($this->question as $question){
			$this->DrawQuestion($question);
		}

		$this->AddPage();
		$this->MultiCell(0,4, 'Solutions' ,0,1);
		foreach($this->answer as $i=>$answer){
			$num = $i + 1;
			$this->Cell(5, 4, $num);
			$this->MultiCell(0,4, implode(',', $answer) ,0,1);
		}
	}

	public function DrawQuestion($question){
		if($this->getY() > 200){
			$this->AddPage();
		}

		$this->MultiCell(0,4,$question['header'],0,1);
		foreach($question['answer'] as $answer){
			$this->Ln();
			$this->Cell(12, 4, "______ ({$answer['choice']})");
			$this->Cell(5, 4, " ");
			$this->MultiCell(0,4,"{$answer['line']}",0,1);
		}

		$this->answer []= $question['solution_alpa'];

		$this->Ln();
		$this->Ln();
	}
}