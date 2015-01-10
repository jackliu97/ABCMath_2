<?php
namespace ABCMath\Vocabulary;

use \ABCMath\Meta\Implement\Element,
	\ABCMath\ElementBase,
	\ABCMath\Grouping\Keyword,
	\ABCMath\Grouping\KeywordManager,
	\ABCMath\Vocabulary\Definition;

class Word extends ElementBase implements Element{

	public $id;
	public $word;
	public $definitions;

	/**
	* @var boolean turns off transaction.
	*/
	public $transaction;

	public function __construct($id=NULL){
		parent::__construct();

		$this->id = $id;
		$this->word = NULL;
		$this->definitions = array();
		$this->transaction = false;
	}

	public function addDefinition(Definition $definition){
		$this->definitions[]= $definition;
	}

	public function clearDefinition(){
		$this->definition = array();
	}

	/**
	* A word can be loaded by
	*	populating $this->id
	*   populating $this->word
	*   passing an Array
	*
	* @param Array $data array with key structure similar to table.
	* @return boolean false if failed, true if no error.
	*/
	public function load($data=array()){

		if(!count($data)){

			if($this->id){
				$data = $this->_loadFromDbWithId();
			}else if($this->word){
				$data = $this->_loadFromDbWithWord();
			}else{
				$this->log('ID or word is needed to load existing data.');
				return false;
			}
		}

		foreach($data as $k=>$v){
			$this->{$k} = $v;
		}

		if(!isset($data['definitions']) || !count($data['definitions'])){
			$data['definitions'] = $this->_loadDefinitions();
		}
		if(!count($data['definitions'])){
			return true;
		}
		$this->definitions = array();
		foreach($data['definitions'] as $definition){
			$defObj = new Definition();
			$defObj->load($definition);
			$this->definitions[]= $defObj;
		}

		return true;
	}

	public function checkWordExist(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('v.id')
			->from('vocabulary', 'v')
			->where('v.word = ?')
			->setParameter(0, $this->word);
		return count($qb->execute()->fetchAll()) != 0;
	}

	public function save(){

		if(!$this->word){
			return array(
				'success'=>false,
				'message'=>'Word is not populated correctly.'
				);
		}

		$this->_beginTransaction();

		if(!$this->id){
			$this->id = $this->_insert();
		}else{
			$this->_update();
		}

		if(!$this->id){
			$this->_rollback();
			return array('success'=>false,
				'message'=>'ID Failed to populate on save.');
		}

		if($this->_saveDefinitions()){
			$this->_commit();
			return array('success'=>true);
		}else{
			$this->_rollback();
			return array('success'=>false, 'message'=>'Failed to save word definitions.');
		}
	}

	public function delete(){
		if($this->id == NULL){
			throw new \Exception('ID is required in order to delete.');
		}

		$this->_conn->delete('vocabulary', array('id' => $this->id));
		$this->_conn->delete('vocabulary_keyword', array('vocabulary_id' => $this->id));
		$this->_conn->delete('vocabulary_definition', array('vocabulary_id' => $this->id));

	}

	public function markedAsChecked($source_id){
		$this->_conn->insert('vocabulary_source_checked',
					array(
						'vocabulary_id' => $this->id,
						'source_id' => $source_id));
		return true;
	}

	protected function _loadDefinitions(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('vd.id',
					'vd.vocabulary_id as word_id',
					'vd.word',
					'vd.parts_of_speech',
					'vd.definition')
			->from('vocabulary_definition', 'vd')
			->where('vd.vocabulary_id = ?')
			->setParameter(0, $this->id);
		$definitions = $qb->execute()->fetchAll();

		if(!is_array($definitions) || !count($definitions)){
			return array();
		}

		return $definitions;
	}

	protected function _loadFromDbWithId(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('v.id', 'v.word')
			->from('vocabulary', 'v')
			->where('v.id = ?')
			->setParameter(0, $this->id);
		return $qb->execute()->fetch();
	}

	protected function _loadFromDbWithWord(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('v.id', 'v.word')
			->from('vocabulary', 'v')
			->where('v.word = ?')
			->setParameter(0, $this->word);
		return $qb->execute()->fetch();
	}

	protected function _beginTransaction(){
		if($this->transaction === false){
			return;
		}

		$this->_conn->beginTransaction();
	}

	protected function _commit(){
		if($this->transaction === false){
			return;
		}

		$this->_conn->commit();
	}

	protected function _rollback(){
		if($this->transaction === false){
			return;
		}

		$this->_conn->rollback();
	}

	protected function _insert(){
		$this->_conn->insert('vocabulary',
					array(
						'word' => $this->word)
					);
		return $this->_conn->lastInsertId();

	}

	protected function _update(){
		$this->_conn->update('vocabulary',
					array('word' => $this->word),
					array('id' => $this->id)
					);
	}

	protected function _saveDefinitions(){

		if(!count($this->definitions)){
			return true;
		}

		foreach($this->definitions as $definition){
			$definition->word_id = $this->id;
			$definition->save();
		}

		return true;

	}
}

?>