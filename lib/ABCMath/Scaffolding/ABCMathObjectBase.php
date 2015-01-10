<?php
namespace ABCMath\Scaffolding;

use ABCMath\Entity\Entity;
use ABCMath\Entity\EntityFieldHTMLOutput;
use ABCMath\Entity\EntityManager;
use ABCMath\Base;

class ABCMathObjectBase extends Base
{
    public $id;
    public $entity;
    public $entityFields;

    public function __construct()
    {
        parent::__construct();
        $this->entity_id = null;
        $this->entity = null;
        $this->entityFields = null;
    }

    public function loadEntity()
    {
        $this->entity = new Entity();
        $this->entity->id = $this->entity_id;

        return $this->entity->load();
    }

    public function loadEntityFields()
    {
        $em = new EntityManager();
        $fields = $em->getAllFieldsByEntity($this->entity_id);

        foreach ($fields as $field) {
            $id = $field['id'];
            $ef = new EntityFieldHTMLOutput();
            $ef->id = $field['id'];
            if (!$ef->load()) {
                return false;
            }

            $this->entityFields[] = $ef;
        }

        return true;
    }

    public function loadMeta()
    {
        if ($this->entity_id !== null) {
            return $this->loadEntity();
        }

        return false;
    }
}
