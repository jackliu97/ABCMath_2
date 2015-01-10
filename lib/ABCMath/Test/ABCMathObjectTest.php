<?php
require_once "test_bootstrap.php";

use \ABCMath\Scaffolding\ABCMathObject;
use \ABCMath\Scaffolding\ABCMathObjectList;

class ABCMathObjectTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    protected function setUp()
    {
        $this->fakeEntityData = array(
              'id' => '1',
              'table_name' => 'students_fake',
              'display_name' => 'Students FAKE',
              'last_updated' => '2014-08-02 12:07:35',
            );

        $this->fakeEntityFieldData = array(
              'id' => '1',
              'table_name' => 'students',
              'display_name' => 'Students',
              'last_updated' => '2014-08-02 12:07:35',
            );
    }

    //php phpunit.phar --filter testLoad ABCMathObjectTest.php
    public function testLoad()
    {
        $obj = new ABCMathObject();
        $obj->entity_id = 6;
        $obj->id = 17;
        if ($obj->loadMeta()) {
            print("LOAD");
            $obj->load();
        };

        //print_r( $student );
    }

    //php phpunit.phar --filter testLoadList ABCMathObjectTest.php
    public function testLoadList()
    {
        $obj = new ABCMathObjectList();
        $obj->entity_id = 1;
        if ($obj->loadMeta()) {
            print("LOAD");
            $obj->load();
        };

        //print_r( $student );
    }
}
