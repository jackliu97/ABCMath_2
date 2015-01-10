<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use \ABCMath\Student\StudentManager,
	\ABCMath\Student\StudentImporter,
	\ABCMath\Student\StudentMapper;

class Setup extends CI_Controller {


	public function __construct(){
		parent::__construct();

		if($this->User_Model->check_permission('scaffolding') == false){
			$this->editable = false;
		}else{
			$this->editable = true;
		}
	}

	public function sample_student_csv(){
		$mapper = StudentMapper::getMapper();

		unset($mapper['id']);
		$mapper['note'] = array('display_name'=>'Notes', 'type'=>'textarea');
		$mapper['class1'] = array('display_name'=>'External Class Id 1', 'type'=>'input');
		$mapper['class2'] = array('display_name'=>'External Class Id 2', 'type'=>'input');
		$mapper['class3'] = array('display_name'=>'External Class Id 3', 'type'=>'input');
		$headers = array_keys($mapper);

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("ABCMath")
							 ->setLastModifiedBy("ABCMath")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Template for student import");

		$active_sheet = $objPHPExcel->getActiveSheet();

		$active_sheet->setTitle('Student Importer');
		$active_sheet->fromArray($headers);

		$objPHPExcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="student_importer.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$filePath = '/tmp/' . rand(0, getrandmax()) . rand(0, getrandmax()) . '.tmp';
		$objWriter->save($filePath);
		readfile($filePath);
		unlink($filePath);
	}

	public function sample_student_xls(){
		$mapper = StudentMapper::getMapper();
		$headers = array_keys($mapper);

		$this->output
			->set_header('Content-Disposition: attachment; filename=student.csv')
			->set_content_type('file/csv')
			->set_output(implode(',', $headers));
	}


	public function index(array $data=array()){

		$data = array();
		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('setup/setup_form', $data);
		$this->load->view('footer', array(
										'private_js'=> array('setup/student.js'),
										));
	}


	/**
	* logic to update student files.
	*/
	public function import_student_action(){

		$upload_path = './uploads/student_uploads/';
		if(!is_dir($upload_path)){
			mkdir($upload_path, 0777, true);
		}

		$this->load->library('upload', 
			array(
				'upload_path'=>$upload_path,
				'allowed_types'=>'xlsx'
				)
			);

		if (!$this->upload->do_upload('0')){
			$result['success'] = false;
			$result['message'] = $this->upload->display_errors();
			$this->load->view('response/json', array('json'=>$result));
			return false;
		}

		$upload_data = $this->upload->data();
		$importer = new StudentImporter();
		$importer->setFile($upload_data);
		$result = $importer->import();
		$this->load->view('response/json', array('json'=>$result));
		return true;

	}


}