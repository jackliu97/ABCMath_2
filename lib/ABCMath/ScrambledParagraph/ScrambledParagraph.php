<?php
namespace ABCMath\ScrambledParagraph;

use ABCMath\Meta\Implement\Element;
use ABCMath\Grouping\Keyword;
use ABCMath\ElementBase;

class ScrambledParagraph extends ElementBase implements Element
{
    public $id;
    public $full_text;
    public $lines;

    public function __construct($id = null)
    {
        parent::__construct();
        $this->table = 'scrambled_paragraph';
        $this->id = $id;
        $this->full_text = null;
        $this->lines = array();
    }

    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_loadFromDb();
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        if (!isset($data['lines'])) {
            $this->lines = $this->_loadLinesFromDB();
        }
    }

    public function save()
    {
        $this->_conn->beginTransaction();

        if (!$this->id) {
            $this->id = $this->_insert();
        } else {
            $this->_update();
        }

        //we must have ID to go beyond this point.
        if (!$this->id) {
            $this->_conn->rollback();

            $return = array(
                'success' => false,
                'message' => 'No scrambled paragraph ID produced.', );
            $this->log($return['message']);

            return $return;
        }

        //if we don't have any lines, no point in proceeding.
        if (!count($this->lines)) {
            $this->_conn->rollback();

            $return = array(
                'success' => false,
                'message' => 'No lines inputted for this scrambled paragraph.', );
            $this->log($return['message']);

            return $return;
        }

        //save lines.
        $this->_deleteAllLines();
        foreach ($this->lines as $line) {
            $this->_saveLine($line);
        }

        $this->_conn->commit();

        return array('success' => true);
    }

    protected function _insert()
    {
        $this->_conn->insert('scrambled_paragraph',
            array('full_text' => $this->full_text));

        return $this->_conn->lastInsertId();
    }

    protected function _update()
    {
        $this->_conn->update('scrambled_paragraph',
            array('full_text' => $this->full_text),
            array('id' => $this->id));
    }

    protected function _loadFromDb()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('s.id', 's.full_text')
            ->from('scrambled_paragraph', 's')
            ->where('s.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }

    protected function _loadLinesFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('s.order_id', 's.text')
            ->from('scrambled_paragraph_line', 's')
            ->where('s.scrambled_paragraph_id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetchAll();
    }

    public function delete()
    {
        if ($this->id == null) {
            return;
        }

        $this->_conn->delete('scrambled_paragraph', array('id' => $this->id));
    }

    protected function _saveLine($line)
    {
        $this->_conn->insert('scrambled_paragraph_line',
            array(
                'scrambled_paragraph_id' => $this->id,
                'order_id' => $line['order_id'],
                'text' => $line['text'],
                ));
    }

    protected function _deleteAllLines()
    {
        if ($this->id == null) {
            return;
        }

        $this->_conn->delete('scrambled_paragraph_line',
            array('scrambled_paragraph_id' => $this->id));
    }

    protected function _saveKeywords()
    {
        foreach ($this->keywords as $keyword) {
            try {
                $keyword->bind('scrambled_paragraph', $this->id);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
}
