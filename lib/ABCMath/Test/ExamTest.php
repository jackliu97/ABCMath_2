<?php
require_once "test_bootstrap.php";

use \ABCMath\Exam\Exam;

class ExamTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    //php phpunit.phar --filter testLoadType_success ExamTest.php
    //test returning a random reading comprehension.
    public function testLoadType_success()
    {
        $exam = $this->getMock('\ABCMath\Exam\Exam',
            array( '_getFromDB' ));

        $object = $exam->examFactory('reading_comprehension');
        $this->assertEquals(
            gettype($object), 'object'
            );

        $this->assertEquals(
            get_class($object), 'ABCMath\ReadingComprehension\ReadingComprehensionExam'
            );
    }

    //php phpunit.phar --filter testLoadType__unknown_class ExamTest.php
    public function testLoadType__unknown_class()
    {
        $exam = $this->getMock('\ABCMath\Exam\Exam',
            array( '_getFromDB' ));

        try {
            $exam->examFactory('reading_comprehension_bad');
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(),
                'Class of type [reading_comprehension_bad] does not exist in list');
        }
    }

    //php phpunit.phar --filter testLoadType__bad_class ExamTest.php
    public function testLoadType__bad_class()
    {
        $exam = $this->getMock('\ABCMath\Exam\Exam',
            array( '_getFromDB' ));

        $exam->addType('bad_class', 'ABCMath\path\to\bad\class');

        try {
            $exam->examFactory('bad_class');
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(),
                'Class [ABCMath\path\to\bad\class] does not exist.');
        }
    }

    //php phpunit.phar --filter testGetQuestion ExamTest.php
    public function testGetQuestion__catch_exception()
    {
        $exam = $this->getMock('\ABCMath\Exam\Exam',
            array( '_getFromDB' ));

        try {
            $exam->getRamdomQuestion('vocabulary_bad', 11);
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(),
                'Class of type [vocabulary_bad] does not exist in list');
        }
    }

    //php phpunit.phar --filter testGetQuestion_randomQuestion ExamTest.php
    public function testGetQuestion_randomQuestion()
    {
        $exam = $this->getMock('\ABCMath\Exam\Exam',
            array( '_getFromDB' ));

        $quest = $exam->examFactory('vocabulary');
        $questions = $quest->getRandomExamQuestions(null, 2);

        $examQuestions = $quest->buildExam($questions);
        print_r($questions);
        //print_r($examQuestions);
    }
}
