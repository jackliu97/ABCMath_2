<?php
namespace ABCMath\ReadingComprehension;

use ABCMath\Meta\Implement\Element;
use ABCMath\Base;

class Question extends Base implements Element
{
    public $reading_comprehension_id;
    public $id;
    public $text;
    public $original_text;
    public $choices;

    public function __construct()
    {
        $this->table = 'reading_comprehension_question';
        parent::__construct();

        $this->reading_comprehension_id = null;
        $this->choices = array();
    }

    /**
     * Loads question. Populates question data.
     * If data is passed, we do not access database.
     *
     * @param array $data Data about this question in the format of
     *
     * 		array(
     *			'id'=>'...' (int) NOTE: If this is not set or blank, save will perform an insert.
     *			'reading_comprehension_id'=>'...' (int)
     *			'text'=>'...',
     *			'original_text'=>'...',
     *			'choices'=>array(
     *						'is_answer'=>'...' (bool)
     *						'text'=>'...'
     *						)
     *			)
     *
     */
    public function load($data = array())
    {
        if (!count($data)) {
            $this->log('Data does not exist, loading from DB');
            $data = $this->_loadQuestionFromDb();
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        if (!isset($data['choices']) || !count($data['choices'])) {
            $this->choices = $this->_loadChoicesFromDb();
        }
    }

    /*
    * Saves this question. If id is give, we update, else we add new.
    */
    public function save()
    {
        if (!count($this->choices)) {
            return array(
                'success' => false,
                'message' => 'No choices', );
        }

        if (!$this->id) {
            $this->_insert();
        } else {
            $this->_update();
        }

        if (!$this->id) {
            $this->log('ID Failed to populate after insert/update.');

            return array(
                'success' => false,
                'message' => 'ID Failed to populate after insert/update.', );
        }

        $this->_saveChoices();

        return array('success' => true);
    }

    protected function _insert()
    {
        $this->_conn->insert('reading_comprehension_question',
                    array(
                        'reading_comprehension_id' => $this->reading_comprehension_id,
                        'text' => $this->text,
                        'original_text' => $this->original_text, ));
        $this->id = $this->_conn->lastInsertId();
    }

    protected function _update()
    {
        $this->_conn->update('reading_comprehension_question',
                    array(
                        'reading_comprehension_id' => $this->reading_comprehension_id,
                        'text' => $this->text,
                        'original_text' => $this->original_text, ),
                    array('id' => $this->id));
    }

    protected function _saveChoices()
    {
        if (!$this->id) {
            $this->log('ID does not exist.');
        }

        if (!count($this->choices)) {
            $this->log("[{$this->id}] No choices exists");

            return;
        }

        $this->_deleteAllChoices();
        foreach ($this->choices as $choice) {
            $this->_insertChoice($choice);
        }
    }

    public function loadAttr()
    {
        $attrs = array();
        $attrs['reading_comprehension_id'] = $this->reading_comprehension_id;
        $attrs['text'] = $this->text;
        $attrs['original_text'] = $this->original_text;

        if ($this->id) {
            $attrs['id'] = $this->id;
        }

        return $attrs;
    }

    protected function _insertChoice($choice)
    {
        $this->_conn->insert('reading_comprehension_question_choice',
                array(
                    'reading_comprehension_question_id' => $this->id,
                    'is_answer' => $choice['is_answer'],
                    'text' => $choice['text'], ));
    }

    protected function _deleteAllChoices()
    {
        $this->_conn->delete('reading_comprehension_question_choice',
            array('reading_comprehension_question_id' => $this->id));
    }

    protected function _loadQuestionFromDb()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('q.id', 'q.reading_comprehension_id', 'q.text', 'q.original_text')
            ->from($this->table, 'q')
            ->where('q.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetchAll();
    }

    protected function _loadChoicesFromDb()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('q.reading_comprehension_question_id', 'q.text', 'q.is_answer')
            ->from('reading_comprehension_question_choice', 'q')
            ->where('q.reading_comprehension_question_id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetchAll();
    }
}
