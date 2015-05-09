<?php
namespace ABCMath\ReadingComprehension;

use ABCMath\Meta\Implement\Element;
use ABCMath\Grouping\Keyword;
use ABCMath\ReadingComprehension\QuestionManager;
use ABCMath\ReadingComprehension\Question;
use ABCMath\ElementBase;

class ReadingComprehension extends ElementBase implements Element
{
    public $id;
    public $full_text;
    public $lines;
    public $questions;

    public function __construct($id = null)
    {
        $this->table = 'reading_comprehension';
        parent::__construct();

        $this->id = $id;
        $this->full_text = null;
        $this->lines = array();
        $this->questions = array();
    }

    public static function parse($paragraph, array $questions){

        $result = array(
            'full_text' => $paragraph,
            'lines' => array(),
            'questions' => array()
            );
        $result['lines'] = array_map('trim', explode("\n", $paragraph));

        if(!count($questions)){
            return $result;
        }

        foreach($questions as $question){
            $result['questions'][] = self::parseQuestion($question);
        }

        return $result;
    }

    public static function parseQuestion($question){
        $question_text = $question['question'];

        $question_results = array(
            'original_text'=>$question_text,
            'text'=>'',
            'choices'=>array()
            );
        $question_pieces = preg_split('/\\n[a-p]\.|(\\nANS:)/i', $question_text);
        $question_results['text'] = trim(array_shift($question_pieces));

        $answer = trim(strtolower(array_pop($question_pieces)));
        $answer_index = ord($answer) - 97;

        foreach($question_pieces as $k=>$choices){
            $choice_pieces = array(
                'text'=>trim($choices),
                'is_answer'=>($k == $answer_index) ? '1' : '0'

                );
            $question_results['choices'][] = $choice_pieces;
        }

        return $question_results;
    }

    /**
     * loads data. Questions gets loaded separately.
     * if no params are passed, we load data from database. ID must be passed.
     *
     * @param array $data data to load in the form of attribute structure.
     *                    array('id'=>$id,
     *                    'full_text'=>$full_text
     *                    'lines' => array(
     *                    '0'=>'line1',
     *                    '1'=>'line2' ... ),
     *                    'questions'=> array(... questions ...)
     *                    )
     *
     */
    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_loadFromDB();
        }

        if (empty($data)) {
            $this->log('No data found.');

            return false;
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        if (!isset($data['lines'])) {
            $this->lines = $this->_loadLinesFromDB();
        }

        if (!isset($data['questions'])) {
            $data['questions'] = array();
        }

        $questions = new QuestionManager($this->id);
        $questions->load($data['questions']);
        $this->questions = $questions->questions;
    }

    /**
     * If id exists, we update, else we add new.
     */
    public function save()
    {
        $is_new = false;
        $this->_conn->beginTransaction();

        //start saving rc related stuff.
        if (!$this->id) {
            $this->id = $this->_insert();
            $is_new = true;
        } else {
            $this->_update();
        }

        //we must have ID to go beyond this point.
        if (!$this->id) {
            $this->log('No reading comprehension ID produced.');
            $this->_conn->rollback();

            return array(
                'success' => false,
                'message' => 'No reading comprehension ID produced.', );
        }

        //if we don't have any lines, no point in proceeding.
        if (!count($this->lines)) {
            $this->log('No lines inputted for this reading comprehension');
            $this->_conn->rollback();

            return array(
                'success' => false,
                'message' => 'At least one line is required.', );
        }

        //save lines.
        $this->_deleteAllLines();
        foreach ($this->lines as $line_number => $line) {
            try {
                $this->_saveLines($line, $line_number);
            } catch (Exception $e) {
                $this->log('Failed at saving line: '.$e->getMessage());
                $this->_conn->rollback();
            }
        }

        $result = $this->saveQuestions();

        if (!$result['success']) {
            $this->log($result['message']);
            $this->_conn->rollback();

            return $result;
        }

        $this->_conn->commit();

        return array('success' => true, 'is_new'=>$is_new);
    }

    public function delete()
    {
        if ($this->id == null) {
            return;
        }

        $this->_conn->delete('reading_comprehension', array('id' => $this->id));
    }

    public function saveQuestions()
    {

        $qm = new QuestionManager($this->id);
        if( $qm->deleteAll() === false ){
            return array('success'=> false);
        }
        
        if (!count($this->questions)) {
            return array(
                'success' => true,
                'message' => 'All existing questions removed.'
                );
        }

        foreach ($this->questions as $question) {
            $question->reading_comprehension_id = $this->id;
            $result = $question->save();
            if (!$result['success']) {
                $result['message'] = "Failed at saving question: [{$result['message']}]";
                $this->log($result['message']);
                return $result;
            }
        }

        return array('success' => true);
    }

    public function allSQL()
    {
        return "SELECT
				rc.id,
				SUBSTRING(rc.full_text, 1, 100) full_text,
				GROUP_CONCAT(k.word) keyword
			FROM reading_comprehension rc
			LEFT JOIN reading_comprehension_keyword rpk ON rc.id = rpk.reading_comprehension_id
			LEFT JOIN keyword k ON k.id = rpk.keyword_id
			GROUP BY rc.id, rc.full_text";
    }

    protected function _insert()
    {
        $this->_conn->insert('reading_comprehension',
                    array('full_text' => $this->full_text));

        return $this->_conn->lastInsertId();
    }

    protected function _update()
    {
        $this->_conn->update('reading_comprehension',
                    array('full_text' => $this->full_text),
                    array('id' => $this->id));
    }

    /**
     * Save line. We are inserting only here.
     * @param string $line
     * @param string $line_number
     *
     */
    protected function _saveLines($line, $line_number)
    {
        $this->_conn->insert('reading_comprehension_line',
                    array(
                        'reading_comprehension_id' => $this->id,
                        'line_number' => $line_number,
                        'line' => $line, ));
    }

    protected function _deleteAllLines()
    {
        $this->_conn->delete('reading_comprehension_line',
            array('reading_comprehension_id' => $this->id));
    }

    protected function _loadLinesFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('rl.line_number', 'rl.line')
            ->from('reading_comprehension_line', 'rl')
            ->where('rl.reading_comprehension_id = ?')
            ->setParameter(0, $this->id);
        $lines = $qb->execute()->fetchAll();
        if (!count($lines)) {
            return array();
        }
        $return = array();
        foreach ($lines as $line) {
            $return[$line['line_number']] = $line['line'];
        }

        return $return;
    }

    protected function _loadFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('r.id', 'r.full_text')
            ->from('reading_comprehension', 'r')
            ->where('r.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
