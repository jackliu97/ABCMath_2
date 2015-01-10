<?php
namespace ABCMath\ReadingComprehension;

use ABCMath\ReadingComprehension\Question;
use ABCMath\Base;

class QuestionManager extends Base
{
    public $reading_comprehension_id;
    public $questions;

    public function __construct($reading_comprehension_id = null)
    {
        parent::__construct();

        $this->reading_comprehension_id = $reading_comprehension_id;
        $this->questions = array();
    }

    public function save($input)
    {
    }

    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_loadFromDb();
        }

        foreach ($data as $question) {
            $q = new Question();
            $q->load($question);
            $this->questions[] = $q;
        }

        return true;
    }

    public function deleteAll()
    {
        if (!$this->reading_comprehension_id) {
            throw new Exception('ID is required in order to delete.');
        }

        $this->_conn->delete("reading_comprehension_question",
                array("reading_comprehension_id" => $this->reading_comprehension_id));
    }

    private function _loadFromDb()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('q.id', 'q.reading_comprehension_id', 'q.text', 'q.original_text')
            ->from('reading_comprehension_question', 'q')
            ->where('q.reading_comprehension_id = ?')
            ->setParameter(0, $this->reading_comprehension_id);

        return $qb->execute()->fetchAll();
    }
}
