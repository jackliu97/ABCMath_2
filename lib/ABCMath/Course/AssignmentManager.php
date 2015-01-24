<?php
namespace ABCMath\Course;

use ABCMath\Base;
use ABCMath\Course\Assignment;

/**
 * This class manages a group of classes.
 *
 */

class AssignmentManager extends Base
{
    public $assignments;

    public function __construct()
    {
        parent::__construct();
    }

    public function addAssignment(Assignment $assignment)
    {
        $this->assignments [] = $assignment;
    }

    public function getAssignmentByLesson($lesson_id)
    {
        $assignments = $this->_getAssignmentByLesson($lesson_id);

        if (!count($assignments)) {
            return;
        }

        foreach ($assignments as $assignment) {
            $assignment_obj = new Assignment();
            $assignment_obj->load($assignment);
            $this->addAssignment($assignment_obj);
        }

        return $this->assignments;
    }

    public function getAssignmentTypes()
    {
        $q = "SELECT id, description FROM assignment_types";
        $stmt = $this->_conn->prepare($q);
        $stmt->execute();
        $types = $stmt->fetchAll();

        $return = array();
        foreach ($types as $type) {
            $return[$type['id']] = $type['description'];
        }

        return $return;
    }

    public function gradeAssignments(array $data)
    {
        if (!count($data)) {
            return array('success' => true);
        }

        $this->_conn->beginTransaction();

        foreach ($data as $grade) {
            try {
                if ($grade['grade_id']) {
                    $this->_updateGrade($grade);
                } else {
                    $this->_insertGrade($grade);
                }
            } catch (Exception $e) {
                $this->_conn->rollback();

                return array(
                    'success' => false,
                    'message' => $e->getMessage(),
                    );
            }
        }

        $this->_conn->commit();

        return array(
            'success' => true,
            'data' => $data,
            'message' => 'Grades successfully saved.',
            );
    }

    protected function _updateGrade(array $grade)
    {
        if (!count($grade)) {
            return true;
        }

        $this->_conn->update(
            'grades',
                array('grade' => $grade['grade']),
                array('id' => $grade['grade_id']));
    }

    protected function _insertGrade(array $grade)
    {
        if (!count($grade)) {
            return true;
        }

        $this->_conn->insert(
            'grades',
                array(
                    'student_id' => intval($grade['student_id']),
                    'assignment_id' => intval($grade['assignment_id']),
                    'grade' => $grade['grade'],
                    )
                );
    }

    protected function _getAssignmentByLesson($lesson_id)
    {
        $q = "SELECT 	a.id id,
						a.name name,
						a.description description,
						a.assignment_type_id assignment_type_id,
						t.description type,
						a.maximum_score maximum_score,
						a.weight weight
				FROM assignments a
				INNER JOIN assignment_types t
					ON a.assignment_type_id = t.id
				WHERE a.lesson_id = ?";

        $stmt = $this->_conn->prepare($q);
        $stmt->bindValue(1, $lesson_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
