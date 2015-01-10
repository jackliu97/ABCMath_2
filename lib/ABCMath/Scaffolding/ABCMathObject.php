<?php
namespace ABCMath\Scaffolding;

use ABCMath\Meta\Implement\Element;
use ABCMath\Entity\Entity;
use ABCMath\Scaffolding\ABCMathObjectBase;
use ABCMath\Scaffolding\HookProcessor;

class ABCMathObject extends ABCMathObjectBase implements Element
{
    public $id;
    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->id = null;
    }

    public function allFields($update = false)
    {
        if (empty($this->entityFields)) {
            return false;
        }
        $return = array();
        foreach ($this->entityFields as $field) {
            //we don't touch id. ever.
            if ($field->field_name == 'id') {
                continue;
            }

            $return [] = $field->field_name;
        }

        return $return;
    }

    public function load()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from($this->entity->table_name, 't')
            ->where('t.id = ?')
            ->setParameter(0, $this->id);
        $this->data = $qb->execute()->fetch();
    }

    public function save()
    {
        $success = false;
        $hk = new HookProcessor($this->entity);
        $hk->runBeforeHook();

        if (!$this->id) {
            $this->id = $this->_insert();
            $success = true;
        } else {
            $success = $this->_update();
        }

        //runs after save hook.
        if ($success === true) {
            $hk->hookParameter = $this->id;
            $hk->runAfterHook();
        }

        return array('success' => $success);
    }

    protected function _insert()
    {
        $fields = array();
        foreach ($this->allFields() as $field) {
            $fields[$field] = $this->data[$field];
        }

        $this->_conn->insert($this->entity->table_name, $fields);

        return $this->_conn->lastInsertId();
    }

    protected function _update()
    {
        $fields = array();
        foreach ($this->allFields(true) as $field) {
            $fields[$field] = $this->data[$field];
        }

        $this->_conn->update($this->entity->table_name, $fields, array('id' => $this->id));

        return true;
    }

    public function delete()
    {
        if ($this->id == null) {
            return;
        }

        $this->_conn->delete($this->entity->table_name, array('id' => $this->id));

        return array('success' => true);
    }
}
