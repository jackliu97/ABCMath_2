<?php
namespace ABCMath\ReadingComprehension;

use ABCMath\Base;
use ABCMath\Meta\Implement\ExamGenerator;

class ReadingComprehensionExam extends Base implements ExamGenerator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return the sql to get x number of random questions based on keywords.
     * @param mixed $keyword_id one, or an array of keywords
     * @param int   $limit
     */
    public function getRandomExamSQL($keyword_id, $limit = 10)
    {
        if (is_array($keyword_id)) {
            $keyword_sql = implode(',', $keyword_id);
        } else {
            $keyword_sql = $keyword_id;
        }

        return "SELECT rc.id FROM reading_comprehension rc ".
                "LEFT JOIN reading_comprehension_keyword rck ".
                    "ON rc.id = rck.reading_comprehension_id ".
                "WHERE rck.keyword_id IN ({$keyword_sql}) ".
                "ORDER BY RAND() ".
                "LIMIT {$limit}";
    }

    /**
     * Return x number of random questions based on keywords.
     * @param mixed $keyword_id one, or an array of keywords
     * @param int   $limit
     */
    public function getRandomExamQuestions($keyword_id, $limit = 10)
    {
        $stmt = $this->_conn->prepare(
            $this->getRandomExamSQL($keyword_id, $limit)
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
