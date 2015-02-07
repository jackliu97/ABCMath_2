<?php
namespace ABCMath\Course;

use ABCMath\Base;
use ABCMath\Course\AssignmentManager;
use ABCMath\Course\ABCClass;
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

    public static function get($idOrData){

        $lesson = new Lesson();
        if(is_array($idOrData)){
            $lesson->load($idOrData);
        }else{
            $lesson->setId($idOrData);
            $lesson->load();
        }

        return $lesson;

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

        $query = "SELECT 
                        a.id attendance_id,
                        a.lesson_id lesson_id,
                        a.student_id student_id,
                        a.present present,
                        a.tardy tardy,
                        group_concat(ad.type separator ',') attandance_types,
                        group_concat(ad.data separator ',') attandance_data
                    FROM attendance a
                    LEFT JOIN attendance_data ad ON a.id = ad.`attendance_id`
                    WHERE a.lesson_id = ?
                    GROUP BY a.id";

        $stmt = $this->_conn->prepare($query);
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        $data = $stmt->fetchAll();
        
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

    public function getAttendanceData($attendance_id, $data_type){
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('ad.id, ad.data')
            ->from('attendance_data', 'ad')
            ->where('ad.attendance_id = ? AND ad.type=?')
            ->setParameter(0, $attendance_id)
            ->setParameter(1, $data_type);

        return $qb->execute()->fetch();
    }

    /**
    * Checks to see if an attendance record exist, if yes we return attenance id.
    * if not, we create and return attendance id.
    *
    * @param Integer $student_id
    * @return array
    */
    public function touchAttendance($student_id){
        $attendance = $this->getAttendanceSingleStudent($student_id);
        if (!$attendance) {
            $attendance = $this->_insertAttendance($student_id, 1);
            if($attendance['success'] === false){
                return $attendance;
            }
        }

        return $attendance['id'];
    }

    /**
    * Attendance data becomes a toggle. It's either on or off.
    * Returned true if turned on, false if turned off.
    * 
    * @param Integer $attendance_id
    * @param String $data_type
    * @return boolean
    */
    public function toggleAttendanceData($attendance_id, $data_type){

        $attendance_data = $this->getAttendanceData($attendance_id, $data_type);
        if (!$attendance_data) {
            //if data doesn't exist, we turn it on on insert.
            $attendance_data = $this->_insertAttendanceData($attendance_id, $data_type, 1);
            return true;
        }else{
            if($attendance_data['data'] == 0){
                //if this is off, we turn it on.
                $this->_updateAttendanceData($attendance_id, $data_type, 1);
                return true;
            }else{
                //else we turn it off.
                $this->_updateAttendanceData($attendance_id, $data_type, 0);
                return false;
            }
        }

        return false;
    }

    public function takeAttendance($student_id)
    {
        $return = array(
            'success' => true,
            'message' => '',
            'present' => true,
            );

        $attendance_data = $this->getAttendanceSingleStudent($student_id);
        if (!$attendance_data) {
            $this->_insertAttendance($student_id, 1);
        }else{
            $this->_updateAttendance($attendance_data['id'], 1);
        }

        return $return;
    }

    public function markTardy($student_id)
    {
        $return = array(
            'success' => true,
            'message' => '',
            'present' => true,
            'tardy' => true,
            );

        $attendance_data = $this->getAttendanceSingleStudent($student_id);
        if (!$attendance_data) {
            $this->_insertAttendance($student_id, 1, true);
        }else{
            $this->_updateAttendance($attendance_data['id'], 1, true);
        }

        return $return;
    }

    public function markAbsent($student_id)
    {
        $return = array(
            'success' => true,
            'message' => '',
            'present' => false,
            'tardy' => false,
            'absent' => true
            );

        $attendance_data = $this->getAttendanceSingleStudent($student_id);
        if (!$attendance_data) {
            $this->_insertAttendance($student_id, 2);
        }else{
            $this->_updateAttendance($attendance_data['id'], 2);
        }

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

    protected function _insertAttendance($student_id, $present, $tardy = false)
    {
        try {
            $this->_conn->insert('attendance',
                array(
                    'lesson_id' => $this->id,
                    'student_id' => $student_id,
                    'present' => $present,
                    'tardy' => ($tardy ? date('c') : null),
                    )
                );
            $id = $this->_conn->lastInsertId();

            return array('success' => true, 'id'=>$id);
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    protected function _insertAttendanceData($attendance_id, $data_type, $data)
    {
        try {
            $this->_conn->insert('attendance_data',
                array(
                    'attendance_id' => $attendance_id,
                    'type' => $data_type,
                    'data' => $data
                    )
                );
            $id = $this->_conn->lastInsertId();

            return array('success' => true, 'id'=>$id);
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    protected function _updateAttendanceData($attendance_id, $data_type, $data)
    {

        try {
            $this->_conn->update('attendance_data',
                    array(
                        'data' => $data
                        ),
                array(
                    'attendance_id' => $attendance_id,
                    'type' => $data_type
                    ));

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
