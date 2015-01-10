<?php
require_once "test_bootstrap.php";

class ScrambledParagraphExamTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    //php phpunit.phar --filter testGetRandomExamSQL__ten ScrambledParagraphExamTest.php
    public function testGetRandomExamSQL__ten()
    {
        $sp = $this->getMock('\ABCMath\ScrambledParagraph\ScrambledParagraphExam',
          array( '_insert',
            '_update',
            '_loadFromDB',
            '_deleteAllLines',
            '_saveLines',
            'saveQuestions', ));

        $sql = $sp->getRandomExamSQL(10, 10);
        $this->assertEquals($sql, "SELECT sp.id FROM scrambled_paragraph sp ".
            "LEFT JOIN scrambled_paragraph_keyword spk ".
              "ON sp.id = spk.scrambled_paragraph_id ".
            "WHERE spk.keyword_id IN (10) ".
            "ORDER BY RAND() ".
            "LIMIT 10");

        $sql = $sp->getRandomExamSQL(array(10), 1);
        $this->assertEquals($sql, "SELECT sp.id FROM scrambled_paragraph sp ".
            "LEFT JOIN scrambled_paragraph_keyword spk ".
              "ON sp.id = spk.scrambled_paragraph_id ".
            "WHERE spk.keyword_id IN (10) ".
            "ORDER BY RAND() ".
            "LIMIT 1");

        $sql = $sp->getRandomExamSQL(array(10, 11), 4);
        $this->assertEquals($sql, "SELECT sp.id FROM scrambled_paragraph sp ".
            "LEFT JOIN scrambled_paragraph_keyword spk ".
              "ON sp.id = spk.scrambled_paragraph_id ".
            "WHERE spk.keyword_id IN (10,11) ".
            "ORDER BY RAND() ".
            "LIMIT 4");
    }
}
