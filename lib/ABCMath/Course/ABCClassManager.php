<?php
namespace ABCMath\Course;

use ABCMath\Base;
use ABCMath\Course\ABCClass;

/**
 * This class manages a group of classes.
 *
 */

class ABCClassManager extends Base
{
    public $classes;
    public $semester_id;

    public function __construct()
    {
        parent::__construct();
        $this->semester_id = null;
    }

    public function addClass(ABCClass $class)
    {
        $this->classes [] = $class;
    }

    public function getClassesByTeacher($teacher_id)
    {
        $classes = $this->_getClassByTeacher($teacher_id);

        if (!count($classes)) {
            return;
        }

        foreach ($classes as $class) {
            $class_obj = new ABCClass();
            $class_obj->load($class);
            $this->addClass($class_obj);
        }
    }

    protected function _getClassByTeacher($teacher_id)
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from(ADMIN_DB.'.classes', 'c')
            ->where('c.teacher_id = ?')
            ->setParameter(0, $teacher_id);

        return $qb->execute()->fetchAll();
    }

    public function getAllClasses($type = null)
    {
        $stmt = $this->_conn->prepare($this->getAllClassesSQL());
        $stmt->execute();
        $classes = $stmt->fetchAll();

        if ($type === null) {
            return $classes;
        }

        if ($type === 'options') {
            $options = array('' => 'Pick a class');
            foreach ($classes as $class) {
                $options[$class['id']] = "{$class['external_id']} {$class['school']}";
            }

            return $options;
        }
    }

    public function getClassForRegistration($type=null)
    {

        $stmt = $this->_conn->prepare($this->getClassForRegistrationSQL());
        $stmt->execute();
        $classes = $stmt->fetchAll();

        if ($type === null) {
            return $classes;
        }

        if($type === 'options'){
            $options = array(''=>'Pick a class');
            foreach ($classes as $class) {
                $options[$class['id']] = $class['name'];
            }
            return $options;
        }

    }

    public function getClassForRegistrationSQL(){
        $sql = "SELECT 
                    c.id id,
                    CONCAT(s.description, ' ', c.description) name,
                    s.start_date
                FROM classes c
                INNER JOIN semesters s ON c.term_id = s.id
                WHERE s.end_date >= NOW()
                ORDER BY s.start_date DESC, c.description ASC";

        return $sql;
    }

    public function getAllClassesSQL($active_only = false)
    {
        $where = array();

        if ($active_only) {
            $where [] = 'c.start_time < NOW()';
            $where [] = 'c.end_time > NOW()';
            $where [] = 'c.'.strtolower(date('D')).'=1';
        }

        if ($this->semester_id) {
            $where [] = "c.term_id = {$this->semester_id}";
        }

        $select = "SELECT
							c.id,
							c.external_id,
							c.description,
							s.description subject,
							sch.description school,
							CONCAT(t.first_name, ' ', t.last_name) teacher,
							DATE_FORMAT(c.start_time, '%h:%i %p') `start_time`,
							DATE_FORMAT(c.end_time, '%h:%i %p') `end_time`,
							TRIM(BOTH ',' FROM CONCAT(
							IF(c.mon='1', 'mon,', ''),
							IF(c.tue='1', 'tue,', ''),
							IF(c.wed='1', 'wed,', ''),
							IF(c.thu='1', 'thu,', ''),
							IF(c.fri='1', 'fri,', ''),
							IF(c.sat='1', 'sat,', ''),
							IF(c.sun='1', 'sun,', ''))) days
					FROM classes c
						LEFT JOIN `subject` s ON c.subject_id = s.id
						LEFT JOIN `teachers` t ON c.teacher_id = t.id
						LEFT JOIN schools sch ON sch.id = c.school_id";

        if (count($where)) {
            $select .= ' WHERE '.implode(' AND ', $where);
        }

        $select .= ' ORDER BY c.external_id';

        return $select;
    }
}
