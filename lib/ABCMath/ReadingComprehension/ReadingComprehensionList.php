<?php
namespace ABCMath\ReadingComprehension;

use ABCMath\Base;

class ReadingComprehensionList extends Base
{
    const LIST_SIZE = 100;
    public $list_size;
    public $current_page;

    public function __construct(){
        parent::__construct();
        $this->list_size = self::LIST_SIZE;
        $this->current_page = 0;
    }

    public function nextPage(){
        $this->current_page += 1;
        return $this;
    }

    public function buildQuery(){
        $offset = $this->current_page * $this->list_size;
        $q = "SELECT id 
                FROM reading_comprehension 
                ORDER BY add_datetime 
                LIMIT {$this->list_size} OFFSET {$offset}";
        return $q;
    }

    public function fetchAll(){

        $stmt = $this->_conn->prepare(
            $this->buildQuery()
            );
        $stmt->execute();

        $result = $stmt->fetchAll();

        if (!count($result)) {
            return array();
        }

        $return = array();
        foreach ($result as $r) {
            $return[] = $r['id'];
        }

        return $return;

    }

}