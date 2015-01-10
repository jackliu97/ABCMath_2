<?php
namespace ABCMath\Entity;

use ABCMath\Meta\Implement\Element;
use ABCMath\Base;

class EntityField extends Base implements Element
{
    const INPUT = 1;
    const HIDDEN = 2;
    const SELECT = 3;
    const CHECKBOX = 4;
    const DATE = 5;
    const TIME = 6;
    const DATETIME = 7;
    const TEXTAREA = 8;

    public $id;
    public $table_name;
    public $display_name;

    public function __construct()
    {
        $this->table = 'entity_fields';
        parent::__construct();
        $this->id = null;
    }

    public function allFields($update = false)
    {
        if ($update) {
            return array(
                    'display_name',
                    'field_type_id',
                    'is_primary',
                    'display_in_list',
                    'extra_tags',
                    'field_size',
                    'field_order',
                    'join_table',
                    'join_column',
                    'join_display',
                    );
        }

        return array(
                    'entity_table_id',
                    'field_name',
                    'display_name',
                    'field_type_id',
                    'is_primary',
                    'display_in_list',
                    'extra_tags',
                    'field_size',
                    'field_order',
                    'join_table',
                    'join_column',
                    'join_display',
                    );
    }

    public function showInList()
    {
        if ($this->is_primary) {
            return true;
        }

        if ($this->display_in_list) {
            return true;
        }

        return false;
    }

    public function load($data = array())
    {
        if (!count($data)) {
            $this->log('Data does not exist, loading from DB');
            $data = $this->_loadEntityFromDb();
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        return true;
    }

    public function save()
    {
        if (!$this->id) {
            $this->id = $this->_insert();
        } else {
            $this->_update();
        }

        return array('success' => true);
    }

    protected function _insert()
    {
        $fields = array();
        foreach ($this->allFields() as $field) {
            $fields[$field] = $this->{$field};
        }
        $this->_conn->insert($this->table, $fields);

        return $this->_conn->lastInsertId();
    }

    protected function _update()
    {
        $fields = array();
        foreach ($this->allFields(true) as $field) {
            $fields[$field] = $this->{$field};
        }

        return $this->_conn->update($this->table, $fields, array('id' => $this->id));
    }

    public function delete()
    {
        if ($this->id == null) {
            return;
        }

        $result = $this->_conn->delete($this->table, array('id' => $this->id));

        return array('success' => $result);
    }

    protected function _loadEntityFromDbSQL()
    {
        $selects = array();
        foreach ($this->allFields() as $field) {
            $selects[] = "ef.{$field}";
        }

        $selectClause = implode(',', $selects);

        $sql = "SELECT ef.id,
				{$selectClause}
				FROM {$this->table} ef
					LEFT JOIN entity e ON ef.entity_table_id = e.id
				WHERE ef.id = {$this->id}";

        return $sql;
    }

    protected function _loadEntityFromDb()
    {
        $stmt = $this->_conn->prepare(
            $this->_loadEntityFromDbSQL()
            );
        $stmt->execute();

        return $stmt->fetch();
    }
}
