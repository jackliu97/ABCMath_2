<?php
namespace ABCMath\Vocabulary\Dictionary;

use ABCMath\Vocabulary\DefinitionManager;
use ABCMath\Vocabulary\Word;
use Symfony\Component\DomCrawler\Crawler;

class DictionaryDotCom extends DefinitionManager
{
    public $word;
    public $html;
    public $crawler;

    public function __construct(Word $word)
    {
        $this->word = $word;
        $this->html = null;
        $this->crawler = null;
    }

    public function extractDefinition()
    {
        $this->_getRemoteGetDefinition();
        $this->_buildDefinitionCrawler();

        $test = $this->crawler->filter('body > span')->extract('_text');
        print_r($test);
    }

    private function _buildDefinitionCrawler()
    {
        $this->crawler = new Crawler($this->html);
    }

    private function _getRemoteGetDefinition()
    {
        $uri = "http://dictionary.reference.com/browse/".
                urlencode($this->word->word);
        $this->html = file_get_contents($uri);
    }
}
