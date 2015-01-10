<?php
namespace ABCMath\Vocabulary;

use \ABCMath\Meta\Implement\ElementList,
	\ABCMath\Base,
	\ABCMath\Vocabulary\Word,
	\ABCMath\Grouping\Keyword,
	\ABCMath\Db\Datatable;

class WordManager extends Base implements ElementList{
	public $words;
	public $keywords;
	public $count;

	public $displayLength;
	public $displayStart;
	public $sSearch;

	public function __construct(){
		parent::__construct();
		$this->words = array();
		$this->count = 0;
		$this->displayLength = 10;
		$this->displayStart = 0;
		$this->sSearch = NULL;
	}

	public function addWord(Word $word){
		$this->count += 1;
		$this->words[] = $word;
	}

	public function count(){
		return $this->count;
	}

	public function addKeyWord(Keyword $keyword){

		if(!$keyword->id){
			$keyword->save();
		}

		$this->keywords[] = $keyword;
	}

	public function clearKeyword(){
		$this->keywords = array();
	}

	/*
	* Takes in an array of ids, load them all.
	*/
	public function loadList($ids){

		if(!count($ids)){
			return false;
		}

		foreach($ids as $id){
			$word = new Word();
			$word->id = $id;
			$word->load();
			$this->words[] = $word;
		}
	}

	public function getRandomWord($count=1){
		$wordIds = $this->_getRandomWordIds($count);

		foreach($wordIds as $id){
			$word = new Word($id);
			$word->load();

			$this->addWord($word);
		}
	}

	protected function _getRandomWordIds($count){
		$q = "SELECT 	v.id
				FROM vocabulary v
				ORDER BY RAND()
				LIMIT {$count}";
		$stmt = $this->_conn->prepare($q);
		$stmt->execute();
		return $stmt->fetch();
	}


	public function getWordsWithoutDefinition($maxCount=0){

		if($maxCount != 0){
			$limit = "LIMIT {$maxCount}";
		}else{
			$limit = '';
		}

		$sql = "SELECT 	v.id,
						v.word
				FROM vocabulary v
				LEFT JOIN vocabulary_definition vd 
						ON v.id = vd.vocabulary_id
				LEFT JOIN vocabulary_source_checked sc 
						ON v.id = sc.vocabulary_id
				WHERE vd.id IS NULL
				AND sc.id IS NULL
				ORDER BY v.id
				{$limit}";

		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	public function allSQL(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('v.id', 'v.word')
			->from('vocabulary', 'v')
			->orderBy('v.word');
		return $qb->getSQL();
	}


	public function all(){
		$stmt = $this->_conn->prepare($this->allSQL());
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getWordsByKeyword(){

		if(!count($this->keywords)){
			return array();
		}
		$keyword_ids = array();
		foreach($this->keywords as $kw){
			$keyword_ids[]=$kw->id;
		}

		$sql = "SELECT v1.id, v1.word
				FROM vocabulary v1 WHERE v1.id IN (
				SELECT 	distinct(v.id)
				FROM 	vocabulary v
				INNER JOIN vocabulary_keyword vk
					ON vk.vocabulary_id = v.id
				WHERE vk.keyword_id IN (" . 
					implode(',', $keyword_ids) . 
					"))";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*
	* Inserts word one by one.
	*/
	public function insertList(){
		$count = 0;
		if(!count($this->words)){
			return;
		}

		$kw_relation = array();

		$this->_conn->beginTransaction();
		foreach($this->words as $word){
			try{
				$result = $word->save();
				if($result['success'] === false){
					continue;
				}

				foreach($this->keywords as $kw){
					$kw_relation[]= "({$word->id}, {$kw->id})";
				}
				$count += 1;
			}catch(Exception $e){
				$this->_conn->rollback();
				throw $e;
			}
		}

		if(!count($kw_relation)){
			$this->_conn->rollback();
			return $count;
		}

		$insertq = "INSERT INTO vocabulary_keyword (vocabulary_id, keyword_id) VALUES " 
					. implode(',', $kw_relation);
		try{
			$this->_conn->executeUpdate($insertq);
		}catch(Exception $e){
			$this->_conn->rollback();
			throw $e;
		}
		$this->_conn->commit();
		return $count;

	}


}

?>