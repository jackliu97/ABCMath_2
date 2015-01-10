<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_Exam extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if($this->User_Model->check_login() == false){
			$this->session->sess_destroy();
			header('Location: /login');
		}
		$this->load->model(array('ScrambledParagraph_Model', 'ReadingComprehension_Model'));
	}

	public function index(){
		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('exam/create');
		$this->load->view('footer', array(
										'private_js'=> array('exam/base.js')
										));
	}

	public function scrambled_paragraph($exam_id){

		$data = array();
		$questions = $this->ScrambledParagraph_Model->generate_exam($exam_id);
		$data['questions'] = $this->format_sp_exam_questions($questions);
		$data['exam_id'] = $exam_id;

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('exam/create_scrambled_paragraph', $data);
		$this->load->view('footer', array(
										'private_js'=> array('exam/scrambled_paragraph/base.js')
										));
	}

	public function scrambled_paragraph_pdf($exam_id){

		$question = $this->ScrambledParagraph_Model->generate_exam($exam_id);
		$question = $this->format_sp_exam_questions($question);

		$this->load->library('pdf/ScrambledParagraphPDF');
		$this->scrambledparagraphpdf->examHeader();
		$this->scrambledparagraphpdf->SetQuestion($question);
		$this->scrambledparagraphpdf->DrawQuestionBody();
		$this->scrambledparagraphpdf->Output();
	}

	public function reading_comprehension($exam_id){
		$data = array();

		$questions = $this->ReadingComprehension_Model->generate_exam($exam_id);
		$data['questions'] = $this->ReadingComprehension_Model->format_questions($questions);
		$data['exam_id'] = $exam_id;

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('exam/create_reading_comprehension', $data);
		$this->load->view('footer', array(
										'private_js'=> array('exam/reading_comprehension/base.js')
										));
	}

	public function reading_comprehension_pdf($exam_id){
		$data = array();

		$questions = $this->ReadingComprehension_Model->generate_exam($exam_id);
		$questions = $this->ReadingComprehension_Model->format_questions($questions);

		$this->load->library('pdf/ReadingComprehensionPDF');
		$this->readingcomprehensionpdf->examHeader();
		$this->readingcomprehensionpdf->SetQuestion($questions);
		$this->readingcomprehensionpdf->DrawQuestionBody();
		$this->readingcomprehensionpdf->Output();
	}

	public function get_questions(){
		$num = $this->input->post('num');
		$questions = $this->ScrambledParagraph_Model->generate_random_exam((int)$num);
		$return = array(
					'success'=>true,
					'questions'=>$this->format_sp_exam_questions($questions)
					);
		$this->load->view('response/json', array('json'=>$return));

		return true;
	}

	private function format_sp_exam_questions($questions){
		$return = array();
		$count = 1;
		if(count($questions) == 0){
			return array();
		}

		foreach($questions as $q){
			$f_question = array();
			$solution = array();
			$solution_alpa = array();
			$ord = ($q['option_start'] == 'A') ? 64 : 80;
			$f_question['header'] = $count . '. ' . $q['lead'];

			$ans_count = 1;
			foreach($q['answer'] as $answer){
				$f_question['answer'][] = array(
											'choice'=>chr($ord + $ans_count),
											'line'=>$answer['text']
											);
				$solution[$answer['order_id']-1] = $ans_count;
				$solution_alpa[$answer['order_id']-1] = chr($ord + $ans_count);
				$ans_count +=1;
			}
			ksort($solution);
			ksort($solution_alpa);
			$f_question['solution'] = $solution;
			$f_question['solution_alpa'] = $solution_alpa;
			$return[] = $f_question;
			$count += 1;
		}
		return $return;
	}


}