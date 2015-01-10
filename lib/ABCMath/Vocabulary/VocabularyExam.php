<?php
namespace ABCMath\Vocabulary;

use \ABCMath\Base,
	\ABCMath\Meta\Implement\ExamGenerator,
	\ABCMath\Vocabulary\Word,
	\ABCMath\Vocabulary\DefinitionManager,
	\ABCMath\Template\Template;

class VocabularyExam extends Base implements ExamGenerator {

	public $formatResult = true;
	public $template;

	public function __construct(){
		parent::__construct();
	}

	/*
	* With the given list of ids, build a test.
	*/
	public function buildExam($ids){

		$exam = array();

		if($this->formatResult){
			$this->template = new Template(Template::FILESYSTEM);
		}

		if(!count($ids)){
			return $exam;
		}


		foreach($ids as $id){

			$question = array();

			//get the current word information.
			$word = new Word();
			$word->id = $id;
			$word->load();

			$question['word'] = $word;

			//load 4 more definitions.
			$dm = new DefinitionManager();
			$question['choices'] = $dm->getRandomDefinitions(4);
			$question = $this->format($question);
			$exam[]= $this->formatResult ? $this->render($question) : $question;
		}

		return $exam;
	}

	public function render($formattedResult){

		//print_r($formattedResult);
		$htmlOutput = $this->template->render('Exam/vocabulary.twig', $formattedResult);

		return $htmlOutput;

	}

	public function format($rawResult){
		$result = array(
			'id'=>$rawResult['word']->id,
			'word'=>$rawResult['word']->word
			);

		$solution = $rawResult['word']->definitions[0];

		$definitions = array();
		$definitions[] = array(
			'id' => $solution->id,
			'vocabulary_id' => $solution->word_id,
			'parts_of_speech' => $solution->parts_of_speech,
			'definition' => $solution->definition
			);

		$result['solution'] = $solution->id;
		
		foreach($rawResult['choices'] as $choices){
			$definitions[]= $choices;
		}

		shuffle($definitions);

		$result['choices'] = $definitions;

		return $result;
	}

	/**
	* Return the sql to get x number of random questions based on keywords.
	* @param mixed $keyword_id one, or an array of keywords
	* @param int $limit
	*/
	public function getRandomExamSQL($keyword_id, $limit=10){
		$keyword_sql = '';
		if(is_array($keyword_id)){
			$keyword_sql = "WHERE vk.keyword_id IN (" . implode(',', $keyword_id) . ") ";
		}else if($keyword_id){
			$keyword_sql = "WHERE vk.keyword_id IN ({$keyword_id}) ";
		}

		return "SELECT v.id FROM vocabulary v " .
				"LEFT JOIN vocabulary_keyword vk " .
					"ON v.id = vk.vocabulary_id " .
				"INNER JOIN vocabulary_definition vd " .
					"ON v.id = vd.vocabulary_id " .
				$keyword_sql .
				"ORDER BY RAND() " .
				"LIMIT {$limit}";
	}

	/**
	* Return x number of random questions based on keywords.
	* @param mixed $keyword_id one, or an array of keywords
	* @param int $limit
	*/
	public function getRandomExamQuestions($keyword_id, $limit=10){
		$stmt = $this->_conn->prepare(
			$this->getRandomExamSQL($keyword_id, $limit)
			);
		$stmt->execute();

		$result = $stmt->fetchAll();

		if(!count($result)){
			return array();
		}

		$return = array();
		foreach($result as $r){
			$return[]= $r['id'];
		}
		return $return;
	}


}