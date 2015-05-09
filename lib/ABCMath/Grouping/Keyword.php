<?php
namespace ABCMath\Grouping;

use ABCMath\Meta\Implement\Element;
use ABCMath\Grouping\Keyword;
use ABCMath\Base;

class Keyword extends Base implements Element
{
    public $id;
    public $word;
    public $type;

    public function __construct($id = null)
    {
        parent::__construct();
        $this->id = $id;
        $this->word = null;
        $this->type = null;
    }

    public function load($data = array())
    {
        if (!is_array($data) || !count($data)) {
            $data = $this->_loadFromDb();
        }

        if (!count($data) || !$data) {
            $this->log('No data found.');

            return false;
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        return true;
    }

    public function save()
    {
        if ($this->id) {
            $this->_update();
        } else {
            $this->id = $this->_insert();
        }
    }

    public function bind($table, $external_id)
    {
        $this->_conn->insert(
                    "{$table}_keyword",
                    array("{$table}_id" => $external_id,
                          'keyword_id' => $this->id, )
                    );

        return $this->_conn->lastInsertId();
    }

    public function unbind($table, $external_id)
    {
        if (!$external_id) {
            throw new Exception('ID is required in order to delete.');
        }

        $this->_conn->delete("{$table}_keyword",
                                array(
                                    "{$table}_id" => $external_id,
                                    "keyword_id" => $this->id, )
                            );
    }

    protected function _insert()
    {
        $this->_conn->insert('keyword',
                    array(    'word' => $this->word,
                            'type' => $this->type, )
                    );

        return $this->_conn->lastInsertId();
    }

    protected function _update()
    {
        $this->_conn->update('keyword',
                    array(    'word' => $this->word,
                            'type' => $this->type, ),
                    array('id' => $this->id));
    }

    protected function _loadFromDb()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('k.id', 'k.word', 'k.type')
            ->from('keyword', 'k')
            ->where('k.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
