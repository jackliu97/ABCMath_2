<?php
namespace ABCMath\ReadingComprehension;

use ABCMath\Base;
use ABCMath\Meta\Implement\ExamGenerator;
use ABCMath\ReadingComprehension\ReadingComprehension;

class ReadingComprehensionExam extends Base implements ExamGenerator
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function getExamById(array $ids)
    {
        $questions = array();
        $examContainer = new ReadingComprehensionExam();

        if(empty($ids)){
            return array();
        }


        foreach($ids as $id){
            $rc = new ReadingComprehension($id);
            $rc->load();
            $questions[] = $examContainer->makeRTF($rc);
        }

        return $questions;
    }

    public function makeRTF(ReadingComprehension $rc)
    {

        $data = array();
        $data['id'] = $rc->id;
        $data['lines'] = $rc->lines;
        $data['questions'] = array();

        if(count($rc->questions)){
            foreach($rc->questions as $question){
                $q = array();
                $q['text'] = $question->text;
                $q['choices'] = array();
                shuffle($question->choices);

                $answer = '';
                foreach($question->choices as $k=>$choice){
                    $c = $choice;
                    $letter = chr($k + 97);
                    $q['choices'][$letter] = $c;
                    if($c['is_answer'] == 1){
                        $answer = $letter;
                    }

                }
                $q['answer'] = $answer;
                $data['questions'][] = $q;
            }
        }

        return $data;
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
