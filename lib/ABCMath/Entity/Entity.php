<?php
namespace ABCMath\Entity;

use ABCMath\Meta\Implement\Element;
use ABCMath\Base;

class Entity extends Base implements Element
{
    public $id;
    public $table_name;
    public $display_name;
    public $before_hook;
    public $after_hook;

    public function __construct()
    {
        $this->table = 'entity';
        parent::__construct();
        $this->id = null;
    }

    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_loadEntityFromDb();
        }

        if (!$data) {
            $this->log("Error: This table ID does not exist. [{$this->id}]");

            return false;
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        return true;
    }

    public function validateTableExists($table_schema, $table_name)
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('t.table_schema', 't.table_name')
            ->from('information_schema.tables', 't')
            ->where('t.table_schema = ?', 't.table_name = ?')
            ->setParameter(0, $table_schema)
            ->setParameter(1, $table_name);

        return $qb->execute()->fetch();
    }

    public function save()
    {
        if ($this->validateTableExists($this->_db_name, $this->table_name) == false) {
            return array(
                'success' => false,
                'message' => "Table by the name [{$this->table_name}] does not exist", );
        }

        if (!$this->id) {
            $this->id = $this->_insert();
        } else {
            $this->_update();
        }

        return array('success' => true);
    }

    protected function _insert()
    {
        $this->_conn->insert($this->table,
                array(
                    'table_name' => $this->table_name,
                    'display_name' => $this->display_name,
                    'before_hook' => $this->before_hook,
                    'after_hook' => $this->after_hook,
                    )
            );

        return $this->_conn->lastInsertId();
    }

    protected function _update()
    {
        $this->_conn->update($this->table,
                array(
                    'table_name' => $this->table_name,
                    'display_name' => $this->display_name,
                    'before_hook' => $this->before_hook,
                    'after_hook' => $this->after_hook,
                    ),
            array('id' => $this->id));
    }

    public function delete()
    {
        if ($this->id == null) {
            return;
        }

        $this->_conn->delete($this->table, array('id' => $this->id));

        return array('success' => true);
    }

    protected function _loadEntityFromDb()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('e.id',
                'e.table_name',
                'e.display_name',
                'e.last_updated',
                'e.before_hook',
                'e.after_hook')
            ->from($this->table, 'e')
            ->where('e.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
