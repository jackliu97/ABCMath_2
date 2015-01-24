<?php
namespace ABCMath\Course;

use ABCMath\Base;
use ABCMath\Course\AssignmentManager;
use ABCMath\Attachment\Attachment;

class Lesson extends Base
{
    public $id;
    public $assignments;

    public static $cache;

    public function __construct()
    {
        parent::__construct();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_getFromDB();
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public function loadAssignments()
    {
        $a_manager = new AssignmentManager();
        $this->assignments = $a_manager->getAssignmentByLesson($this->id);
    }

    /**
     * get an attendance data about this lesson.
     */
    public function getAttendance()
    {

        if(isset(self::$cache['attendance_' . $this->id])){
            return self::$cache['attendance_' . $this->id];
        }

        $qb = $this->_conn->createQueryBuilder();
        $qb->select('student_id, present, tardy')
            ->from('attendance', 'a')
            ->where('a.lesson_id = ?')
            ->setParameter(0, $this->id);
        $data = $qb->execute()->fetchAll();
        if (!$data) {
            return array();
        }

        $return = array();
        foreach ($data as $row) {
            $return[$row['student_id']] = $row;
        }

        self::$cache['attendance_' . $this->id] = $return;
        return $return;
    }

    /**
     * get attendence data for one student
     */
    public function getAttendanceSingleStudent($student_id)
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('a.id, a.present', 'a.tardy')
            ->from('attendance', 'a')
            ->where('a.lesson_id = ? AND a.student_id=?')
            ->setParameter(0, $this->id)
            ->setParameter(1, $student_id);

        return $qb->execute()->fetch();
    }

    public function takeAttendance($student_id)
    {
        $return = array(
            'success' => true,
            'message' => '',
            'present' => false,
            );

        $attendance_data = $this->getAttendanceSingleStudent($student_id);

        if (!$attendance_data) {
            //if this never existed, mark as present.
            $this->_insertAttendance($student_id);
            $return['present'] = true;

            return $return;
        }

        if ($attendance_data['present'] != 1) {
            $return['present'] = true;
        }

        $this->_updateAttendance($attendance_data['id'], $return['present']);

        return $return;
    }

    public function markTardy($student_id)
    {
        $return = array(
            'success' => true,
            'message' => '',
            'present' => true,
            'tardy' => false,
            );

        $attendance_data = $this->getAttendanceSingleStudent($student_id);
        if (!$attendance_data) {
            //if this never existed, mark as present and late.
            $this->_insertAttendance($student_id, true);
            $return['present'] = true;
            $return['tardy'] = true;

            return $return;
        }

        if (!$attendance_data['tardy']) {
            $return['tardy'] = true;
        }
        $this->_updateAttendance($attendance_data['id'], 1, $return['tardy']);

        return $return;
    }

    public function addAttachment(Attachment $attachment)
    {
        $attachment_id = $attachment->id;
        if (!isset($attachment->id) && !$attachment->id) {
            return 'Invalid attachment id.';
        }

        $stmt = $this->_conn->prepare(
                'INSERT IGNORE INTO lessons_attachments (lesson_id, attachment_id) VALUES (?, ?)'
                );

        $stmt->bindValue(1, $this->id);
        $stmt->bindValue(2, $attachment->id);

        try {
            $stmt->execute();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    protected function _getAllAttachmentsSQL()
    {
        return "SELECT
					 a.*
				FROM lessons_attachments la
				INNER JOIN attachments a ON la.attachment_id = a.id
				WHERE la.lesson_id = ?";
    }

    public function getAllAttachments()
    {
        $return = array( 'success' => true, 'message' => '' );

        $stmt = $this->_conn->prepare($this->_getAllAttachmentsSQL());
        $stmt->bindValue(1, $this->id);

        try {
            $stmt->execute();
            $attachments = $stmt->fetchAll();
        } catch (\Exception $e) {
            $return = array( 'success' => false, 'message' => $e->getMessage() );

            return $return;
        }

        $return['attachments'] = $attachments;

        return $return;
    }

    protected function _updateAttendance($id, $present, $tardy = false)
    {
        try {
            $this->_conn->update('attendance',
                    array(
                        'present' => $present,
                        'tardy' => ($tardy ? date('c') : null),
                        ),
                array('id' => $id));

            return array('success' => true);
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    protected function _insertAttendance($student_id, $tardy = false)
    {
        try {
            $this->_conn->insert('attendance',
                array(
                    'lesson_id' => $this->id,
                    'student_id' => $student_id,
                    'present' => 1,
                    'tardy' => ($tardy ? date('c') : null),
                    )
                );

            return array('success' => true);
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    protected function _getFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from('lessons', 'l')
            ->where('l.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
