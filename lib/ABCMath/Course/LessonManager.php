<?php
namespace ABCMath\Course;

use \ABCMath\Base,
	\ABCMath\Course\Lesson;


/**
* This class manages a group of lessons.
* 
*/

class LessonManager extends Base {

	public $lessons;
	public $class_id;

	public function __construct(){
		parent::__construct();
		$this->lessons = array();
		$this->class_id = null;
	}

	public function addLesson(Lesson $lesson){
		$this->lessons []= $lesson;
	}

	public function getLessons(){
		return $this->lessons;
	}

	public function getLessonsByClass($class_id=null){
		if(!is_null($class_id)){
			$this->class_id = $class_id;
		}
		$result = array('success'=>false, 'lessons'=>array());

		$lessonsData = $this->_getLessonsDataByClass();
		if(!count($lessonsData)){
			return $result;
		}

		foreach($lessonsData as $lessonData){
			$lesson = new Lesson();
			$lesson->load($lessonData);
			$this->addLesson($lesson);
		}

		return array('success'=>false, 'lessons'=>$this->lessons);

	}

	protected function _getLessonsDataByClass(){
		$qb = $this->_conn->createQueryBuilder();
		$qb->select('*')
			->from('lessons', 'l')
			->where('l.class_id = ?')
			->setParameter(0, $this->class_id);
		return $qb->execute()->fetchAll();
	}



}