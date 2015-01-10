<?php
require_once "test_bootstrap.php";

use \ABCMath\Vocabulary\Word;

class WordTest extends PHPUnit_Framework_TestCase {

    protected $dataNoID;
    public static function setUpBeforeClass() {}

    protected function setUp() {
        $this->dataNoID = array(
            'word' => 'abbey',
            'definitions'=>array(
                0 =>
                array(
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'A monastery ruled by an abbot.',
                ),
                1 =>
                array(
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'A convent ruled by an abbess.',
                ),
                2 =>
                array(
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'An abbey church.',
                ),
                3 =>
                array(
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'Edwin Austin 1852-1911 Am. painter & illustrator.',
                ),
            )
        );
        $this->dataWithID = array(
            'id' => '7',
            'word' => 'abbey',
            'definitions'=>array(
                0 =>
                array(
                    'id' => '1',
                    'word_id' => '7',
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'A monastery ruled by an abbot.',
                ),
                1 =>
                array(
                    'id' => '2',
                    'word_id' => '7',
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'A convent ruled by an abbess.',
                ),
                2 =>
                array(
                    'id' => '3',
                    'word_id' => '7',
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'An abbey church.',
                ),
                3 =>
                array(
                    'id' => '4',
                    'word_id' => '7',
                    'word' => 'abbey',
                    'parts_of_speech' => 'noun',
                    'definition' => 'Edwin Austin 1852-1911 Am. painter & illustrator.',
                ),
            )
        );
        $this->dataWithIDNoDefinitions = array(
            'id' => '7',
            'word' => 'abbey',
        );

        $this->definitionDataWithID = array(
            0 =>
            array(
                'id' => '1',
                'word_id' => '7',
                'word' => 'abbey',
                'parts_of_speech' => 'noun',
                'definition' => 'A monastery ruled by an abbot.',
            ),
            1 =>
            array(
                'id' => '2',
                'word_id' => '7',
                'word' => 'abbey',
                'parts_of_speech' => 'noun',
                'definition' => 'A convent ruled by an abbess.',
            ),
            2 =>
            array(
                'id' => '3',
                'word_id' => '7',
                'word' => 'abbey',
                'parts_of_speech' => 'noun',
                'definition' => 'An abbey church.',
            ),
            3 =>
            array(
                'id' => '4',
                'word_id' => '7',
                'word' => 'abbey',
                'parts_of_speech' => 'noun',
                'definition' => 'Edwin Austin 1852-1911 Am. painter & illustrator.',
            ),
        );
    }

    //php phpunit.phar --filter testLoad__withNoID WordTest.php
    public function testLoad__withNoID() {
        $word = $this->getMock( '\ABCMath\Vocabulary\Word',
            array( '_loadFromDbWithId',
                '_loadFromDbWithWord',
                '_loadDefinitions',
                '_update',
                '_insert' ) );

        $word->expects( $this->never() )
        ->method( '_loadFromDbWithId' );


        $word->expects( $this->never() )
        ->method( '_loadFromDbWithWord' );

        $word->expects( $this->never() )
        ->method( '_loadDefinitions' );

        $word->load( $this->dataNoID );

        $this->assertEquals( $word->word, $this->dataNoID['word'] );
        $this->assertEquals( count( $word->definitions ), count( $this->dataNoID['definitions'] ) );

        foreach ( $this->dataNoID['definitions'] as $index=>$def ) {
            foreach ( $def as $k=>$v ) {
                $this->assertEquals( $word->definitions[$index]->{$k}, $v );
            }
        }

    }

    //php phpunit.phar --filter testLoad__withID WordTest.php
    public function testLoad__withID() {
        $word = $this->getMock( '\ABCMath\Vocabulary\Word',
            array( '_loadFromDbWithId',
                '_loadFromDbWithWord',
                '_loadDefinitions',
                '_update',
                '_insert' ) );

        $word->expects( $this->once() )
        ->method( '_loadFromDbWithId' )
        ->will( $this->returnValue( $this->dataWithIDNoDefinitions ) );


        $word->expects( $this->never() )
        ->method( '_loadFromDbWithWord' );

        $word->expects( $this->once() )
        ->method( '_loadDefinitions' )
        ->will( $this->returnValue( $this->definitionDataWithID ) );

        $word->id = 7;
        $word->load();

        $this->assertEquals( $word->id, $this->dataWithIDNoDefinitions['id'] );
        $this->assertEquals( $word->word, $this->dataWithIDNoDefinitions['word'] );
        $this->assertEquals( count( $word->definitions ), count( $this->definitionDataWithID ) );

        foreach ( $this->definitionDataWithID as $index=>$def ) {
            foreach ( $def as $k=>$v ) {
                $this->assertEquals( $word->definitions[$index]->{$k}, $v );
            }
        }
    }

    //php phpunit.phar --filter testLoad__withWord WordTest.php
    public function testLoad__withWord() {
        $word = $this->getMock( '\ABCMath\Vocabulary\Word',
            array( '_loadFromDbWithId',
                '_loadFromDbWithWord',
                '_loadDefinitions',
                '_update',
                '_insert' ) );

        $word->expects( $this->never() )
        ->method( '_loadFromDbWithId' );


        $word->expects( $this->once() )
        ->method( '_loadFromDbWithWord' )
        ->will( $this->returnValue( $this->dataWithIDNoDefinitions ) );

        $word->expects( $this->once() )
        ->method( '_loadDefinitions' )
        ->will( $this->returnValue( $this->definitionDataWithID ) );

        $word->word = 'WordTest';
        $word->load();

        $this->assertEquals( $word->id, $this->dataWithIDNoDefinitions['id'] );
        $this->assertEquals( $word->word, $this->dataWithIDNoDefinitions['word'] );
        $this->assertEquals( count( $word->definitions ), count( $this->definitionDataWithID ) );

        foreach ( $this->definitionDataWithID as $index=>$def ) {
            foreach ( $def as $k=>$v ) {
                $this->assertEquals( $word->definitions[$index]->{$k}, $v );
            }
        }
    }

    //php phpunit.phar --filter testSave__insert WordTest.php
    public function testSave__insert() {

        $word = $this->getMock( '\ABCMath\Vocabulary\Word',
            array( '_loadFromDbWithId',
                '_loadFromDbWithWord',
                '_loadDefinitions',
                '_update',
                '_insert',
                '_saveDefinitions' ) );

        $word->expects( $this->never() )
        ->method( '_loadFromDbWithWord' );

        $word->expects( $this->never() )
        ->method( '_loadDefinitions' );

        $word->expects( $this->never() )
        ->method( '_update' );

        $word->expects( $this->once() )
        ->method( '_insert' )
        ->will( $this->returnValue( 1 ) );

        $word->expects( $this->once() )
        ->method( '_saveDefinitions' );

        $word->load( $this->dataNoID );
        $word->save();

        $this->assertEquals( $word->id, 1 );

    }

}
