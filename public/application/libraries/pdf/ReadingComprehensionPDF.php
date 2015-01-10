<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH.'libraries/pdf/PDF.php';

/*
* All the logic that goes into creating the attendance PDF.
*/

class ReadingComprehensionPDF extends PDF
{
    const MARGIN = 15;

    public $question;
    public $questionHeader;
    public $answer;
    public $count;

    public function __construct()
    {
        parent::__construct();
        $this->SetAutoPageBreak(true);
        $this->question = array();
        $this->examHeader = 'Reading Comprehension';
        $this->className = '';
        $this->answer = array();
        $this->count = 0;
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 80);
        $this->SetTextColor('225', '204', '204');
        $this->RotatedText(20, 40, 'ABCMath Academy', 305);
    }

    /**********
    * SETTERS *
    **********/

    public function examHeader()
    {
        $this->AddPage();
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(5);
        $this->Cell(0, 8, $this->examHeader, 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 4, $this->className, 0, 1, 'L');
        $this->Cell(0, 4, "Name: ", 0, 1, 'L');
        $this->Ln(5);
    }

    public function SetQuestion($question)
    {
        $this->question = $question;
    }

    /**************
    * DRAW STUFF. *
    ***************/

    public function DrawReadingMaterial($readingMaterial)
    {
        $this->SetLeftMargin(15);
        $this->SetRightMargin(15);
        $this->SetFont('Arial', '', 9);
        foreach ($readingMaterial as $i => $line) {
            $this->DrawReadingMaterialLine($line, ++$i);
        }
    }

    public function DrawReadingMaterialLine($line, $lineNum)
    {
        $this->Ln();

        $lineText = '';
        if ($lineNum%5 == 0) {
            $lineText = "Line ".$lineNum;
        }

        $this->Cell(12, 4, $lineText);
        $this->Cell(5, 4, " ");
        $this->MultiCell(0, 4, "{$line}", 0, 1);
    }

    public function DrawQuestionBody()
    {
        $this->SetLeftMargin(15);
        $this->SetRightMargin(15);
        $this->SetFont('Arial', '', 9);
        foreach ($this->question as $question) {
            $this->DrawReadingMaterial($question['reading_material']);
            $this->DrawQuestion($question['questions']);
            $this->AddPage();
        }

        $this->AddPage();
        $this->MultiCell(0, 4, 'Solutions', 0, 1);

        foreach ($this->answer as $answer) {
            $this->MultiCell(0, 4, $answer, 0, 1);
        }
    }

    public function DrawQuestion($question)
    {
        foreach ($question as $q) {
            if ($this->getY() > 200) {
                $this->AddPage();
            }
            $this->count += 1;
            $this->Ln();
            $this->Cell(5, 4, $this->count.'. ');
            $this->MultiCell(0, 4, $q['question'], 0, 1);

            foreach ($q['choices'] as $i => $c) {
                $this->Ln();
                $this->Cell(5, 4, " ");
                $this->Cell(5, 4, chr(65 + $i).'. ');
                $this->MultiCell(0, 4, $c['text'], 0, 1);
                if ($c['answer'] == '1') {
                    $this->answer[] = $this->count.'. '.chr(65 + $i);
                }
            }
        }
        $this->Ln();
        $this->Ln();
    }
}
