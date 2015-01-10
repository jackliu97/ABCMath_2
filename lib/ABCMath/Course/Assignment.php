<?php
namespace ABCMath\Course;

use \ABCMath\Base;


class Assignment extends Base {

	public $id;

	public function __construct(){
		parent::__construct();
	}

	public function setId($id){
		$this->id = $id;
	}

	public function load($data=array()){

		if(!count($data)){
			$data = $this->_getFromDB();
		}

		foreach($data as $k=>$v){
			$this->{$k} = $v;
		}
	}

	public function save() {
		if ( !$this->id ) {
			$this->id = $this->_insert();
		}else {
			$this->_update();
		}

		if ( !$this->id ) {
			$this->log( 'ID does not exist, save failed.' );
			return false;
		}

		return true;
	}

	public function delete() {
		$this->_conn->delete( 'assignments', array( 'id' => $this->id ) );
	}

	protected function _insert() {
		$this->_conn->insert( 'assignments',
			array(
				'name' => $this->name,
				'description'=>$this->description,
				'weight'=>$this->weight,
				'maximum_score'=>$this->maximum_score,
				'assignment_type_id'=>$this->assignment_type_id,
				'lesson_id'=>$this->lesson_id
				)
		);
		$this->id = $this->_conn->lastInsertId();

		return $this->id;
	}

	protected function _update() {

		$this->_conn->update( 'assignments',
			array(
				'name' => $this->name,
				'description'=>$this->description,
				'weight'=>$this->weight,
				'maximum_score'=>$this->maximum_score,
				'assignment_type_id'=>$this->assignment_type_id,
				'lesson_id'=>$this->lesson_id
				),
			array(
				'id' => $this->id
				));
	}

	protected function _getFromDB(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('*')
			->from('assignments', 'a')
			->where('a.id = ?')
			->setParameter(0, $this->id);
		return $qb->execute()->fetch();
	}

}