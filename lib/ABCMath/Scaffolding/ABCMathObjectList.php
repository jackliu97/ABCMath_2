<?php
namespace ABCMath\Scaffolding;

use \ABCMath\Meta\Implement\ElementList,
	\ABCMath\Entity\Entity,
	\ABCMath\Entity\EntityField,
	\ABCMath\Entity\EntityManager,
	\ABCMath\Base,
	\ABCMath\Scaffolding\ABCMathObjectBase;

class ABCMathObjectList extends ABCMathObjectBase implements ElementList{

	public $id;
	public $data;

	public function __construct(){
		parent::__construct();

		$this->id = NULL;
	}

	public function loadSql(){

		if(!count($this->entityFields)){
			return false;
		}

		$display_fields = array();
		$joins = array();

		foreach($this->entityFields as $field){

			if(!$field->showInList()){
				continue;
			}

			if($field->field_type_id == EntityField::SELECT ) {
				$this->buildSQLSelect($field, $display_fields, $joins);

			}else if ($field->field_type_id == EntityField::TIME){
				$this->buildSQLTime($field, $display_fields);

			}else if ($field->field_type_id == EntityField::DATE){
				$this->buildSQLDate($field, $display_fields);

			}else if ($field->field_type_id == EntityField::DATETIME){
				$this->buildSQLDateTime($field, $display_fields);

			}else{
				$display_fields []= 't.' . $field->field_name;
			}

		}

		$sql = 	"SELECT\n" . implode(",\n", $display_fields) . "\n" .
				"FROM {$this->entity->table_name} t \n" . 
				implode("\n", $joins);

		return $sql;
	}

	public function buildSQLDateTime($field, &$display_fields){
		$display_fields []= "DATE_FORMAT({$field->field_name}, '%b / %d / %Y %h:%i %p') AS `$field->field_name`";
	}

	public function buildSQLDate($field, &$display_fields){
		$display_fields []= "DATE_FORMAT({$field->field_name}, '%b / %d / %Y') AS `$field->field_name`";
	}

	public function buildSQLTime($field, &$display_fields){
		$display_fields []= "DATE_FORMAT({$field->field_name}, '%h:%i %p') AS `$field->field_name`";
	}

	public function buildSQLSelect($field, &$display_fields, &$joins){
		if(empty($field->join_table) || 
			empty($field->join_column) || 
			empty($field->join_display)){
			$display_fields []= 't.' . $field->field_name;
			return;
		}

		$joins[] = "LEFT JOIN {$field->join_table} {$field->join_table} " . 
					"ON t.{$field->field_name} = {$field->join_table}.{$field->join_column}";

		$display_fields []= "{$field->join_table}.{$field->join_display} AS `{$field->field_name}`";

	}

	public function loadMeta(){
		if(parent::loadMeta()){

			return $this->loadEntityFields();
		}

		return false;
	}

}