<?php
require_once "test_bootstrap.php";

use \ABCMath\ScrambledParagraph\Paragraph;

class ScrambledParagraphTest extends PHPUnit_Framework_TestCase
{
    protected $dataNoID;
    public static function setUpBeforeClass()
    {
    }

    protected function setUp()
    {
        $this->dataNoID = array(
            'full_text' => 'What I was struck by was this was not a speech, though, designed to make headlines. Both the things you pointed out were the parts that made news, but by and large it was really using the bully pulpit of the United Nations to educate people about what America\'s policy and what its interests are, particularly in the Middle East.He laid it out methodically, acknowledging criticism, reminding people why the United States had done certain things in Egypt, done certain things in Libya.And in that sense, [Obama] sort of took on the role that he has often taken on which is a kind of "explainer in chief," and did it very well.I thought it was a very intelligent speech. He laid out the ground work. More from GPS: Why Obama shouldn\'t talk to RouhaniIt also reminded us that the president actually has a pretty ambitious agenda.You know if you think of the three things he talked about really - Syria, Iran and the Middle East peace process. Syria is a crisis thrust upon the administration, thrust upon the world.But the other two were choices made by the Obama administration to pursue some kind of a diplomatic breakthrough with Iran and to try to resolve the Arab-Israeli peace process. If there\'s movement on all three fronts, maybe he will end up deserving that Noble Peace Prize that he was awarded..',
            'lines' => array(
                0 => array(
                    'order_id' => '0',
                    'text' => 'What I was struck by was this was not a speech, though, designed to make headlines. Both the things you pointed out were the parts that made news, but by and large it was really using the bully pulpit of the United Nations to educate people about what America\'s policy and what its interests are, particularly in the Middle East.',
                ),
                1 => array(
                    'order_id' => '1',
                    'text' => 'He laid it out methodically, acknowledging criticism, reminding people why the United States had done certain things in Egypt, done certain things in Libya.',
                ),
                2 => array(
                    'order_id' => '2',
                    'text' => 'And in that sense, [Obama] sort of took on the role that he has often taken on which is a kind of "explainer in chief," and did it very well.',
                ),
                3 => array(
                    'order_id' => '3',
                    'text' => 'I thought it was a very intelligent speech. He laid out the ground work. More from GPS: Why Obama shouldn\'t talk to RouhaniIt also reminded us that the president actually has a pretty ambitious agenda.',
                ),
                4 => array(
                    'order_id' => '4',
                    'text' => 'You know if you think of the three things he talked about really - Syria, Iran and the Middle East peace process. Syria is a crisis thrust upon the administration, thrust upon the world.',
                ),
                5 => array(
                    'order_id' => '5',
                    'text' => 'But the other two were choices made by the Obama administration to pursue some kind of a diplomatic breakthrough with Iran and to try to resolve the Arab-Israeli peace process. If there\'s movement on all three fronts, maybe he will end up deserving that Noble Peace Prize that he was awarded..',
                ),
            ),
        );

        $this->dataWithID = array(
            'id' => '21',
            'full_text' => 'What I was struck by was this was not a speech, though, designed to make headlines. Both the things you pointed out were the parts that made news, but by and large it was really using the bully pulpit of the United Nations to educate people about what America\'s policy and what its interests are, particularly in the Middle East.He laid it out methodically, acknowledging criticism, reminding people why the United States had done certain things in Egypt, done certain things in Libya.And in that sense, [Obama] sort of took on the role that he has often taken on which is a kind of "explainer in chief," and did it very well.I thought it was a very intelligent speech. He laid out the ground work. More from GPS: Why Obama shouldn\'t talk to RouhaniIt also reminded us that the president actually has a pretty ambitious agenda.You know if you think of the three things he talked about really - Syria, Iran and the Middle East peace process. Syria is a crisis thrust upon the administration, thrust upon the world.But the other two were choices made by the Obama administration to pursue some kind of a diplomatic breakthrough with Iran and to try to resolve the Arab-Israeli peace process. If there\'s movement on all three fronts, maybe he will end up deserving that Noble Peace Prize that he was awarded..',
            'lines' => array(
                0 => array(
                    'order_id' => '0',
                    'text' => 'What I was struck by was this was not a speech, though, designed to make headlines. Both the things you pointed out were the parts that made news, but by and large it was really using the bully pulpit of the United Nations to educate people about what America\'s policy and what its interests are, particularly in the Middle East.',
                ),
                1 => array(
                    'order_id' => '1',
                    'text' => 'He laid it out methodically, acknowledging criticism, reminding people why the United States had done certain things in Egypt, done certain things in Libya.',
                ),
                2 => array(
                    'order_id' => '2',
                    'text' => 'And in that sense, [Obama] sort of took on the role that he has often taken on which is a kind of "explainer in chief," and did it very well.',
                ),
                3 => array(
                    'order_id' => '3',
                    'text' => 'I thought it was a very intelligent speech. He laid out the ground work. More from GPS: Why Obama shouldn\'t talk to RouhaniIt also reminded us that the president actually has a pretty ambitious agenda.',
                ),
                4 => array(
                    'order_id' => '4',
                    'text' => 'You know if you think of the three things he talked about really - Syria, Iran and the Middle East peace process. Syria is a crisis thrust upon the administration, thrust upon the world.',
                ),
                5 => array(
                    'order_id' => '5',
                    'text' => 'But the other two were choices made by the Obama administration to pursue some kind of a diplomatic breakthrough with Iran and to try to resolve the Arab-Israeli peace process. If there\'s movement on all three fronts, maybe he will end up deserving that Noble Peace Prize that he was awarded..',
                ),
            ),
        );

        $this->paragraphData = array(
            'id' => '21',
            'full_text' => 'What I was struck by was this was not a speech, though, designed to make headlines. Both the things you pointed out were the parts that made news, but by and large it was really using the bully pulpit of the United Nations to educate people about what America\'s policy and what its interests are, particularly in the Middle East.He laid it out methodically, acknowledging criticism, reminding people why the United States had done certain things in Egypt, done certain things in Libya.And in that sense, [Obama] sort of took on the role that he has often taken on which is a kind of "explainer in chief," and did it very well.I thought it was a very intelligent speech. He laid out the ground work. More from GPS: Why Obama shouldn\'t talk to RouhaniIt also reminded us that the president actually has a pretty ambitious agenda.You know if you think of the three things he talked about really - Syria, Iran and the Middle East peace process. Syria is a crisis thrust upon the administration, thrust upon the world.But the other two were choices made by the Obama administration to pursue some kind of a diplomatic breakthrough with Iran and to try to resolve the Arab-Israeli peace process. If there\'s movement on all three fronts, maybe he will end up deserving that Noble Peace Prize that he was awarded..',
        );

        $this->lineData = array(
            0 => array(
                'order_id' => '0',
                'text' => 'What I was struck by was this was not a speech, though, designed to make headlines. Both the things you pointed out were the parts that made news, but by and large it was really using the bully pulpit of the United Nations to educate people about what America\'s policy and what its interests are, particularly in the Middle East.',
            ),
            1 => array(
                'order_id' => '1',
                'text' => 'He laid it out methodically, acknowledging criticism, reminding people why the United States had done certain things in Egypt, done certain things in Libya.',
            ),
            2 => array(
                'order_id' => '2',
                'text' => 'And in that sense, [Obama] sort of took on the role that he has often taken on which is a kind of "explainer in chief," and did it very well.',
            ),
            3 => array(
                'order_id' => '3',
                'text' => 'I thought it was a very intelligent speech. He laid out the ground work. More from GPS: Why Obama shouldn\'t talk to RouhaniIt also reminded us that the president actually has a pretty ambitious agenda.',
            ),
            4 => array(
                'order_id' => '4',
                'text' => 'You know if you think of the three things he talked about really - Syria, Iran and the Middle East peace process. Syria is a crisis thrust upon the administration, thrust upon the world.',
            ),
            5 => array(
                'order_id' => '5',
                'text' => 'But the other two were choices made by the Obama administration to pursue some kind of a diplomatic breakthrough with Iran and to try to resolve the Arab-Israeli peace process. If there\'s movement on all three fronts, maybe he will end up deserving that Noble Peace Prize that he was awarded..',
            ),
        );
    }

    //php phpunit.phar --filter testLoad__fromParameter ScrambledParagraphTest.php
    public function testLoad__fromParameter()
    {
        $sp = $this->getMock('\ABCMath\ScrambledParagraph\ScrambledParagraph',
            array( '_loadFromDb', '_loadLinesFromDB' ));

        $sp->expects($this->never())
        ->method('_loadFromDb');

        $sp->expects($this->never())
        ->method('_loadLinesFromDB');

        $sp->load($this->dataNoID);

        $this->assertEquals($sp->id, null);
        $this->assertEquals($sp->full_text, $this->dataNoID['full_text']);
        $this->assertEquals($sp->lines, $this->dataNoID['lines']);
    }

    //php phpunit.phar --filter testLoad__fromDB ScrambledParagraphTest.php
    public function testLoad__fromDB()
    {
        $sp = $this->getMock('\ABCMath\ScrambledParagraph\ScrambledParagraph',
            array( '_loadFromDb', '_loadLinesFromDB' ));

        $sp->expects($this->once())
        ->method('_loadFromDb')
        ->will($this->returnValue($this->paragraphData));

        $sp->expects($this->once())
        ->method('_loadLinesFromDB')
        ->will($this->returnValue($this->lineData));

        $sp->load();

        $this->assertEquals($sp->id, '21');
        $this->assertEquals($sp->full_text, $this->paragraphData['full_text']);
        $this->assertEquals($sp->lines, $this->lineData);
    }

    //php phpunit.phar --filter testSave__success ScrambledParagraphTest.php
    public function testSave__success()
    {
        $sp = $this->getMock('\ABCMath\ScrambledParagraph\ScrambledParagraph',
            array(
                '_loadFromDb',
                '_loadLinesFromDB',
                '_insert',
                '_update',
                '_deleteAllLines',
                '_saveLine', ));

        $sp->expects($this->never())
        ->method('_loadFromDb');

        $sp->expects($this->never())
        ->method('_loadLinesFromDB');

        $sp->expects($this->once())
        ->method('_insert')
        ->will($this->returnValue('1'));

        $sp->expects($this->never())
        ->method('_update');

        $sp->expects($this->once())
        ->method('_deleteAllLines');

        $sp->expects($this->exactly(6))
        ->method('_saveLine');

        $sp->load($this->dataNoID);
        $return = $sp->save();

        $this->assertEquals($sp->id, '1');
        $this->assertTrue($return['success']);
    }

    //php phpunit.phar --filter testSave__failedAtNoIDReturned ScrambledParagraphTest.php
    public function testSave__failedAtNoIDReturned()
    {
        $sp = $this->getMock('\ABCMath\ScrambledParagraph\ScrambledParagraph',
            array(
                '_loadFromDb',
                '_loadLinesFromDB',
                '_insert',
                '_update',
                '_deleteAllLines',
                '_saveLine', ));

        $sp->expects($this->never())
        ->method('_loadFromDb');

        $sp->expects($this->never())
        ->method('_loadLinesFromDB');

        $sp->expects($this->once())
        ->method('_insert')
        ->will($this->returnValue(null));

        $sp->expects($this->never())
        ->method('_update');

        $sp->load($this->dataNoID);
        $return = $sp->save();

        $this->assertEquals($sp->id, null);
        $this->assertFalse($return['success']);
        $this->assertEquals($return['message'], 'No scrambled paragraph ID produced.');
    }

    //php phpunit.phar --filter testSave__failedNoLinesGiven ScrambledParagraphTest.php
    public function testSave__failedNoLinesGiven()
    {
        $sp = $this->getMock('\ABCMath\ScrambledParagraph\ScrambledParagraph',
            array(
                '_loadFromDb',
                '_loadLinesFromDB',
                '_insert',
                '_update',
                '_deleteAllLines',
                '_saveLine', ));

        $sp->expects($this->never())
        ->method('_loadFromDb');

        $sp->expects($this->never())
        ->method('_loadLinesFromDB');

        $sp->expects($this->once())
        ->method('_insert')
        ->will($this->returnValue('1'));

        $sp->expects($this->never())
        ->method('_update');

        $sp->expects($this->never())
        ->method('_deleteAllLines');

        $sp->expects($this->never())
        ->method('_saveLine');

        $this->dataNoID['lines'] = array();

        $sp->load($this->dataNoID);
        $return = $sp->save();

        $this->assertEquals($sp->id, '1');
        $this->assertFalse($return['success']);
        $this->assertEquals($return['message'], 'No lines inputted for this scrambled paragraph.');
    }

    //php phpunit.phar --filter testUpdate__success ScrambledParagraphTest.php
    public function testUpdate__success()
    {
        $sp = $this->getMock('\ABCMath\ScrambledParagraph\ScrambledParagraph',
            array(
                '_loadFromDb',
                '_loadLinesFromDB',
                '_insert',
                '_update',
                '_deleteAllLines',
                '_saveLine', ));

        $sp->expects($this->never())
        ->method('_loadFromDb');

        $sp->expects($this->never())
        ->method('_loadLinesFromDB');

        $sp->expects($this->never())
        ->method('_insert')
        ->will($this->returnValue('1'));

        $sp->expects($this->once())
        ->method('_update');

        $sp->expects($this->once())
        ->method('_deleteAllLines');

        $sp->expects($this->exactly(6))
        ->method('_saveLine');

        $sp->load($this->dataWithID);
        $return = $sp->save();

        $this->assertEquals($sp->id, '21');
        $this->assertTrue($return['success']);
    }
}
