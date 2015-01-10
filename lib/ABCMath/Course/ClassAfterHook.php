<?php
namespace ABCMath\Course;

use \ABCMath\Meta\Implement\HookInterface,
\ABCMath\Course\ABCClass,
\DateTime,
\DateInterval,
\ABCMath\Course\Lesson,
\ABCMath\Base,
\ABCMath\Course\LessonManager;

class ClassAfterHook extends Base implements HookInterface{
	public $classId;

	public function __construct(){
		$this->classId = null;
		parent::__construct();
	}

	public function setParameters($classId){
		$this->classId = $classId;
	}

	public function run(){

		//if lessons exists already, we don't bother making any more lessons.
		$lessonManager = new LessonManager();
		$lessonManager->getLessonsByClass($this->classId);
		if(count($lessonManager->getLessons())){
			return false;
		}

		//no lessons exists for this class, we make lessons.
		$class = new ABCClass();
		$class->setId($this->classId);
		$class->load();

		//if semester isn't set, we don't bother.
		$semester = $class->getSemester();
		if($semester === null){
			return false;
		}

		try{
			$currentDate = new DateTime($semester['start_date']);
			$dateEnd = new DateTime($semester['end_date']);
		}catch (Exception $e){
			//invalid start/end date.
			return false;
		}

		$lessonNumber = 0;

		//days this class is registered for.
		$registeredDays = $class->getRegisteredDaysOfWeek();

		while ($currentDate <= $dateEnd){
			if(in_array(strtolower($currentDate->format('D')), $registeredDays)){
				$lessonNumber += 1;
				$newLessonId = $this->_makeNewLesson($class, $currentDate, $lessonNumber);
			}

			//increment by a day.
			$currentDate->add(new DateInterval('P1D'));

		}
		return true;
	}

	protected function _makeNewLesson(ABCClass $class, DateTime $date, $lessonNumber){
		$fields = array(
			'lesson_number' => $lessonNumber,
			'description' => "Lesson #{$lessonNumber}, " . $date->format('l'),
			'class_id' => $class->id,
			'lesson_date' => $date->format(DateTime::ATOM),
			'start_time' => $class->start_time,
			'end_time' => $class->end_time,
			'notes' => 'Created via automation',
			'creation_datetime' => date('c')
			);
		$this->_conn->insert('lessons', $fields);
		return $this->_conn->lastInsertId();
	}
}