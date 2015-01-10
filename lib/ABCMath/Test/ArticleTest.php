<?php
require_once "test_bootstrap.php";

use \ABCMath\Vocabulary\Word;

class ArticleTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->dataNoID = array(
            'title' => 'Test Article Title',
            'article' => 'Test Article Body... ',
        );

        $this->dataWithID = array(
            'id' => 1,
            'title' => 'Test Article Title',
            'article' => 'Test Article Body... ',
        );

        $this->fakeWords = array(
            array(
                'id' => 1,
                'word' => 'test',
                'definition' => 'test definition',
            ),

            array(
                'id' => 1,
                'word' => 'test1',
                'definition' => 'test definition 1',
            ),

            array(
                'id' => 2,
                'word' => 'test2',
                'definition' => 'test definition 2',
            ),

            array(
                'id' => 3,
                'word' => 'article',
                'definition' => 'test definition for article',
            ),
        );
    }

    //php phpunit.phar --filter testLoad__withNoID ArticleTest.php
    public function testLoad__withNoID()
    {
        $article = $this->getMock('\ABCMath\Article\Article',
            array( '_loadFromDb' ));

        $article->expects($this->never())
        ->method('_loadFromDb');

        $article->load($this->dataNoID);

        $this->assertEquals($article->title, $this->dataNoID['title']);
        $this->assertEquals($article->article, $this->dataNoID['article']);
    }

    //php phpunit.phar --filter testLoad__withIDParameter ArticleTest.php
    public function testLoad__withIDParameter()
    {
        $article = $this->getMock('\ABCMath\Article\Article',
            array( '_loadFromDb' ));

        $article->id = 1;

        $article->expects($this->once())
        ->method('_loadFromDb')
        ->will($this->returnValue($this->dataWithID));

        $article->load();

        $this->assertEquals($article->id, $this->dataWithID['id']);
        $this->assertEquals($article->title, $this->dataWithID['title']);
        $this->assertEquals($article->article, $this->dataWithID['article']);
    }

    //php phpunit.phar --filter testSave__insert ArticleTest.php
    public function testSave__insert()
    {
        $article = $this->getMock('\ABCMath\Article\Article',
            array(
                '_insert',
                '_update',
            )
        );

        $article->expects($this->once())
        ->method('_insert')
        ->will($this->returnValue(1));

        $article->expects($this->never())
        ->method('_update');

        $article->load($this->dataNoID);
        $article->save();

        $this->assertEquals($article->id, 1);
        $this->assertEquals($article->title, $this->dataNoID['title']);
        $this->assertEquals($article->article, $this->dataNoID['article']);
    }

    //php phpunit.phar --filter testSave__update ArticleTest.php
    public function testSave__update()
    {
        $article = $this->getMock('\ABCMath\Article\Article',
            array(
                '_insert',
                '_update',
            )
        );

        $article->expects($this->never())
        ->method('_insert')
        ->will($this->returnValue(1));

        $article->expects($this->once())
        ->method('_update');

        $article->load($this->dataWithID);
        $article->save();

        $this->assertEquals($article->id, $this->dataWithID['id']);
        $this->assertEquals($article->title, $this->dataWithID['title']);
        $this->assertEquals($article->article, $this->dataWithID['article']);
    }

    //php phpunit.phar --filter testGetVocabularyFromPassage ArticleTest.php
    public function testGetVocabularyFromPassage()
    {
        $article = $this->getMock('\ABCMath\Article\Article',
            array(
                '_loadFromDb',
            )
        );

        $article->expects($this->never())
        ->method('_loadFromDb');

        $this->dataWithID['article'] = 'test article this is a test articvle... etc';

        $article->load($this->dataWithID);

        $voc = $article->getVocabularyFromPassage($this->fakeWords);
        $this->assertEquals(count($voc), 2);
        $this->assertEquals($voc[0], $this->fakeWords[0]);
        $this->assertEquals($voc[1], $this->fakeWords[3]);
    }

    //php phpunit.phar --filter testGetVocabularyFromPassage__test_case_sensitive ArticleTest.php
    public function testGetVocabularyFromPassage__test_case_sensitive()
    {
        $article = $this->getMock('\ABCMath\Article\Article',
            array(
                '_loadFromDb',
            )
        );

        $article->expects($this->never())
        ->method('_loadFromDb');

        //should still provide the same result, even with upper cases
        $this->dataWithID['article'] = 'TEST Article this is a test Article... etc';

        $article->load($this->dataWithID);

        $voc = $article->getVocabularyFromPassage($this->fakeWords);
        $this->assertEquals(count($voc), 2);
        $this->assertEquals($voc[0], $this->fakeWords[0]);
        $this->assertEquals($voc[1], $this->fakeWords[3]);
    }

    //php phpunit.phar --filter testHighlightWordsInPassage__case_insensitivity ArticleTest.php
    public function testHighlightWordsInPassage__case_insensitivity()
    {
        $article = $this->getMock('\ABCMath\Article\Article',
            array(
                '_loadFromDb',
            )
        );

        $article->expects($this->never())
        ->method('_loadFromDb');

        $this->dataWithID['article'] = 'test Article this is a test article... etc';

        $article->load($this->dataWithID);

        $voc = $article->getVocabularyFromPassage($this->fakeWords);
        $result = $article->highlightWordsInPassage($voc);

        $resultPassage = 'test <strong class="text-danger highlighted_word"';
        $resultPassage .= ' title="test definition for article">article</strong> this is a ';
        $resultPassage .= '<strong class="text-danger highlighted_word" ';
        $resultPassage .= 'title="test definition">test</strong> article... etc';

        $this->assertEquals(count($voc), 2);
        $this->assertEquals($voc[0], $this->fakeWords[0]);
        $this->assertEquals($voc[1], $this->fakeWords[3]);

        $this->assertEquals($result, $resultPassage);
    }
}
