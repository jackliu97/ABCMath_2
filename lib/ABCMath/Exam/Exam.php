<?php
namespace ABCMath\Exam;

use ABCMath\Base;
use ABCMath\ReadingComprehension\ReadingComprehension;

class Exam extends Base
{
    public $id;
    public $questions;

    protected $_typeInfo;

    public function __construct()
    {
        parent::__construct();

        $this->_typeInfo = array(
            //'reading_comprehension'=>'\ABCMath\ReadingComprehension\ReadingComprehensionExam',
            //'scrambled_paragraph'=>'\ABCMath\ScrambledParagraph\ParagraphExam',
            'vocabulary' => '\ABCMath\Vocabulary\VocabularyExam',
            );
    }

    public function addType($typeKey, $typeName)
    {
        $this->_typeInfo[$typeKey] = $typeName;
    }

    public function getTypes()
    {
        return $this->_typeInfo;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /*
    * enter a type, return an instance of that type.
    */
    public function examFactory($type)
    {
        $this->log("Loading an exam object of type [{$type}]");

        if (array_key_exists($type, $this->_typeInfo) === true) {
            if (!class_exists($this->_typeInfo[$type])) {
                throw new \Exception("Class [{$this->_typeInfo[$type]}] does not exist.");
            }

            return new $this->_typeInfo[$type]();
        } else {
            throw new \Exception("Class of type [{$type}] does not exist in list");
        }
    }

    /**
     * Loads all data that pertains to this teacher.
     */
    public function load($data = array())
    {
        if (!count($data)) {
            $data = $this->_getFromDB();
        }

        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
    }

    /**
     * Get a random question of type, filtered by keyword id.
     *
     * @param string $type       class type
     * @param array  $keyword_id array of keyword id.
     * @param int    $num_result
     *
     * @return array an array of ids.
     */
    public function getRamdomQuestion($type, $keyword_id, $num_result = 1)
    {
        try {
            $obj = $this->examFactory($type);
        } catch (Exception $e) {
            $this->log($e->getMessage());

            return false;
        }

        return $obj->getRandomExamQuestions($keyword_id, $num_result);
    }

    protected function _getFromDB()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('*')
            ->from(BANK_DB.'.exam', 'e')
            ->where('e.id = ?')
            ->setParameter(0, $this->id);

        return $qb->execute()->fetch();
    }
}
