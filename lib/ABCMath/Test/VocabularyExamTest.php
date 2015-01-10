<?php
require_once "test_bootstrap.php";

class VocabularyExamTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    //php phpunit.phar --filter testGetRandomExamSQL__ten VocabularyExamTest.php
    public function testGetRandomExamSQL__ten()
    {
        $v = $this->getMock('\ABCMath\Vocabulary\VocabularyExam',
          array( '_insert',
            '_update',
            '_loadFromDB',
            '_deleteAllLines',
            '_saveLines',
            'saveQuestions', ));

        $sql = $v->getRandomExamSQL(10, 10);
        $this->assertEquals($sql, "SELECT v.id FROM vocabulary v ".
            "LEFT JOIN vocabulary_keyword vk ".
              "ON v.id = vk.vocabulary_id ".
            "INNER JOIN vocabulary_definition vd ".
                    "ON v.id = vd.vocabulary_id ".
            "WHERE vk.keyword_id IN (10) ".
            "ORDER BY RAND() ".
            "LIMIT 10");

        $sql = $v->getRandomExamSQL(array(10), 1);
        $this->assertEquals($sql, "SELECT v.id FROM vocabulary v ".
            "LEFT JOIN vocabulary_keyword vk ".
              "ON v.id = vk.vocabulary_id ".
            "INNER JOIN vocabulary_definition vd ".
              "ON v.id = vd.vocabulary_id ".
            "WHERE vk.keyword_id IN (10) ".
            "ORDER BY RAND() ".
            "LIMIT 1");

        $sql = $v->getRandomExamSQL(array(10, 11), 4);
        $this->assertEquals($sql, "SELECT v.id FROM vocabulary v ".
            "LEFT JOIN vocabulary_keyword vk ".
              "ON v.id = vk.vocabulary_id ".
            "INNER JOIN vocabulary_definition vd ".
                    "ON v.id = vd.vocabulary_id ".
            "WHERE vk.keyword_id IN (10,11) ".
            "ORDER BY RAND() ".
            "LIMIT 4");
    }
}
