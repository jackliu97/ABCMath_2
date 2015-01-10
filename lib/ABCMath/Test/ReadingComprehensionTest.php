<?php
require_once "test_bootstrap.php";

use \ABCMath\ReadingComprehension\ReadingComprehension;
use \ABCMath\Grouping\Keyword;

class ReadingComprehensionTest extends PHPUnit_Framework_TestCase
{
    protected $dataNoID;
    public static function setUpBeforeClass()
    {
    }

    protected function setUp()
    {
        $this->dataWithID = array(
      'id' => 1,
      'full_text' => "\"They're trying to kill me!\" the boy screamed.The pioneers of the teaching of science imagined that its
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
      'lines' => array(
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
      'questions' => array(
        0 => array(
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
            0 => array(
              'id' => '272',
              'reading_comprehension_question_id' => '13',
              'text' => 'pure science',
              'is_answer' => '0',
            ),
            1 => array(
              'id' => '273',
              'reading_comprehension_question_id' => '13',
              'text' => 'applied science',
              'is_answer' => '0',
            ),
            2 => array(
              'id' => '275',
              'reading_comprehension_question_id' => '13',
              'text' => 'technology',
              'is_answer' => '0',
            ),
            3 => array(
              'id' => '274',
              'reading_comprehension_question_id' => '13',
              'text' => 'ethics',
              'is_answer' => '1',
            ),
          ),
        ),
      ),
    );

        $this->dataNoID = array(
      'full_text' => "\"They're trying to kill me!\" the boy screamed.The pioneers of the teaching of science imagined that its
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
      'lines' => array(
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
      'questions' => array(
        0 => array(
          'text' => 'The study of standards for what is right and what is wrong is called _____.',
          'original_text' => 'The study of standards for what is right and what is wrong is called _____.
a. pure science
b. applied science
c. ethics
d. technology
ANS: C',
          'choices' => array(
            0 => array(
              'text' => 'pure science',
              'is_answer' => '0',
            ),
            1 => array(
              'text' => 'applied science',
              'is_answer' => '0',
            ),
            2 => array(
              'text' => 'technology',
              'is_answer' => '0',
            ),
            3 => array(
              'text' => 'ethics',
              'is_answer' => '1',
            ),
          ),
        ),
      ),
    );
    }

  //php phpunit.phar --filter testSave__success ReadingComprehensionTest.php
  public function testSave__success()
  {
      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $rc->expects($this->once())
    ->method('_insert')
    ->will($this->returnValue('1'));

      $rc->expects($this->never())
    ->method('_update');

      $rc->expects($this->exactly(1))
    ->method('_deleteAllLines');

      $rc->expects($this->exactly(39))
    ->method('_saveLines');

      $rc->expects($this->never())
    ->method('_loadFromDB');

      $rc->expects($this->exactly(1))
    ->method('saveQuestions')
    ->will($this->returnValue(
        array( 'success' => true )
      ));

      $rc->load($this->dataNoID);
      $result = $rc->save();

      $this->assertEquals($rc->id, '1');
      $this->assertEquals($result['success'], true);
  }

  //php phpunit.phar --filter testSave__failsAtSavingQuestion ReadingComprehensionTest.php
  public function testSave__failsAtSavingQuestion()
  {
      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $rc->expects($this->any())
    ->method('_insert')
    ->will($this->returnValue('1'));

      $rc->expects($this->never())
    ->method('_update');

      $rc->expects($this->any())
    ->method('_deleteAllLines');

      $rc->expects($this->any())
    ->method('_saveLines');

      $rc->expects($this->never())
    ->method('_loadFromDB');

      $rc->expects($this->any())
    ->method('saveQuestions')
    ->will($this->returnValue(
        array( 'success' => false, 'message' => 'test error' )
      ));

    //test failure at saving questions.
    $rc->load($this->dataNoID);
      $result = $rc->save();
      $this->assertEquals($result['success'], false);
      $this->assertEquals($result['message'], 'test error');
  }

  //php phpunit.phar --filter testSave__failsBecauseNoLines ReadingComprehensionTest.php
  public function testSave__failsBecauseNoLines()
  {
      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $rc->expects($this->any())
    ->method('_insert')
    ->will($this->returnValue('1'));

      $rc->expects($this->never())
    ->method('_update');

      $rc->expects($this->any())
    ->method('_deleteAllLines');

      $rc->expects($this->any())
    ->method('_saveLines');

      $rc->expects($this->never())
    ->method('_loadFromDB');

      $rc->expects($this->any())
    ->method('saveQuestions')
    ->will($this->returnValue(
        array( 'success' => false, 'message' => 'test error' )
      ));

    //test failure at saving lines.
    $this->dataNoID['lines'] = array();
      $rc->load($this->dataNoID);
      $result = $rc->save();
      $this->assertEquals($result['success'], false);
      $this->assertEquals($result['message'], 'At least one line is required.');
  }

  //php phpunit.phar --filter testSave__failsBecauseNoIDReturned ReadingComprehensionTest.php
  public function testSave__failsBecauseNoIDReturned()
  {
      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $rc->expects($this->any())
    ->method('_insert')
    ->will($this->returnValue(null));

      $rc->expects($this->never())
    ->method('_update');

      $rc->expects($this->any())
    ->method('_deleteAllLines');

      $rc->expects($this->any())
    ->method('_saveLines');

      $rc->expects($this->never())
    ->method('_loadFromDB');

      $rc->expects($this->any())
    ->method('saveQuestions')
    ->will($this->returnValue(
        array( 'success' => true )
      ));

      $rc->expects($this->any())
    ->method('_insert')
    ->will($this->returnValue(null));

    //test failure at saving
    $rc->load($this->dataNoID);
      $result = $rc->save();
      $this->assertEquals($result['success'], false);
      $this->assertEquals($result['message'], 'No reading comprehension ID produced.');
  }

  //php phpunit.phar --filter testLoad__fromParameter ReadingComprehensionTest.php
  public function testLoad__fromParameter()
  {
      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $rc->expects($this->never())
    ->method('_loadFromDB');

      $rc->load($this->dataNoID);

      $this->assertEquals($rc->full_text, $this->dataWithID['full_text']);
      $this->assertEquals($rc->id, null);
      $this->assertEquals(count($rc->lines), count($this->dataWithID['lines']));

      $export_data = $rc->export();
      $this->assertEquals($export_data['full_text'], $this->dataWithID['full_text']);
      $this->assertEquals($export_data['table'], 'reading_comprehension');

      $this->assertEquals($rc->questions[0]->id, null);
      $this->assertEquals($rc->questions[0]->text, $this->dataWithID['questions'][0]['text']);

      $this->assertEquals(
      $rc->questions[0]->choices[0]['text'],
      $this->dataWithID['questions'][0]['choices'][0]['text']
    );
  }

  //php phpunit.phar --filter testLoad__fromDB ReadingComprehensionTest.php
  public function testLoad__fromDB()
  {
      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $rc->expects($this->once())
    ->method('_loadFromDB')
    ->will($this->returnValue(
        $this->dataWithID
      ));

      $rc->load();

      $this->assertEquals($rc->full_text, $this->dataWithID['full_text']);
      $this->assertEquals($rc->id, $this->dataWithID['id']);
      $this->assertEquals(count($rc->lines), count($this->dataWithID['lines']));

      $export_data = $rc->export();
      $this->assertEquals($export_data['full_text'], $this->dataWithID['full_text']);
      $this->assertEquals($export_data['table'], 'reading_comprehension');

      $this->assertEquals($rc->questions[0]->id, $this->dataWithID['questions'][0]['id']);
      $this->assertEquals($rc->questions[0]->text, $this->dataWithID['questions'][0]['text']);

      $this->assertEquals(
      $rc->questions[0]->choices[0]['text'],
      $this->dataWithID['questions'][0]['choices'][0]['text']
    );
  }

  //php phpunit.phar --filter testSetKeyword__saveAndAdd ReadingComprehensionTest.php
  public function testSetKeyword__saveAndAdd()
  {
      $kw = $this->getMock('\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ));

      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $kw->expects($this->once())
    ->method('_insert')
    ->will($this->returnValue(
        12
      ));

      $kw->expects($this->never())
    ->method('_update');

      $kw->load(
      array(
        'word' => 'test keyword',
        'type' => '', )
    );
      $rc->load();
      $rc->addKeyword($kw);

      $this->assertEquals(count($rc->keywords), 1);
      $this->assertEquals($rc->keywords[0]->word, $kw->word);
      $this->assertEquals($rc->keywords[0]->id, 12);
  }

  //php phpunit.phar --filter testSetKeyword__justAdd ReadingComprehensionTest.php
  public function testSetKeyword__justAdd()
  {
      $kw = $this->getMock('\ABCMath\Grouping\Keyword',
      array( '_loadFromDb', '_update', '_insert' ));

      $rc = $this->getMock('\ABCMath\ReadingComprehension\ReadingComprehension',
      array( '_insert',
        '_update',
        '_loadFromDB',
        '_deleteAllLines',
        '_saveLines',
        'saveQuestions', ));

      $kw->expects($this->never())
    ->method('_insert');

      $kw->expects($this->never())
    ->method('_update');

      $kw->load(
      array(
        'id' => '1',
        'word' => 'test keyword',
        'type' => '', )
    );
      $rc->load();
      $rc->addKeyword($kw);

      $this->assertEquals(count($rc->keywords), 1);
      $this->assertEquals($rc->keywords[0]->word, $kw->word);
      $this->assertEquals($rc->keywords[0]->id, $kw->id);
  }
}
