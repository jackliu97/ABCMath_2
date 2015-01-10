<?php
require_once "test_bootstrap.php";

class ReadingComprehensionExamTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    //php phpunit.phar --filter testGetRandomExamSQL__ten ReadingComprehensionExamTest.php
  public function testGetRandomExamSQL__ten()
  {
      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehensionExam',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $sql = $rc->getRandomExamSQL(10, 10);
      $this->assertEquals($sql, "SELECT rc.id FROM reading_comprehension rc ".
        "LEFT JOIN reading_comprehension_keyword rck ".
          "ON rc.id = rck.reading_comprehension_id ".
        "WHERE rck.keyword_id IN (10) ".
        "ORDER BY RAND() ".
        "LIMIT 10");

      $sql = $rc->getRandomExamSQL(array(10), 1);
      $this->assertEquals($sql, "SELECT rc.id FROM reading_comprehension rc ".
        "LEFT JOIN reading_comprehension_keyword rck ".
          "ON rc.id = rck.reading_comprehension_id ".
        "WHERE rck.keyword_id IN (10) ".
        "ORDER BY RAND() ".
        "LIMIT 1");

      $sql = $rc->getRandomExamSQL(array(10, 11), 4);
      $this->assertEquals($sql, "SELECT rc.id FROM reading_comprehension rc ".
        "LEFT JOIN reading_comprehension_keyword rck ".
          "ON rc.id = rck.reading_comprehension_id ".
        "WHERE rck.keyword_id IN (10,11) ".
        "ORDER BY RAND() ".
        "LIMIT 4");
  }
}
