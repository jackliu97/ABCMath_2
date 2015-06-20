<?php
namespace ABCMath\Student;

use ABCMath\Base;

/**
 * This class manages a group of students.
 *
 */

class StudentManager extends Base
{
    public $students;
    public $class_id;
    public $semester_id;

    public function __construct()
    {
        parent::__construct();
    }

    public function addStudent(Student $student)
    {
        $this->students [] = $student;
    }

    public function exportStudent()
    {
        if (!count($this->students)) {
            return array();
        }

        $export = array();
        foreach ($this->students as $student) {
            $export[] = $student->getRawData();
        }

        return $export;
    }

    public function getRandomStudents($class_id, $num = 10)
    {
        $students = $this->_getRandomStudents($class_id, $num);

        foreach ($students as $s) {
            $s_obj = new Student();
            $s_obj->load($s);
            $this->addStudent($s_obj);
        }
    }

    public function getAllAbsentStudentsSQL(\DateTime $datetime)
    {
        $date = $datetime->format('Y-m-d');
        $class_join = $this->_buildClassJoin();

        return "SELECT
					s.id student_id,
					s.external_id external_id,
					CONCAT(s.first_name, ' ', s.last_name) name,
					IF(CHAR_LENGTH(s.email) > 0, s.email, s.email2) email,
					s.telephone telephone,
					s.cellphone cellphone,
					c.external_id class_name
				FROM students s
				{$class_join}
				WHERE sc.class_id IN (
				SELECT DISTINCT class_id FROM lessons WHERE lesson_date = '{$date}'
				)
				AND student_id IN (
				SELECT student_id FROM attendance a
				INNER JOIN lessons l ON a.lesson_id = l.id AND lesson_date = '{$date}'
				WHERE present = 2
				)";
    }

    public function getAllTardyStudentsSQL(\DateTime $datetime)
    {
        $date = $datetime->format('Y-m-d');
        $class_join = $this->_buildClassJoin();

        return "SELECT
					s.id student_id,
					s.external_id external_id,
					CONCAT(s.first_name, ' ', s.last_name) name,
					IF(CHAR_LENGTH(s.email) > 0, s.email, s.email2) email,
					s.telephone telephone,
					s.cellphone cellphone,
					c.external_id class_name
				FROM students s
				{$class_join}
				WHERE sc.class_id IN (
				SELECT DISTINCT class_id FROM lessons WHERE lesson_date = '{$date}'
				)
				AND student_id IN (
				SELECT student_id FROM attendance a
				INNER JOIN lessons l ON a.lesson_id = l.id AND lesson_date = '{$date}'
				WHERE present = 1 AND tardy IS NOT NULL
				)";
    }

    public function getAllRegisteredStudentsSQL()
    {
        $semester_id = intval($this->semester_id);
        $term_conditional = '';
        $join = 'LEFT';
        if($semester_id !== 0){
            $term_conditional = "AND c.term_id = {$semester_id}";
            $join = 'INNER';
        }

        return "SELECT
                    s.id student_id,
                    s.external_id external_id,
                    CONCAT(s.first_name, ' ', s.last_name) name,
                    IF(CHAR_LENGTH(s.email) > 0, s.email, s.email2) email,
                    s.telephone telephone,
                    s.cellphone cellphone,
                    group_concat(c.external_id SEPARATOR ' / ') class_name
                FROM students s
                {$join} JOIN student_class sc ON sc.student_id = s.id
                {$join} JOIN classes c ON sc.class_id = c.id {$term_conditional}
                GROUP BY sc.student_id";
    }

    public function getAllStudentsSQL()
    {
        return "SELECT
					s.id student_id,
					s.external_id external_id,
					CONCAT(s.first_name, ' ', s.last_name) name,
					IF(CHAR_LENGTH(s.email) > 0, s.email, s.email2) email,
					s.telephone telephone,
					s.cellphone cellphone,
					group_concat(c.external_id SEPARATOR ' / ') class_name,
                    group_concat(c.id) class_id
				FROM students s
				LEFT JOIN student_class sc ON sc.student_id = s.id
				LEFT JOIN classes c ON sc.class_id = c.id
				GROUP BY sc.student_id";
    }

    protected function _buildClassJoin(){
        $semester_id = intval($this->semester_id);
        $sql = 'LEFT JOIN student_class sc ON sc.student_id = s.id';

        if($semester_id > 0){
            return $sql . " INNER JOIN classes c ON sc.class_id = c.id AND c.term_id = {$semester_id}";
        }

        return $sql . ' LEFT JOIN classes c ON sc.class_id = c.id';

    }

    protected function _getRandomStudents($class_id, $num = 10)
    {
        $q = "SELECT s.*
				FROM students s
				INNER JOIN student_class sc
					ON sc.student_id = s.id
					AND sc.class_id = {$class_id}
				ORDER BY RAND()
				LIMIT {$num}";

        $stmt = $this->_conn->prepare($q);
        $stmt->execute();
        $students = $stmt->fetchAll();

        return $students;
    }

    public function loadStudentsByClass()
    {
        $students = $this->getAllStudentsByClass($this->class_id);

        if (!count($students)) {
            return array(
                'success' => false,
                'message' => "There are no students by this class id [{$this->class_id}]", );
        }

        foreach ($students as $data) {
            $student = new Student();
            $student->load($data);
            $this->addStudent($student);
        }

        return array(
                'success' => true,
                'message' => count($this->students)." students successfully loaded.", );
    }

    public function getAllStudentsByClassSQL($class_id)
    {
        $q = "SELECT
					s.id student_id,
					s.external_id external_id,
					CONCAT(s.first_name, ' ', s.last_name) name,
					s.email,
					s.telephone,
					s.cellphone,
                    group_concat(c.external_id SEPARATOR ' / ') class_name
				FROM students s
				LEFT JOIN student_class sc ON sc.student_id = s.id
                LEFT JOIN classes c ON sc.class_id = c.id
                WHERE s.id IN (
                    SELECT student_id FROM student_class where class_id = {$class_id}
                    )
                GROUP BY sc.student_id";

        return $q;
    }

    public function getAllStudentsByClass($class_id)
    {
        $q = "SELECT s.*
				FROM students s
				INNER JOIN student_class sc
					ON sc.student_id = s.id
				WHERE sc.class_id = ?
				ORDER BY s.last_name, s.first_name";

        $stmt = $this->_conn->prepare($q);
        $stmt->bindValue(1, $class_id);
        $stmt->execute();
        $students = $stmt->fetchAll();

        return $students;
    }

    public function getAllStudentsInfoForDashboard()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('s.id student_id', "CONCAT(s.first_name, ' ', s.last_name, ' (', s.external_id, ')') student_name")
            ->from('students', 's');

        return $qb->execute()->fetchAll();
    }
}
