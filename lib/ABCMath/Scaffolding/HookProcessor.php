<?php
namespace ABCMath\Scaffolding;

use \ABCMath\Entity\Entity;
use \ABCMath\Meta\Implement\HookInterface;

class HookProcessor{

	public $hookParameter;
	protected $_entity;

	public function __construct(Entity $entity){
		$this->_entity = $entity;
	}

	public function runBeforeHook(){
		if(class_exists( $this->_entity->before_hook )){
			$this->_run(new $this->_entity->before_hook);
		}
	}

	public function runAfterHook(){
		if(class_exists( $this->_entity->after_hook )){
			$this->run(new $this->_entity->after_hook);
		}
	}

	public function run(HookInterface $hook){
		$hook->setParameters($this->hookParameter);
		return $hook->run();
	}


}