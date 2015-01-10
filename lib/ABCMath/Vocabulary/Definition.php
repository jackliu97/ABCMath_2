<?php
namespace ABCMath\Vocabulary;

use \ABCMath\Meta\Implement\Element,
	\ABCMath\Base;

class Definition extends Base implements Element{
	public $id;
	public $word_id;
	public $parts_of_speech;
	public $word;
	public $definition;
	public $definition_source_id;

	public function __construct($id=NULL){
		parent::__construct();
		$this->id = $id;
	}

	public function toArray(){
		return array(
			'word'=>$this->word,
			'parts_of_speech'=>$this->parts_of_speech,
			'definition'=>$this->definition,
			'definition_source_id'=>$this->definition_source_id
			);
	}

	public function setPartsOfSpeech($parts_of_speech){
		switch($parts_of_speech){
			case 'v.':
				$this->parts_of_speech = 'verb';
			break;

			case 'n.':
				$this->parts_of_speech = 'noun';
			break;

			case 'adj.':
				$this->parts_of_speech = 'adjective';
			break;

			case 'adv.':
			case 'ad.':
				$this->parts_of_speech = 'adverb';
			break;

			case 'conj.':
				$this->parts_of_speech = 'conjunctions';
			break;

			case 'inter.':
			case 'interj.':
				$this->parts_of_speech = 'interjections';
			break;

			case 'prep.':
			case 'pa.':
				$this->parts_of_speech = 'preposition';
			break;

			default:
				$this->parts_of_speech = $parts_of_speech;
			break;
		}

		return;
	}

	public function load($data=array()){

		if(!count($data)){
			$data = $this->_loadFromDb();
		}

		if(!$data){
			return false;
		}
		
		foreach($data as $k=>$v){
			$this->{$k} = $v;
		}

		return true;
	}

	private function _loadFromDb(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('vd.id',
					'vd.vocabulary_id as word_id',
					'vd.word',
					'vd.parts_of_speech',
					'vd.definition',
					'vd.definition_source_id')
			->from('vocabulary_definition', 'vd')
			->where('vd.id = ?')
			->setParameter(0, $this->id);
		return $qb->execute()->fetch();
	}

	public function save(){

		$this->_conn->insert('vocabulary_definition',
					array(	'vocabulary_id' => $this->word_id,
							'word' => $this->word,
							'definition'=>$this->definition,
							'parts_of_speech'=>$this->parts_of_speech,
							'definition_source_id'=>$this->definition_source_id)
					);
		$this->id = $this->_conn->lastInsertId();

		return $this->id;
	}

	public function update(){
		if($this->id == NULL){
			throw new \Exception('ID is required in order to update.');
		}

		$this->_conn->update('vocabulary_definition',
					array(	'word' => $this->word,
							'parts_of_speech' => $this->parts_of_speech,
							'definition' => $this->definition,
							'definition_source_id'=>$this->definition_source_id),
					array('id' => $this->id));
	}

}