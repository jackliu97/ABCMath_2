<?php
require_once "test_bootstrap.php";

use \ABCMath\Entity\EntityManager;

class EntityManagerTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {}

	protected function setUp() {}

	//php phpunit.phar --filter testSelectAll EntityManagerTest.php
	public function testSelectAll() {
		$em = new EntityManager();
		$em->getAllEntities();

		//print_r( $student );
	}

	//php phpunit.phar --filter testSelectAllTables EntityManagerTest.php
	public function testSelectAllTables() {
		$em = new EntityManager();
		$result = $em->getAllTables();

		print_r($result);

		//print_r( $student );
	}

	//php phpunit.phar --filter testSelectAllTableFields EntityManagerTest.php
	public function testSelectAllTableFields() {
		$em = new EntityManager();
		$result = $em->getAllTableColumns('students');

		print_r($result);

		//print_r( $student );
	}


}
