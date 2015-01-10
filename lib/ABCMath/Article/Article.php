<?php
namespace ABCMath\Article;

use \ABCMath\Meta\Implement\Element,
\ABCMath\Grouping\Keyword,
\ABCMath\Grouping\KeywordManager,
\ABCMath\Vocabulary\WordManager,
\ABCMath\Base;

class Article extends Base implements Element{

	public $id;
	public $title;
	public $article;

	public function __construct( $id=NULL ) {
		parent::__construct();

		$this->id = $id;
		$this->title = NULL;
		$this->article = NULL;
	}

	public function load( $data=array() ) {

		if ( !count( $data ) ) {

			//if no data is provided, we attempt to load from id.
			if ( $this->id ) {
				$data = $this->_loadFromDb();
			}else {
				$this->log( 'ID or word is needed to load existing data.' );
				return false;
			}
		}

		foreach ( $data as $k=>$v ) {
			$this->{$k} = $v;
		}

		return true;
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

	public function highlightWordsInPassage( $vocabularies ) {
		if ( !count( $vocabularies ) ) {
			return $passage;
		}

		$search = array();
		$replace = array();

		foreach ( $vocabularies as $v ) {
			$search []= ' ' . $v['word'] . ' ';
			$replace[]= ' <strong class="text-danger highlighted_word" title="' .
				( isset( $v['definition'] ) ? $v['definition'] : 'N/A' ) .
				'">' . $v['word'] . '</strong> ';
		}

		return str_ireplace( $search, $replace, $this->article );
	}

	public function getWordFromArticleByKeyword( $keywords ) {
		$vocabulary = array();
		$wordmanager = new WordManager();

		if ( !count( $keywords ) ) {
			return array();
		}

		//foreach keyword, get all word from passage.
		foreach ( $keywords as $keyword ) {
			$wordmanager->addKeyword( $keyword );
			$vocabulary[$keyword->id] = $this->getVocabularyFromPassage(
				$wordmanager->getWordsByKeyword()
			);
			$wordmanager->clearKeyword();
		}

		return $vocabulary;
	}

	/**
	 * Use an array of words, return those that exists in current article body.
	 *
	 * @param array   an array of array('word'=>'...', 'definition'=>'...').
	 * @param array
	 */
	public function getVocabularyFromPassage( $words ) {

		if ( !$this->article ) {
			return array();
		}

		//split words into array, lower case everything.
		$passage_words = array_unique( preg_split( "/[\s,?.;-]+/", $this->article ) );
		$passage_words = array_map( 'strtolower', $passage_words );

		foreach ( $words as $k=>$v ) {
			if ( in_array( strtolower( $v['word'] ), $passage_words ) === false ) {
				unset( $words[$k] );
			}
		}

		return array_values( $words );
	}

	protected function _loadFromDb() {
		$qb = $this->_conn->createQueryBuilder();
		$qb->select( 'a.id', 'a.title', 'a.article' )
		->from( 'article', 'a' )
		->where( 'a.id = ?' )
		->setParameter( 0, $this->id );
		return $qb->execute()->fetch();
	}

	protected function _insert() {
		$this->_conn->insert( 'article',
			array( 'title' => $this->title,
				'article'=>$this->article )
		);
		$this->id = $this->_conn->lastInsertId();

		return $this->id;
	}

	protected function _update() {

		$this->_conn->update( 'article',
			array( 'title' => $this->title,
				'article'=>$this->article ),
			array( 'id' => $this->id ) );
	}

	public function delete() {
		$this->_conn->delete( 'article', array( 'id' => $this->id ) );
	}
}
