<?php
require_once "test_bootstrap.php";

use \ABCMath\Grouping\Keyword,
\ABCMath\Grouping\KeywordManager;

class KeywordTest extends PHPUnit_Framework_TestCase {

  protected $dataNoID;
  protected $dataWithID;
  public static function setUpBeforeClass() {}

  protected function setUp() {
    $this->readingDataWithID = array(
      'id'=>1,
      'full_text'=>"\"They're trying to kill me!\" the boy screamed.The pioneers of the teaching of science imagined that its
    introduction into education would remove the conventionality,
    artificiality, and backward-lookingness which were characteristic;
    of classical studies, but they were gravely disappointed. So, too, in
their time had the humanists thought that the study of the classical
    authors in the original would banish at once the dull pedantry and
    superstition of mediaeval scholasticism. The professional
    schoolmaster was a match for both of them, and has almost
    managed to make the understanding of chemical reactions as dull
and as dogmatic an affair as the reading of Virgil's Aeneid.
    The chief claim for the use of science in education is that it
    teaches a child something about the actual universe in which he is
    living, in making him acquainted with the results of scientific
 discovery, and at the same time teaches him how to think logically
    and inductively by studying scientific method. A certain limited
    success has been reached in the first of these aims, but practically
    none at all in the second. Those privileged members of the
    community who have been through a secondary or public school
 education may be expected to know something about the
    elementary physics and chemistry of a hundred years ago, but they
    probably know hardly more than any bright boy can pick up from
    an interest in wireless or scientific hobbies out of school hours.
    As to the learning of scientific method, the whole thing is palpably
 a farce. Actually, for the convenience of teachers and the
    requirements of the examination system, it is necessary that the
    pupils not only do not learn scientific method but learn precisely
    the reverse, that is, to believe exactly what they are told and to
    reproduce it when asked, whether it seems nonsense to them or
 not. The way in which educated people respond to such quackeries
    as spiritualism or astrology, not to say more dangerous ones such
    as racial theories or currency myths, shows that fifty years of
    education in the method of science in Britain or Germany has
    produced no visible effect whatever. The only way of learning the
method of science is the long and bitter way of personal
    experience, and, until the educational or social systems are altered
    to make this possible, the best we can expect is the production of a
    minority of people who are able to acquire some of the techniques
    of science and a still smaller minority who are able to use and
develop them.",
      'lines'=>array(
        0 => '"They\'re trying to kill me!" the boy screamed.The pioneers of the teaching of science imagined that its',
        1 => 'introduction into education would remove the conventionality,',
        2 => 'artificiality, and backward-lookingness which were characteristic;',
        3 => 'of classical studies, but they were gravely disappointed. So, too, in',
        4 => 'their time had the humanists thought that the study of the classical',
        5 => 'authors in the original would banish at once the dull pedantry and',
        6 => 'superstition of mediaeval scholasticism. The professional',
        7 => 'schoolmaster was a match for both of them, and has almost',
        8 => 'managed to make the understanding of chemical reactions as dull',
        9 => 'and as dogmatic an affair as the reading of Virgil\'s Aeneid.',
        10 => 'The chief claim for the use of science in education is that it',
        11 => 'teaches a child something about the actual universe in which he is',
        12 => 'living, in making him acquainted with the results of scientific',
        13 => 'discovery, and at the same time teaches him how to think logically',
        14 => 'and inductively by studying scientific method. A certain limited',
        15 => 'success has been reached in the first of these aims, but practically',
        16 => 'none at all in the second. Those privileged members of the',
        17 => 'community who have been through a secondary or public school',
        18 => 'education may be expected to know something about the',
        19 => 'elementary physics and chemistry of a hundred years ago, but they',
        20 => 'probably know hardly more than any bright boy can pick up from',
        21 => 'an interest in wireless or scientific hobbies out of school hours.',
        22 => 'As to the learning of scientific method, the whole thing is palpably',
        23 => 'a farce. Actually, for the convenience of teachers and the',
        24 => 'requirements of the examination system, it is necessary that the',
        25 => 'pupils not only do not learn scientific method but learn precisely',
        26 => 'the reverse, that is, to believe exactly what they are told and to',
        27 => 'reproduce it when asked, whether it seems nonsense to them or',
        28 => 'not. The way in which educated people respond to such quackeries',
        29 => 'as spiritualism or astrology, not to say more dangerous ones such',
        30 => 'as racial theories or currency myths, shows that fifty years of',
        31 => 'education in the method of science in Britain or Germany has',
        32 => 'produced no visible effect whatever. The only way of learning the',
        33 => 'method of science is the long and bitter way of personal',
        34 => 'experience, and, until the educational or social systems are altered',
        35 => 'to make this possible, the best we can expect is the production of a',
        36 => 'minority of people who are able to acquire some of the techniques',
        37 => 'of science and a still smaller minority who are able to use and',
        38 => 'develop them.',
      ),
      'questions'=>array(
        0 =>
        array(
          'id' => '13',
          'reading_comprehension_id' => '1',
          'text' => 'The study of standards for what is right and what is wrong is called _____.',
          'original_text' => 'The study of standards for what is right and what is wrong is called _____.
a. pure science
b. applied science
c. ethics
d. technology
ANS: C',
          'choices' => array(
            0 =>
            array(
              'id' => '272',
              'reading_comprehension_question_id' => '13',
              'text' => 'pure science',
              'is_answer' => '0',
            ),
            1 =>
            array(
              'id' => '273',
              'reading_comprehension_question_id' => '13',
              'text' => 'applied science',
              'is_answer' => '0',
            ),
            2 =>
            array(
              'id' => '275',
              'reading_comprehension_question_id' => '13',
              'text' => 'technology',
              'is_answer' => '0',
            ),
            3 =>
            array(
              'id' => '274',
              'reading_comprehension_question_id' => '13',
              'text' => 'ethics',
              'is_answer' => '1',
            ),
          )
        ),
      )
    );

    $this->dataNoID = array (
      'word' => 'sat2',
      'type' => '',
    );

    $this->dataWithID = array (
      'id' => '8',
      'word' => 'sat2',
      'type' => '',
    );

    $this->batchKeyword = array (
      0 =>
      array (
        'id' => '11',
        'word' => '11th grade',
      ),
      1 =>
      array (
        'id' => '10',
        'word' => '12th grade',
      ),
      2 =>
      array (
        'id' => '27',
        'word' => 'Blah Blah bLah',
      ),
      3 =>
      array (
        'id' => '',
        'word' => 'test3',
      ),
    );
  }

  //php phpunit.phar --filter testLoad__withNoId KeywordTest.php
  public function testLoad__withNoId() {
    $kw = $this->getMock( '\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ) );

    $kw->expects( $this->never() )
    ->method( '_loadFromDb' );

    $kw->load( $this->dataNoID );
    $this->assertEquals( $kw->word, 'sat2' );

  }

  //php phpunit.phar --filter testLoad__withId KeywordTest.php
  public function testLoad__withId() {
    $kw = $this->getMock( '\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ) );

    $kw->expects( $this->once() )
    ->method( '_loadFromDb' )
    ->will( $this->returnValue( $this->dataWithID ) );

    $kw->load();
    $this->assertEquals( $kw->word, 'sat2' );

  }

  //php phpunit.phar --filter testSave__withoutId KeywordTest.php
  public function testSave__withoutId() {
    $kw = $this->getMock( '\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ) );

    $kw->expects( $this->never() )
    ->method( '_loadFromDb' );

    $kw->expects( $this->never() )
    ->method( '_update' );

    $kw->expects( $this->once() )
    ->method( '_insert' );

    $kw->load( $this->dataNoID );
    $kw->save();
    $this->assertEquals( $kw->word, 'sat2' );

  }

  //php phpunit.phar --filter testSave__withId KeywordTest.php
  public function testSave__withId() {
    $kw = $this->getMock( '\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ) );

    $kw->expects( $this->never() )
    ->method( '_loadFromDb' );

    $kw->expects( $this->once() )
    ->method( '_update' );

    $kw->expects( $this->never() )
    ->method( '_insert' );

    $kw->load( $this->dataWithID );
    $kw->save();
    $this->assertEquals( $kw->word, 'sat2' );

  }

  //php phpunit.phar --filter testBind__existingKeyword KeywordTest.php
  public function testBind__existingKeyword() {
    $kw = $this->getMock( '\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ) );

    $kw->load( $this->dataWithID );


    $rc = $this->getMock( '\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions' ) );

    $rc->load( $this->readingDataWithID );
    $rc->addKeyword( $kw );

    $kw->expects( $this->never() )
    ->method( '_insert' );

    $kw->expects( $this->never() )
    ->method( '_update' );

  }

  //php phpunit.phar --filter testBind__newKeyword KeywordTest.php
  public function testBind__newKeyword() {
    $kw = $this->getMock( '\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ) );
    $kw->expects( $this->once() )
    ->method( '_insert' )
    ->will( $this->returnValue( 12 ) );

    $kw->expects( $this->never() )
    ->method( '_update' );


    $rc = $this->getMock( '\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions' ) );

    $kw->load( $this->dataNoID );
    $rc->load( $this->readingDataWithID );
    $rc->addKeyword( $kw );

    $this->assertEquals( $rc->keywords[0]->id, 12 );


    $kw2 = $this->getMock( '\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ) );
    $kw2->expects( $this->once() )
    ->method( '_insert' )
    ->will( $this->returnValue( 14 ) );
    $kw2->load( $this->dataNoID );
    $rc->addKeyword( $kw2 );
    $this->assertEquals( $rc->keywords[1]->id, 14 );
  }

  //php phpunit.phar --filter testLoad__batchKeywords KeywordTest.php
  public function testLoad__batchKeywords() {

    $kwm = new KeywordManager();
    $kwm->load( $this->batchKeyword );
    $this->assertEquals( count( $kwm->keywordList ), 4 );

  }

}
