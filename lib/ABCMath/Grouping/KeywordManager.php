<?php

namespace ABCMath\Grouping;

use ABCMath\Grouping\Keyword;
use ABCMath\Meta\Implement\ElementList;
use ABCMath\Base;

class KeywordManager extends Base implements ElementList
{
    public $keywordList;
    public $category;
    public function __constructor()
    {
        parent::__construct();
        $this->keywordList = array();
        $this->category = null;
    }

    public function load($data = array())
    {
        if (!is_array($data) || !count($data)) {
            return false;
        }

        foreach ($data as $word) {
            $keyword = new Keyword();
            $keyword->load($word);
            $this->keywordList[] = $keyword;
        }
    }

    /*
    * Binds this list of keyword to this object.
    */
    public function bind($table, $external_id)
    {
        if (!count($this->keywordList)) {
            return array(
                'success' => false,
                'message' => 'No Keyword in list.',
                );
        }

        $this->_conn->beginTransaction();

        $this->unbindAll($table, $external_id);

        foreach ($this->keywordList as $keyword) {
            $keyword->save();
            $keyword->bind($table, $external_id);
        }

        $this->_conn->commit();
    }

    public function all()
    {
        $qb = $this->_conn->createQueryBuilder();
        $qb->select('k.id', 'k.word')
            ->from('keyword', 'k');

        return $qb->execute()->fetchAll();
    }

    public function allFormatted()
    {
        $kw = $this->all();
        foreach ($kw as $word) {
            $return[$word['id']] = $word['word'];
        }

        return $return;
    }

    public function loadKeywordByQuestion($table, $id)
    {
        $keywords = $this->getKeywordByQuestion($table, $id);

        if (count($keywords)) {
            foreach ($keywords as $id => $word) {
                $keyword = new Keyword($id);
                $keyword->word = $word;
                $this->keywordList[] = $keyword;
            }
        }
    }

    public function getKeywordByQuestion($table, $id)
    {
        $existing_keyword = array();
        $kw_table = "{$table}_keyword";
        $obj_field = "xk.{$table}_id";

        if ($id == '') {
            return $existing_keyword;
        }

        $q = "SELECT kw.id, kw.word
				FROM keyword kw
				INNER JOIN {$kw_table} xk
					ON xk.keyword_id = kw.id
				WHERE {$obj_field} = {$id}
				GROUP BY kw.id";

        $stmt = $this->_conn->prepare($q);
        $stmt->execute();
        $keywords = $stmt->fetchAll();

        if (count($keywords) == 0) {
            return $existing_keyword;
        }
        foreach ($keywords as $row) {
            $existing_keyword[$row['id']] = $row['word'];
        }

        return $existing_keyword;
    }

    public function getKeywordsByCategory()
    {
        $return = array();
        if (is_null($this->category)) {
            throw new Exception('Category improperly set.');
        }

        $q = "SELECT kw.id, kw.word
				FROM keyword kw
				INNER JOIN {$this->category}_keyword xk
					ON xk.keyword_id = kw.id
				GROUP BY kw.id";
        $stmt = $this->_conn->prepare($q);
        $stmt->execute();
        $keywords = $stmt->fetchAll();

        if (count($keywords)) {
            foreach ($keywords as $kw) {
                $keyword = new KeyWord();
                $keyword->id = $kw['id'];
                $keyword->word = $kw['word'];
                $return[] = $keyword;
            }
        }

        return $return;
    }

    /**
     * Unbinds all keywords for this table/external_id combo.
     *
     * @param $table
     * @param $external_id
     */
    public function unbindAll($table, $external_id)
    {
        if (!$external_id) {
            throw new Exception('ID is required in order to delete.');
        }

        $this->_conn->delete("{$table}_keyword",
                                array(
                                    "{$table}_id" => $external_id, )
                            );
    }
}
