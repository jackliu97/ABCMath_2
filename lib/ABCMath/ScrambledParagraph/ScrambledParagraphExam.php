<?php
namespace ABCMath\ScrambledParagraph;

use ABCMath\Base;
use ABCMath\Meta\Implement\ExamGenerator;
use ABCMath\ScrambledParagraph\ScrambledParagraph;

class ScrambledParagraphExam extends Base implements ExamGenerator
{
    public function __construct()
    {
        parent::__construct();
    }

    public function buildExam(ScrambledParagraph $question)
    {

        $questionFormat = array();
        $lines = $question->lines;

        if(!count($lines)){
            return $questionFormat;
        }

        $questionFormat['lead'] = array_shift($lines);
        shuffle($lines);
        $questionFormat['lines'] = $lines;



        return $questionFormat;
    }

    /**
     * Return the sql to get x number of random questions based on keywords.
     * @param mixed $keyword_id one, or an array of keywords
     * @param int   $limit
     */
    public function getRandomExamSQL($keyword_id=null, $limit = 10)
    {

        if($keyword_id === null){
            $where = '';
        }else{
            if (is_array($keyword_id)) {
                $keyword_sql = implode(',', $keyword_id);
            } else {
                $keyword_sql = $keyword_id;
            }
            $where = "WHERE spk.keyword_id IN ({$keyword_sql}) ";
        }
        

        return "SELECT sp.id FROM scrambled_paragraph sp ".
                "LEFT JOIN scrambled_paragraph_keyword spk ".
                    "ON sp.id = spk.scrambled_paragraph_id {$where}".
                "ORDER BY RAND() ".
                "LIMIT {$limit}";
    }

    /**
     * Return x number of random questions based on keywords.
     * @param mixed $keyword_id one, or an array of keywords
     * @param int   $limit
     */
    public function getRandomExamQuestions($keyword_id=null, $limit = 10)
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
