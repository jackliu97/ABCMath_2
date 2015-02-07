<?php
namespace ABCMath\Student;

use ABCMath\Base;
use ABCMath\Course\Lesson;

class Student extends Base
{
    public $id;
    protected $_rawData;

    public function __construct()
    {
        parent::__construct();
        $this->_dbData = array();
    }

    public static function get($idOrData){

        $student = new Student();
        if(is_array($idOrData)){
            $student->load($idOrData);
        }else{
            $student->setId($idOrData);
            $student->load();
        }

        return $student;

    }

    public function __get($key)
    {
        if(empty($key)){
            return;
        }

        return isset($this->_rawData[$key]) ? $this->_rawData[$key] : null;
    }

    public function __set($key, $value)
    {
        if(empty($key)){
            return;
        }

        $this->{$key} = $value;
        $this->_rawData[$key] = $value;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getRawData()
    {
        return $this->_rawData;
    }

    public function getAllClasses()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('sc.class_id')
            ->from('student_class', 'sc')
            ->innerJoin('sc', 'classes', 'c', 'sc.class_id = c.id')
            ->where('sc.student_id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }

    public function getNote($note_id)
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('n.notes')
            ->from('notes', 'n')
            ->where('n.id = ?')
            ->setParameter(0, $note_id);

        try {
            $result = $qb->execute()->fetch();

            return array('success' => true,
                        'notes' => $result['notes'],
                        );
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    public function deleteNote($note_id)
    {
        $this->_conn->delete('notes', array('id' => $note_id));

        return array('success' => true);
    }

    public function saveNote($notes, $user_id, $note_id = '', $lesson_id=NULL)
    {

        if(!empty($lesson_id)){
            $lesson = Lesson::get($lesson_id);
            $lesson->touchAttendance($this->id);
        }


        if (empty($note_id)) {
            return $this->_insertNote($notes, $user_id, $lesson_id);
        } else {
            return $this->_updateNote($note_id, $notes, $user_id);
        }
    }

    protected function _insertNote($notes, $user_id, $lesson_id=null)
    {

        $params = array(
            'student_id' => $this->id,
            'user_id' => $user_id,
            'notes' => $notes,
            'creation_datetime' => date('c'),
            );

        if(!empty($lesson_id)){
            $params['lesson_id'] = $lesson_id;
        }

        try {

            $this->_conn->insert('notes',$params);

            return array(
                'success' => true,
                'note_id' => $this->_conn->lastInsertId(),
                'message' => 'Note successfully inserted');
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    protected function _updateNote($note_id, $notes, $user_id)
    {
        try {
            $this->_conn->update('notes',
                    array(
                        'user_id' => $user_id,
                        'student_id' => $this->id,
                        'notes' => $notes,
                        ),
                array('id' => $note_id));

            return array(
                'success' => true,
                'note_id' => $note_id,
                'message' => 'Note successfully updated');
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    public function getAllClassesSQL()
    {
        $sql = "SELECT  c.id id,
						c.description,
						s.description subject,
						CONCAT(t.first_name, ' ', t.last_name) teacher,
						DATE_FORMAT(c.start_time, '%h:%i %p') `start_time`,
						DATE_FORMAT(c.end_time, '%h:%i %p') `end_time`,
						TRIM(BOTH ',' FROM CONCAT(
							if(c.mon='1', 'mon,', ''),
							if(c.tue='1', 'tue,', ''),
							if(c.wed='1', 'wed,', ''),
							if(c.thu='1', 'thu,', ''),
							if(c.fri='1', 'fri,', ''),
							if(c.sat='1', 'sat,', ''),
							if(c.sun='1', 'sun,', ''))) days
				FROM classes c
				INNER JOIN student_class sc ON c.id = sc.class_id
				LEFT JOIN subject s ON s.id = c.subject_id
				LEFT JOIN teachers t ON t.id = c.teacher_id
				WHERE sc.student_id = ".mysql_real_escape_string($this->id);

        if ($this->semester_id) {
            $sql .= ' AND c.term_id = '.mysql_real_escape_string($this->semester_id);
        }

        return $sql;
    }

    public function getAllNotesSQL()
    {
        $sql = "SELECT 	n.id note_id,
						DATE_FORMAT(n.creation_datetime, '%b %d, %Y %h:%i %p') `creation_datetime`,
						DATE_FORMAT(n.update_timestamp, '%b %d, %Y %h:%i %p') `update_timestamp`,
						IF(
								LENGTH(n.notes) > 10,
								CONCAT(SUBSTRING(n.notes, 1, 10), '...'),
								n.notes
							) notes,
						u.email
				FROM students s
					INNER JOIN notes n ON n.student_id = s.id
					LEFT JOIN users u ON u.id = n.user_id
				WHERE s.id = ".mysql_real_escape_string($this->id);

        return $sql;
    }

    public function load($data = array())
    {
        if (!count($data)) {
            $this->log('Data does not exist, loading from DB');
            $data = $this->_getFromDB();
        }

        if (!$data || count($data) === 0) {
            return false;
        }

        $this->_rawData = $data;

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        return true;
    }

    protected function _getFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from('students', 's')
            ->where('s.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
