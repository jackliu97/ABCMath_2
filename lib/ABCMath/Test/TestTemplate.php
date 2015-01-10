<?php
require_once "test_bootstrap.php";

use \ABCMath\Template\Template;

class TestTemplate extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    protected function setUp()
    {
    }

    protected function tearDown()
    {
        //must clear twig cache.
        $cacheDir = 'Cache';
        $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($cacheDir);
    }

    //php phpunit.phar --filter testSetParameters__string TestTemplate.php
    public function testSetParameters__string()
    {
        $template = new Template(null, true);
        $templateMainDir = __DIR__.'/testFiles/';
        $variable = array(
            'test_string' => 'String, blah blah',
            );

        $h = fopen($templateMainDir.'test.twig', "r");
        $fileOutput = fread($h, filesize($templateMainDir.'test.twig'));

        $this->assertEquals(
            'Test String here: String, blah blah',
            $template->render($fileOutput, $variable)
            );
    }

    //php phpunit.phar --filter testSetParameters__filesystem TestTemplate.php
    public function testSetParameters__filesystem()
    {
        $template = new Template(Template::FILESYSTEM, true);

        $file = 'test.twig';
        $variable = array(
            'test_string' => 'String, blah blah',
            );

        $this->assertEquals(
            'Test String here: String, blah blah',
            $template->render($file, $variable)
            );
    }
}
