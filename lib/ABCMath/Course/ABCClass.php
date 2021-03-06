<?php
namespace ABCMath\Course;

use ABCMath\Base;

/**
 * This class manages a group of classes.
 * NOTE : can't call this class, because class is a reserved word.
 */

class ABCClass extends Base
{
    public $id;
    public $classes;

    public function __construct()
    {
        parent::__construct();
    }

    public static function get($idOrData){

        $class = new ABCClass();
        if(is_array($idOrData)){
            $class->load($idOrData);
        }else{
            $class->setId($idOrData);
            $class->load();
        }

        return $class;

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

        if (!$data) {
            return;
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public function getAllGrades2(){
        $rawData = $this->_getAllGradesRaw();

        /*
        * Conform to handsOnTable format.
        *
        * [
        *      ["", "HW (86)", "Quiz A (16)", "Quiz B (15)", "HW (100)"],
        *      ["Bao, Kevin", 10, 11, 12, 13],
        *      ["Chen, Wesley", 20, 11, 14, 13],
        *      ["Chen, Alvin", 30, 15, 12, 13]
        * ]
        */
        $header = array();
        $students = array();
        $col_headers = array();
        $row_headers = array();

        $col_mapper = array(); //array of assignment id.
        $row_mapper = array(); //array of student id.

        $row_data = array();

        foreach($rawData as $k=>$box){
            $header[$box['assignment_id']] = array(
                'title' => "{$box['assignment_name']} ({$box['maximum_score']})",
                'assignment_id' => $box['assignment_id'],
                'lesson_id'=> $box['lesson_id'],
                'lesson_number'=> $box['lesson_number']
                );

            if(array_key_exists($box['student_id'], $students)){
                $students[$box['student_id']][] = $box['grade'];
            }else{
                $students[$box['student_id']] = array();
                $row_headers[] = "{$box['last_name']}, {$box['first_name']}";
                $students[$box['student_id']][] = $box['grade'];
            }
        }

        foreach($header as $assignment_id=>$head){
            $col_headers[] = $head['title'];
            $col_mapper[] = $head;
        }

        foreach($students as $student_id=>$row){
            $row_data[] = $row;
            $row_mapper[] = $student_id;
        }

        return array(
            'grade_col_header' => $col_headers,
            'grade_row_header' => $row_headers,
            'grade_row_data' => $row_data,
            'grade_col_id_mapper' => $col_mapper,
            'grade_row_id_mapper' => $row_mapper
            );
    }

    /**
     * Get all grades for this class in a grid format.
     */
    public function getAllGrades()
    {
        $rawData = $this->_getAllGradesRaw();

        $formattedData = array();
        $formattedData['header'] = $this->_buildGradeHeaders($rawData);
        $formattedData['data'] = $this->_buildGradeDataGrid($rawData);

        return $formattedData;
    }

    public function getAllGradesForReport( $student_id ){
        $grades = $this->_getAllGradesRaw( $student_id );
        $output = array();

        foreach($grades as $grade){
            $lesson_id = $grade['lesson_id'];
            $assignment_type = $grade['assignment_type'];

            if(!isset($output[$lesson_id])){
                $output[$lesson_id] = array();
            }
            $output[$lesson_id]['lesson_number'] = $grade['lesson_number'];
            $output[$lesson_id]['lesson_date'] = $grade['lesson_date'];
            $output[$lesson_id]['present'] = $grade['present'];
            $output[$lesson_id]['tardy'] = $grade['tardy'];

            if(!isset($output[$lesson_id][$assignment_type])){
                $output[$lesson_id][$assignment_type] = array();
            }

            $output[$lesson_id][$assignment_type][] = array(
                'name'=> $grade['assignment_name'],
                'grade'=> $grade['grade'],
                'maximum_score'=>$grade['maximum_score']
                );

        }
        return $output;
    }

    public function removeStudent($student_id)
    {
        try {
            $this->_conn->delete('student_class',
                array(
                'student_id' => $student_id,
                'class_id' => $this->id,
                ));
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }

        return array('success' => true);
    }

    public function addStudent($student_id)
    {
        try {
            $this->_conn->insert('student_class',
                array(
                    'student_id' => $student_id,
                    'class_id' => $this->id,
                    )
                );
        } catch (\Doctrine\DBAL\DBALException $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }

        return array('success' => true);
    }

    public function getSemester()
    {
        if (empty($this->term_id)) {
            return;
        }

        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from('semesters', 's')
            ->where('s.id = ?')
            ->setParameter(0, $this->term_id);

        return $qb->execute()->fetch();
    }

    public function getRegisteredDaysOfWeek()
    {
        $registeredDays = array();

        if (isset($this->mon) && $this->mon == 1) {
            $registeredDays[] = 'mon';
        }

        if (isset($this->tue) && $this->tue == 1) {
            $registeredDays[] = 'tue';
        }

        if (isset($this->wed) && $this->wed == 1) {
            $registeredDays[] = 'wed';
        }

        if (isset($this->thu) && $this->thu == 1) {
            $registeredDays[] = 'thu';
        }

        if (isset($this->fri) && $this->fri == 1) {
            $registeredDays[] = 'fri';
        }

        if (isset($this->sat) && $this->sat == 1) {
            $registeredDays[] = 'sat';
        }

        if (isset($this->sun) && $this->sun == 1) {
            $registeredDays[] = 'sun';
        }

        return $registeredDays;
    }

    protected function _buildGradeDataGrid(array $rawData)
    {
        $fData = array();
        $tmpData = array();

        if (!count($rawData)) {
            return $fData;
        }

        foreach ($rawData as $data) {
            $tmpData[$data['student_id']][0] = array(
                'student_name' => "{$data['last_name']}, {$data['first_name']}",
                'student_id' => $data['student_id'],
                );
            if ($data['assignment_id'] == '') {
                continue;
            }
            $tmpData[$data['student_id']][] = array('grade_id' => $data['grade_id'],
                                                    'grade' => $data['grade'],
                                                    'student_id' => $data['student_id'],
                                                    'assignment_id' => $data['assignment_id'],
                                                    'lesson_id' => $data['lesson_id'],
                                                    'lesson_number' => $data['lesson_number'],
                                                    'maximum_score' => $data['maximum_score'], );
        }

        foreach ($tmpData as $data) {
            $fData[] = $data;
        }

        return $fData;
    }

    protected function _buildGradeHeaders(array $rawData)
    {
        $headerData = array();
        foreach ($rawData as $data) {
            $headerData[$data['student_id']][0] = '';
            if (empty($data['assignment_id'])) {
                continue;
            }

            $headerData[$data['student_id']][] =
                array('title' => "<span class='label label-danger delete_assignment' assignment_id='{$data['assignment_id']}'>-</span>&nbsp;" . 
                    "<span class='badge'>{$data['lesson_number']}</span>&nbsp;".
                    $data['assignment_name'] . ' (' . $data['maximum_score'] . ')',
                    'assignment_id' => $data['assignment_id'],
                    'student_id' => $data['student_id'],
                );
        }

        return array_pop($headerData);
    }

    protected function _getAllGradesRaw($student_id=null)
    {
        $wheres = array('c.id = ?');
        $values = array(1=>$this->id);
        if($student_id !== null){
            $wheres []= 's.id = ?';
            $values[]= $student_id;
        }
        $wheres[]= 'a.id IS NOT NULL';

        $q = "SELECT
                s.id student_id,
                s.first_name,
                s.last_name,
                a.id assignment_id,
                a.name assignment_name,
                at.description assignment_type,
                l.id lesson_id,
                l.lesson_number lesson_number,
                l.lesson_date lesson_date,
                g.id grade_id,
                g.student_id grade_student_id,
                g.grade,
                a.maximum_score,
                atn.present,
                atn.tardy
            FROM
                students s
                LEFT JOIN student_class sc ON sc.student_id = s.id
                LEFT JOIN classes c ON sc.class_id = c.id
                LEFT JOIN lessons l ON l.class_id = c.id
                LEFT JOIN assignments a ON a.lesson_id = l.id
                LEFT JOIN assignment_types at ON a.assignment_type_id = at.id
                LEFT JOIN grades g ON g.assignment_id = a.id AND g.student_id = s.id
                LEFT JOIN attendance atn ON atn.lesson_id = l.id AND atn.student_id = s.id
            WHERE " . implode(' AND ', $wheres) . "
            ORDER BY l.id, s.last_name, a.id";

        $stmt = $this->_conn->prepare($q);

        foreach($values as $k=>$v){
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    protected function _getFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from('classes', 'c')
            ->where('c.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
