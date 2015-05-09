<?php
require_once "test_bootstrap.php";

use \ABCMath\ScrambledParagraph\Paragraph;
use \ABCMath\ScrambledParagraph\ScrambledParagraph;

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

    //php phpunit.phar --filter testCreateFromParagraph ScrambledParagraphTest.php
    public function testCreateFromParagraph__normalParagraph(){
        $testData = 'Getting Started with Doctrine This guide covers getting started with the ' . 
        'Doctrine ORM. After working through the guide you should know: How to install and ' . 
        'configure Doctrine by connecting it to a database Mapping PHP objects to database ' . 
        'tables Generating a database schema from PHP objects Using the EntityManager to insert, ' . 
        'update, delete and find objects in the database. Guide Assumptions This ' . 
        'guide is designed for beginners that haven’t worked with Doctrine ORM before. ' . 
        'There are some prerequesites for the tutorial that have to be installed: PHP 5.4 ' . 
        'or above Composer Package Manager (Install Composer) The code of this tutorial ' . 
        'is available on Github.';
        

        $result = ScrambledParagraph::createFromParagraph($testData);
        $this->assertEquals(count($result), 4);
        $this->assertEquals(
            $result[0], 
            'Getting Started with Doctrine This guide covers getting ' . 
            'started with the Doctrine ORM.');
        $this->assertEquals(
            $result[3],
            'There are some prerequesites for the tutorial that have to be installed: ' . 
            'PHP 5.4 or above Composer Package Manager (Install Composer) The code of ' . 
            'this tutorial is available on Github.');
    }

    //php phpunit.phar --filter testCreateFromParagraph__dotsParagraph ScrambledParagraphTest.php
    public function testCreateFromParagraph__dotsParagraph(){
        $testData = 'Getting Started with Doctrine This guide covers getting started with the ' . 
        'Doctrine ORM. After working through Mr. Steven, the guide you should know: How to install and ' . 
        'configure Doctrine by connecting it to a database Mapping PHP objects to database ' . 
        'tables Generating a database schema from PHP objects Using the EntityManager to insert, ' . 
        'update, delete and find objects in the database. Guide Assumptions This ' . 
        'guide is designed for beg...inners that haven’t worked with Doctrine ORM before. ' . 
        'There are some prerequesites for the tutorial that have to be installed: PHP 5.4 ' . 
        'or above Composer Package Manager (Install Composer) The code of this tutorial ' . 
        'is available on Github...';
        

        $result = ScrambledParagraph::createFromParagraph($testData);

        $this->assertEquals(count($result), 4);
        $this->assertEquals(
            $result[0], 
            'Getting Started with Doctrine This guide covers getting ' . 
            'started with the Doctrine ORM.');
        $this->assertEquals(
            $result[2],
            'Guide Assumptions This guide is designed for beg...inners ' . 
            'that haven’t worked with Doctrine ORM before.'
            );
        $this->assertEquals(
            $result[3],
            'There are some prerequesites for the tutorial that have to be installed: ' . 
            'PHP 5.4 or above Composer Package Manager (Install Composer) The code of ' . 
            'this tutorial is available on Github...');
    }

    //php phpunit.phar --filter testCreateFromQuestion__normalQuestion ScrambledParagraphTest.php
    public function testCreateFromQuestion__normalQuestion(){
        $testData = "A lot of U.S. analysts have suggested that, if there is a breakthrough, and potentially there could be a breakthrough between the U.S. and Iran right now, the international sanctions led by the U.S. have really made a dent on the life of the people of Iran, and maybe that's going to be a major factor in convincing the Iranian people and President Rouhani that maybe it's time for a change. There's no question that that has played a huge role.
A. The White House did an internal study where they tried to understand Iranian behavior over the last 30 years, and they found that the Iranians moved and were more conciliatory. But almost always when they faced pressure that it was actually a rational behavior, but you had to pressure them.
B. And so they put together this very impressive international set of sanctions. That's why they took it through the U.N. Now here's the twist - the sanctions have been put in place by law, by Congress.
C. The president does not have the ability to unilaterally waive those sanctions. So if the Iranians start complying, doing the kinds of things he's talking about - transparency, verifiable acts - he actually doesn't have the ability to deliver the carrots, if one may call them that, to the Iranians.
D. So he's now going to be placed in a very awkward position where he can encourage this process, but it's not clear that he can actually...we all worry about Rouhani being able to deliver.
E. The Iranians, I'm sure very smart about this, are wondering whether Obama can deliver. On the edges, he does have a little flexibility, .";
        $result = ScrambledParagraph::createFromQuestion($testData);
        print_r($result);
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
