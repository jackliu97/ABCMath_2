<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\Article\Article;
use \ABCMath\Article\ArticleManager;
use \ABCMath\Vocabulary\Word;
use \ABCMath\Vocabulary\WordManager;
use \ABCMath\Grouping\Keyword;
use \ABCMath\Grouping\KeywordManager;
use \ABCMath\Db\Datatable;

class passage extends CI_Controller
{
    public $keywordManager;

    public function __construct()
    {
        parent::__construct();
        if (!$this->User_Model->check_permission(array('passage.view', 'passage.edit'))) {
            header('Location: /landing');
        }

        $this->session->set_userdata('section', 'passage');

        $this->keywordManager = new KeywordManager();
        ABCMath\Permission\Navigation::$config['quicklink'] = false;
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('footer');
    }

    public function passage_detail($id)
    {
        $data = array();
        $article = new Article();
        $article->id = $id;
        $article->load();

        $data['article_id'] = $article->id;
        $data['article'] = $article->article;
        $data['title'] = $article->title;

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('passage/passage_detail', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('passage/passage_detail.js'),
                                        ));
    }

    public function list_passage()
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array();
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('passage/list_passages');
        $this->load->view('footer', array(
                                        'private_js' => array('passage/list_passages.js'),
                                        'datatable' => true,
                                        ));
    }

    public function get_all_passages()
    {
        $list = new ArticleManager();
        $dt = new Datatable();
        $dt->sql = $list->allSQL();
        $dt->columns = array( 'id', 'title', 'article');
        $result = $dt->processQuery();

        //print_r($result);

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 2) {
                        $lines = explode("\n", $col);
                        $result['aaData'][$key][$k] = substr($lines[0], 0, 100);
                    }
                    if ($k == 0) {
                        $result['aaData'][$key][$k] = "<button article_id='{$col}' ".
                            "class='btn article_detail glyphicon glyphicon-pencil' style='width:45px;'></button>&nbsp;".
                            "<button article_id='{$col}' ".
                            "class='btn more_info glyphicon glyphicon-chevron-down' style='width:45px;'></button>&nbsp;".
                            "<button article_id='{$col}' ".
                            "class='btn remove glyphicon glyphicon-remove' style='width:45px;'></button>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function add_passage()
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array();
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('keyword_form', array(
                            'keyword' => $this->keywordManager->allFormatted(),
                            ));
        $this->load->view('passage/add_passage', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('passage/add_passage.js', 'keyword_form.js'),
                                        ));
    }

    public function save_passage()
    {
        $result = array('success' => false, 'message' => '');
        $keywords = $this->input->post('keyword');
        $title = $this->input->post('title');
        $article_text = $this->input->post('article');

        try {
            $article = new Article();
            $article->load(array(
                'title' => $title,
                'article_text' => $article_text, ));

            $article->save();

            /*
            * Keywords Logic.
            */

            if ($keywords !== false) {
                $this->keywordManager->load($keywords);
                $this->keywordManager->bind('article', $article->id);
            }
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function parse_passage_all()
    {
        $result = array('success' => false, 'message' => '');
        $article_id = $this->input->post('article_id');

        try {
            $article = new Article();
            $article->id = $article_id;
            $article->load();

            $keywordManager = new KeywordManager();
            $keywordManager->category = 'vocabulary';
            $keywords = $keywordManager->getKeywordsByCategory();
            $vocabulary = $article->getWordFromArticleByKeyword($keywords);
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $result['keywords'] = $keywords;
        $result['words'] = $vocabulary;
        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function parse_passage()
    {
        $result = array('success' => false, 'message' => '');
        $keywords = $this->input->post('keyword');
        $passage = $this->input->post('passage');
        $wordManager = new WordManager();
        $article = new Article();

        if (!is_array($keywords) || !count($keywords)) {
            $result['message'] = 'At least one keyword must be selected.';
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        //we need a list of group ids.
        foreach ($keywords as $kw) {
            $keyword = new Keyword();
            $keyword->id = $kw['id'];
            $keyword->word = $kw['word'];
            $wordManager->addKeyword($keyword);
        }

        $article->load(array('article' => $passage));

        try {
            $result['vocabulary_ids'] = array();

            //get all words by that group, and single out all word that exists in the passage.
            $vocabularies = $wordManager->getWordsByKeyword();
            $result['vocabularies'] = $article->getVocabularyFromPassage($vocabularies);
            $result['passage'] = $article->highlightWordsInPassage($result['vocabularies']);
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        if (count($result['vocabularies'])) {
            foreach ($result['vocabularies'] as $voc) {
                $result['vocabulary_ids'][] = $voc['id'];
            }
        }

        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function delete_passage()
    {
        $result = array('success' => false, 'message' => '');
        $article_id = $this->input->post('article_id');

        try {
            $article = new Article();
            $article->id = $article_id;
            $article->delete();
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));

        return;
    }

    public function get_passage()
    {
        $result = array('success' => false, 'message' => '');
        $article_id = $this->input->post('article_id');

        try {
            $article = new Article();
            $article->id = $article_id;
            $article->load();
            $result['article'] = nl2br($article->article);
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));

        return;
    }
}
