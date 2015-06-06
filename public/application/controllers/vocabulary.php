<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\Vocabulary\Word;
use \ABCMath\Vocabulary\WordManager;
use \ABCMath\Vocabulary\Definition;
use \ABCMath\Grouping\Keyword;
use \ABCMath\Grouping\KeywordManager;
use \ABCMath\Db\Datatable;

class vocabulary extends CI_Controller
{
    public $keywordManager;

    public function __construct()
    {
        parent::__construct();

        if ($this->User_Model->check_permission('vocabulary.view') == false) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

        $this->session->set_userdata('section', 'vocabulary');

        $this->keywordManager = new KeywordManager();
        ABCMath\Permission\Navigation::$config['quicklink'] = false;
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('footer');
    }

    public function add_word()
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array();
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('keyword_form', array(
                            'keyword' => $this->keywordManager->allFormatted(),
                            ));
        $this->load->view('vocabulary/add_word', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('vocabulary/add_word.js', 'keyword_form.js'),
                                        ));
    }

    public function word_detail($id)
    {
        $word = new Word();
        $data = array();
        $word->id = $id;
        $word->load();

        $data['word'] = $word->word;
        $data['definition'] = $word->definitions;
        $data['word_id'] = $word->id;

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('keyword_form', array(
                            'existing_keyword' => $this->keywordManager->getKeywordByQuestion('vocabulary', $id),
                            'keyword' => $this->keywordManager->allFormatted(),
                            ));
        $this->load->view('vocabulary/word_detail', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('vocabulary/word_detail.js', 'keyword_form.js'),
                                        ));
    }

    public function get_word_detail()
    {
        $result = array('success' => false, 'message' => '');
        $word_id = $this->input->post('word_id');

        try {
            $word = new Word();
            $word->id = $word_id;
            $word->load();
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $result['word'] = $word;
        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function extract_definition()
    {
        $result = array('success' => false, 'message' => '');
        $word_id = $this->input->post('word_id');
        $source = $this->input->post('source');
        $source = "\ABCMath\Vocabulary\Dictionary\\{$source}";

        try {
            $word_obj = new Word();
            $word_obj->id = $word_id;
            $word_obj->load();

            $dictionary = new $source($word_obj);
            $dictionary->extractDefinition();
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json ' => $result));

            return;
        }

        $word = array();

        if (count($dictionary->definitions)) {
            foreach ($dictionary->definitions as $d) {
                $word[] = $d->toArray();
                $word_obj->addDefinition($d);
            }
        }

        $result['word'] = $word;
        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function save_word()
    {
        $result = array('success' => false, 'message' => '');
        $words = $this->input->post('words');
        $keywords = $this->input->post('keyword');

        if (!count($words)) {
            $result['message'] = 'At least one word is required.';
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        if (!is_array($keywords) || !count($keywords)) {
            $result['message'] = 'At least one keyword is required.';
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $wordManager = new WordManager();

        /*
        * Add word to word manager.
        */
        foreach ($words as $w) {
            $word = new Word();
            $word->load(array('word' => $w['word']));
            $wordManager->addWord($word);
        }

        /*
        * Keywords Logic.
        */
        foreach ($keywords as $kw) {
            $keyword = new Keyword();
            $keyword->id = $kw['id'];
            $keyword->word = $kw['word'];
            $wordManager->addKeyword($keyword);
        }

        $numRowAffected = $wordManager->insertList();
        $result['message'] = "Success. {$numRowAffected} rows affected.";

        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function list_words()
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array();
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('vocabulary/list_words');
        $this->load->view('footer', array(
                                        'private_js' => array('vocabulary/list_words.js'),
                                        'datatable' => true,
                                        ));
    }

    public function word_list_action()
    {
        $result = array('success' => false, 'message' => '');
        $list = new WordManager();
        $dt = new Datatable();
        $dt->sql = $list->allSQL();
        $dt->columns = array( 'id', 'word');
        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $result['aaData'][$key][$k] = "<button word_id='{$col}' ".
                            "class='btn word_detail glyphicon glyphicon-pencil' style='width:45px;'></button>&nbsp;".
                            "<button word_id='{$col}' ".
                            "class='btn more_info glyphicon glyphicon-chevron-down' style='width:45px;'></button>&nbsp;".
                            "<button word_id='{$col}' ".
                            "class='btn remove glyphicon glyphicon-remove' style='width:45px;'></button>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function delete_word()
    {
        $result = array('success' => false, 'message' => '');
        $word_id = $this->input->post('word_id');

        try {
            $word = new Word();
            $word->id = $word_id;
            $word->delete();
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
