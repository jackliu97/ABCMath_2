<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use \ABCMath\Student\StudentManager,
	\ABCMath\Course\ABCClassManager,
	\ABCMath\Teacher\Teacher,
	\ABCMath\Exam\Exam;
class Teacher_Dashboard extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if($this->User_Model->check_login() == false){
			$this->session->sess_destroy();
			header('Location: /login');
		}
	}

	public function index(){


		//current teacher's information.
		$teacher = new Teacher();
		$exam = new Exam();
		$teacher->setId(1);
		$teacher->load();
		$teacher->loadClasses();

		$data = array();
		$data['teacher'] = $teacher;

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('dashboard/teacher', $data);
		$this->load->view('footer', array(
										'private_js'=> array('dashboard/teacher.js')
										));
	}

	public function generate_random_students(){
		$class_id = $this->input->post('class_id');
		$num_students = $this->input->post('num_students');

		$student_manager = new StudentManager();
		$student_manager->getRandomStudents($class_id, $num_students);

		$return = array(
					'success'=>true,
					'students'=>$student_manager->exportStudent()
					);
		$this->load->view('response/json', array('json'=>$return));
	}

	public function generate_random_question(){
		$question_type = $this->input->post('question_type');
		$student_id = $this->input->post('student_id');

		$exam = new Exam();
		$qObject = $exam->examFactory( 'vocabulary' );
        $questions = $qObject->getRandomExamQuestions(NULL, 1);
        $examQuestions = $qObject->buildExam($questions);

		$return = array(
					'success'=>true,
					'question'=>$examQuestions
					);

		$this->load->view('response/json', array('json'=>$return));
	}
}