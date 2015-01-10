<?
namespace ABCMath;
use \ABCMath\Base;
class ElementBase extends Base{

	public $keywords;

	public function __construct(){
		parent::__construct();
		$this->keywords = array();
	}


	/**
	* Adds a keyword to this element, but does not save it. 
	* If keyword is new (no id set) we will perform an insert.
	*
	* @param Keyword $keyword
	*
	*/
	public function addKeyWord(\AbcMath\Grouping\Keyword $keyword){
		if(!$keyword->id){
			$keyword->save();
		}

		$this->keywords[] = $keyword;
	}

	/**
	* Saves keywords to this element on a database level.
	* 
	*/
	public function saveKeywords(){

		if(!$this->id){
			$this->log('ID is required to save keyword.');
			return;
		}

		if(!$this->table){
			$this->log('Table name is required to save keyword.');
			return;
		}

		$this->_conn->beginTransaction();

		$this->deleteAllKeywords();

		if(count($this->keywords)){
			foreach($this->keywords as $keyword){
				$keyword->bind($this->table, $this->id);
			}
		}

		$this->_conn->commit();
	}

	/**
	* Removes all keywords from this element.
	*
	*/
	public function deleteAllKeywords(){
		$this->_conn->delete("{$this->table}_keyword",
			array("{$this->table}_id" => $this->id)
		);
	}

}
