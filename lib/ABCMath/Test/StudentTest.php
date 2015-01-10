<?php
require_once "test_bootstrap.php";

use \ABCMath\Student\Student;

class StudentTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    protected function setUp()
    {
    }

    //php phpunit.phar --filter testSetParameters ReadingComprehensionTest.php
    public function testSetParameters()
    {
        $student = new Student();
        $student->setId(1);
        $student->load();

        //print_r( $student );
    }
}
