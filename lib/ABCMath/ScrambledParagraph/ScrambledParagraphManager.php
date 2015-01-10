<?php
namespace ABCMath\ScrambledParagraph;

use \ABCMath\Meta\Implement\ElementList,
	\ABCMath\Base,
	\ABCMath\Grouping\Keyword;

class ScrambledParagraphManager extends Base implements ElementList{
	public $paragraphs;
	public $keywords;

	public function __construct(){
		parent::__construct();
	}

	public function addParagraph(ScrambledParagraph $paragraph){
		$this->paragraphs[]= $paragraph;
	}

	public function addKeyWord(Keyword $keyword){

		if(!$keyword->id){
			$keyword->save();
		}
		$this->keywords[] = $keyword;
	}

	/*
	* Takes in an array of ids, load them all.
	*/
	public function loadList($ids){}

	/*
	* all
	*/
	public function all(){
		$q = $this->allSQL();
		$stmt = $this->_conn->prepare($q);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function allSQL(){
		return "SELECT
				sp.id,
				SUBSTRING(sp.full_text, 1, 100) full_text,
				GROUP_CONCAT(k.word) keyword
			FROM scrambled_paragraph sp
			LEFT JOIN scrambled_paragraph_keyword spk ON sp.id = spk.scrambled_paragraph_id
			LEFT JOIN keyword k ON k.id = spk.keyword_id
			GROUP BY sp.id, sp.full_text";
	}
}

?>