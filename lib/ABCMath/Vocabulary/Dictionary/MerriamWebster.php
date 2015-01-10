<?php
namespace ABCMath\Vocabulary\Dictionary;

use ABCMath\Vocabulary\Definition;
use ABCMath\Vocabulary\DefinitionManager;
use ABCMath\Vocabulary\Word;
use Symfony\Component\DomCrawler\Crawler;

class MerriamWebster extends DefinitionManager
{
    public $word;
    public $xml;
    public $crawler;

    public function __construct(Word $word)
    {
        $this->word = $word;
        $this->xml = null;
        $this->crawler = null;
        $this->definition_source_id = 1;
    }

    public function extractDefinition()
    {
        if ($this->_checkSuggestions() === false) {
            $this->word->markedAsChecked($this->definition_source_id);

            return;
        }

        $this->_getRemoteGetDefinition();
        $this->_buildDefinitionCrawler();

        /*
        * Retrieves individual definitions for this word
        * and process them individually.
        */
        $entries = $this->crawler->filterXPath('//entry_list/entry');

        if (!count($entries)) {
            $this->word->markedAsChecked($this->definition_source_id);

            return;
        }

        foreach ($entries as $entry) {
            $this->_processEntry($entry);
        }

        $this->word->markedAsChecked($this->definition_source_id);
    }

    private function _processEntry($entry)
    {
        /*
        * For each entry, extract
        *	word
        *	parts of speech
        *	definition
        */
        $crawler = new Crawler($entry);
        $word = $crawler->filterXPath('//hw')->text();
        $parts_of_speech = $crawler->filterXPath('//fl')->extract('_text');
        $def = $crawler->filterXPath('//def/dt')->extract('_text');

        if (count($def)) {
            foreach ($def as $d) {
                $definition = new Definition();
                $definition->load(array(
                    'word_id' => $this->word->id,
                    'word' => $word,
                    'parts_of_speech' => (isset($parts_of_speech[0]) ? $parts_of_speech[0] : ''),
                    'definition' => iconv(
                                            'UTF-8',
                                            'ASCII//TRANSLIT',
                                            (ucfirst(ltrim($d, ':')).'.')
                                        ),
                    'definition_source_id' => $this->definition_source_id,
                    ));

                $this->definitions[] = $definition;
            }
        }
    }

    /**
     * sometimes api might return suggestions in the following format.
     * if the spelling is slightly off.
     *
     * <?xml version="1.0" encoding="utf-8" ?>
     * <entry_list version="1.0">
     * 	<suggestion>Acheulean</suggestion>
     * </entry_list>
     *
     * we only care about one suggestion.
     * @return boolean true if we want to proceed with definition, false if not.
     */

    private function _checkSuggestions()
    {
        $this->_getRemoteGetDefinition();
        $this->_buildDefinitionCrawler();

        $suggestion = $this->crawler
                ->filterXPath('//suggestion');

        if (count($suggestion) === 0) {
            return true;
        }

        //if suggestions is not one.
        //we only want to do this for possible misspelling.
        if (count($suggestion) > 1) {
            return false;
        }
        $this->word->word = $suggestion->text();
        $this->word->save();
        $this->_getRemoteGetDefinition();
        $this->_buildDefinitionCrawler();

        return true;
    }

    private function _buildDefinitionCrawler()
    {
        $this->crawler = new Crawler($this->xml);
    }

    private function _getRemoteGetDefinition()
    {
        $uri = "http://www.dictionaryapi.com/api/v1/references/".
                urlencode(MERRIAMWEBSTER_REF)."/xml/".
                urlencode($this->word->word)."?key=".
                urlencode(MERRIAMWEBSTER_KEY);
        $this->xml = file_get_contents($uri);
    }
}
