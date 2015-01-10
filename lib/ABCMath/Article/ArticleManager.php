<?php
namespace ABCMath\Article;

use \ABCMath\Meta\Implement\ElementList,
\ABCMath\Base,
\ABCMath\Grouping\Keyword;

class ArticleManager extends Base implements ElementList{
	public $articles;

	public function __construct() {
		parent::__construct();
	}

	public function addArticle( Article $article ) {
		$this->articles[]= $article;
	}

	public function addKeyWord( Keyword $keyword ) {

		if ( !$keyword->id ) {
			$keyword->save();
		}

		$this->keywords[] = $keyword;
	}

	/*
	* Takes in an array of ids, load them all.
	*/
	public function loadList( $ids ) {}

	public function allSQL() {
		$qb = $this->_conn->createQueryBuilder();
		$qb->select( 'a.id', 'a.title', 'a.article' )
		->from( 'article', 'a' );
		return $qb->getSQL();
	}


	public function all() {
		$stmt = $this->_conn->prepare( $this->allSQL() );
		$stmt->execute();
		return $stmt->fetchAll();

	}
}

?>
