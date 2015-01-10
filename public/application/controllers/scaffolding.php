<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use \ABCMath\Entity\EntityManager,
	\ABCMath\Entity\Entity,
	\ABCMath\Entity\EntityField,
	\ABCMath\Entity\EntityFieldHTMLOutput,
	\ABCMath\Db\Datatable,
	\ABCMath\Scaffolding\ABCMathObject,
	\ABCMath\Scaffolding\ABCMathObjectList;

class Scaffolding extends CI_Controller {

	public function __construct(){
		parent::__construct();

		if($this->User_Model->check_permission('scaffolding') == false){
			header('Location: /landing');
		}

		$this->session->set_userdata(
			array(
				'section'=>'scaffolding'
				)
			);
	}

	public function index(){
		$this->load->view('header');
		$this->load->view('navbar');

		$this->load->view('scaffolding/list_all');
		$this->load->view('footer', array(
										'private_js'=> array('scaffolding/list_all.js'),
										'datatable'=> true
										));
	}

	public function list_all(){
		$result = array('success'=>false, 'message'=>'');
		$list = new EntityManager();
		$dt = new Datatable();
		$dt->sql = $list->allEntitySQL();
		$dt->columns = array( 'id', 'display_name', 'table_name', 'last_updated');
		$result = $dt->processQuery();

		if(count($result['aaData'])){
			foreach($result['aaData'] as $key=>$row){
				foreach($row as $k=>$col){
					if($k == 0){
						$field_id = $col;
						$result['aaData'][$key][$k] = 
							"<button entity_id='{$field_id}' class='btn add_mode glyphicon glyphicon-plus' style='width:45px;'>" . 
							"</button>";
						}else{
							$result['aaData'][$key][$k] = 
							"<a entity_id='{$field_id}' class='list_mode'>" . 
							"{$result['aaData'][$key][$k]}</a>";
							}
				}
			}
		}

		$this->load->view('response/datatable', array('json'=>$result));
	}

	public function object_list($id){

		$data = array(
			'entity_id'=>$id
			);

		$obj = new ABCMathObjectList();
		$obj->entity_id = $id;
		$obj->loadMeta();

		$data['table_name'] = $obj->entity->display_name;
		$data['entity_id'] = $obj->entity->id;
		$data['fields'] = $obj->entityFields;

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('scaffolding/object_list', $data);
		$this->load->view('footer', array(
										'private_js'=> array('scaffolding/object_list.js'),
										'datatable'=> true
										));

	}

	public function get_all_objects($id){
		$result = array('success'=>false, 'message'=>'');
		$obj = new ABCMathObjectList();
		$obj->entity_id = $id;
		$obj->loadMeta();

		$displayColumns = array();

		foreach($obj->entityFields as $field){
			if(!$field->showInList()){
				continue;
			}

			$displayColumns[] = $field->field_name;
		}

		$dt = new Datatable();
		$dt->sql = $obj->loadSql();
		$dt->columns = $displayColumns;
		$result = $dt->processQuery();

		if(count($result['aaData'])){
			foreach($result['aaData'] as $key=>$row){
				foreach($row as $k=>$col){
					if($k !== 0){
						continue;
					}
					$result['aaData'][$key][$k] = 
						"<button object_id='{$col}' class='btn edit_mode glyphicon glyphicon-pencil' style='width:45px;'>" . 
						"</button>&nbsp;" . 
						"<button object_id='{$col}' class='btn remove glyphicon glyphicon-remove' style='width:45px;'>" . 
						"</button>";
				}
			}
		}

		$this->load->view('response/datatable', array('json'=>$result));
	}

	public function object_detail($entity_id, $object_id=null){
		$data = array(
			'entity_id'=>$entity_id,
			'object_id'=>$object_id,
			'fields'=>array()
			);

		$object = new ABCMathObject();
		$object->entity_id = $entity_id;
		$object->loadEntity();
		$object->loadEntityFields();
		if($object_id !== null){
			$object->id = $object_id;
			$object->load();
		}
		$data['object_data'] = $object->data;
		$data['fields'] = $object->entityFields;

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('scaffolding/object_detail', $data);
		$this->load->view('footer', array(
										'private_js'=> array('scaffolding/object_detail.js')
										));


	}

	public function save_object_detail(){
		$object = new ABCMathObject();
		$object->id = $this->input->post('object_id');
		$object->entity_id = $this->input->post('entity_id');
		$object->loadEntity();
		$object->loadEntityFields();

		foreach($object->allFields() as $field){
			$object->data[$field] = $this->input->post($field);
		}
		
		$result = $object->save();

		if($result['success']){
			$result['field_id'] = $object->id;
		}
		$this->load->view('response/json', array('json'=>$result));
		return true;
	}

	public function delete_object(){
		$object = new ABCMathObject();
		$object->id = $this->input->post('object_id');
		$object->entity_id = $this->input->post('entity_id');
		$object->loadEntity();

		$this->load->view('response/json', 
			array('json'=>$object->delete())
			);
		return true;


	}


}