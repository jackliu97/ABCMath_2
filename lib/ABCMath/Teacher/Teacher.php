<?php
namespace ABCMath\Teacher;

use ABCMath\Base;
use ABCMath\Course\ABCClassManager;

class Teacher extends Base
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

    /**
     * Loads all data that pertains to this teacher.
     */
    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_getFromDB();
        }

        if (!is_array($data)) {
            return false;
        }

        if (!count($data)) {
            return false;
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }

        return true;
    }

    /**
     * Gets all classes that this teacher teaches.
     */
    public function loadClasses()
    {
        $classes = new ABCClassManager();
        $classes->getClassesByTeacher($this->id);
        $this->classes = $classes->classes;
    }

    protected function _getFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('t.id',
                    't.first_name',
                    't.last_name',
                    't.telephone',
                    't.email',
                    't.subject_id',
                    't.creation_datetime',
                    't.update_timestamp')
            ->from('teachers', 't')
            ->where('t.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
