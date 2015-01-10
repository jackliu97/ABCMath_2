<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use \ABCMath\Course\ABCClassManager,
	\ABCMath\Student\Student,
	\ABCMath\Student\StudentManager,
	\ABCMath\Db\Datatable;

class Admin_Dashboard extends CI_Controller {

	public function __construct(){
		parent::__construct();

		if($this->User_Model->check_login() == false){
			$this->session->sess_destroy();
			header('Location: /login');
		}

		if($this->User_Model->check_permission('admin') == false){
			$this->editable = false;
		}else{
			$this->editable = true;
		}

		$this->semester_id = $this->session->userdata('semester_id');
	}

	public function index(){
		$data = array();

		$user = Sentry::getUser();
		$data['user_email'] = $user->email;

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('dashboard/admin', $data);
		$this->load->view('footer', array(
										'private_js'=> array('dashboard/admin.js'),
										'datatable'=> true
										));
	}

	public function get_students($type=''){
		$method = "getAll{$type}StudentsSQL";
		$student_manager = new StudentManager();
		$dt = new Datatable();
		$dt->sql = $student_manager->{$method}(new DateTime('now'));
		$dt->columns = array(	'student_id',
								'external_id',
								'name',
								'email',
								'telephone',
								'cellphone',
								'class_name');
		$result = $dt->processQuery();

		
		if(count($result['aaData'])){
			foreach($result['aaData'] as $key=>$row){
				foreach($row as $k=>$col){
					if($k == 0){
						$student_id = $col;
					}
					$result['aaData'][$key][$k] = 
						"<a href='/student_dashboard/info/{$student_id}'>" . 
						"{$result['aaData'][$key][$k]}</a>";
				}
			}
		}

		$this->load->view('response/datatable', array('json'=>$result));

	}

	public function get_classes($status='all_classes'){
		$active_classes = false;
		if($status === 'active_classes'){
			$active_classes = true;
		}

		$result = array('success'=>false, 'message'=>'');
		$class_manager = new ABCClassManager();
		$class_manager->semester_id = $this->semester_id;
		$dt = new Datatable();
		$dt->sql = $class_manager->getAllClassesSQL($active_classes);
		$dt->columns = array(	'id',
								'external_id',
								'description',
								'subject',
								'teacher',
								'start_time',
								'end_time',
								'days');

		$result = $dt->processQuery();

		
		if(count($result['aaData'])){
			foreach($result['aaData'] as $key=>$row){
				foreach($row as $k=>$col){
					if($k == 0){
						$class_id = $col;
					}
					$result['aaData'][$key][$k] = 
						"<a href='/class_dashboard/info/{$class_id}'>" . 
						"{$result['aaData'][$key][$k]}</a>";
				}
			}
		}

		$this->load->view('response/datatable', array('json'=>$result));
	}

	public function check_student(){
		$student_id = $this->input->post('student_id');
		$student = new Student();
		$student->setId($student_id);
		$return = array('success'=>$student->load());

		$this->load->view('response/json', array('json'=>$return));

	}
}