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
                array('title' => "<span class='badge'>{$data['lesson_number']}</span>&nbsp;".
                    "{$data['assignment_type']} ({$data['assignment_name']})",
                    'assignment_id' => $data['assignment_id'],
                    'student_id' => $data['student_id'],
                );
        }

        return array_pop($headerData);
    }

    protected function _getAllGradesRaw()
    {
        $q = "SELECT
                s.id student_id,
                s.first_name,
                s.last_name,
                a.id assignment_id,
                a.name assignment_name,
                at.description assignment_type,
                l.id lesson_id,
                l.lesson_number lesson_number,
                g.id grade_id,
                g.student_id grade_student_id,
                g.grade,
                a.maximum_score
            FROM
                students s
                LEFT JOIN student_class sc ON sc.student_id = s.id
                LEFT JOIN classes c ON sc.class_id = c.id
                LEFT JOIN lessons l ON l.class_id = c.id
                LEFT JOIN assignments a ON a.lesson_id = l.id
                LEFT JOIN assignment_types at ON a.assignment_type_id = at.id
                LEFT JOIN grades g ON g.assignment_id = a.id AND g.student_id = s.id
            WHERE c.id = ?
            ORDER BY l.id, s.last_name, a.id, l.id";

        $stmt = $this->_conn->prepare($q);
        $stmt->bindValue(1, $this->id);
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
