<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\ScrambledParagraph\ScrambledParagraph;
use \ABCMath\ScrambledParagraph\ScrambledParagraphManager;
use \ABCMath\Grouping\Keyword;
use \ABCMath\Grouping\KeywordManager;
use \ABCMath\Db\Datatable;

class Scrambled_Paragraph extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->User_Model->check_permission(array(
                'scrambled_paragraph.view',
                'scrambled_paragraph.edit', )) == false) {
            header('Location: /landing');
        }

        $this->session->set_userdata('section', 'scrambled_paragraph');

        $this->keywordManager = new KeywordManager();
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('footer');
    }

    public function create_from_paragraph()
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array();

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('keyword_form', array(
            'keyword' => $this->keywordManager->allFormatted(),
        ));

        $this->load->view('scrambled_paragraph/create_from_paragraph', $data);
        $this->load->view('footer', array(
            'private_js' => array('scrambled_paragraph/base_paragraph.js', 'keyword_form.js'),
        ));
    }

    public function create_from_question($id = '')
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array(
            'scrambled_paragraph_id' => $id,
            );

        if ($id != '') {
            $paragraph = new ScrambledParagraph();
            $paragraph->id = $id;
            $paragraph->load();
            $data['paragraph_text'] = $this->_format_question($paragraph);
        }

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('keyword_form', array(
            'existing_keyword' => $this->keywordManager->getKeywordByQuestion(
                'scrambled_paragraph',
                $id
                ),
            'keyword' => $this->keywordManager->allFormatted(),
        ));

        $this->load->view('scrambled_paragraph/create_from_question', $data);
        $this->load->view('footer', array(
            'private_js' => array('scrambled_paragraph/base_question.js', 'keyword_form.js'),
            ));
    }

    private function _format_question($paragraph)
    {
        $text = array($paragraph->lines[0]['text']);

        foreach (array_slice($paragraph->lines, 1) as $line) {
            $text[$line['order_id']] = chr(64 + $line['order_id']).'. '.$line['text'];
        }
        ksort($text);

        return implode("\n", $text);
    }

    public function list_all()
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array();
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('scrambled_paragraph/list_all');
        $this->load->view('footer', array(
                                        'private_js' => array('scrambled_paragraph/list_all.js'),
                                        'datatable' => true,
                                        ));
    }

    public function list_action()
    {
        $result = array('success' => false, 'message' => '');
        $list = new ScrambledParagraphManager();
        $dt = new Datatable();
        $dt->sql = $list->allSQL();
        $dt->columns = array( 'id', 'keyword', 'full_text');
        $result = $dt->processQuery();

        if (count($result['aaData'])) {
            foreach ($result['aaData'] as $key => $row) {
                foreach ($row as $k => $col) {
                    if ($k == 0) {
                        $result['aaData'][$key][$k] = "<button paragraph_id='{$col}' ".
                            "class='btn edit_mode glyphicon glyphicon-pencil' style='width:45px;'></button>&nbsp;".
                            "<button paragraph_id='{$col}' ".
                            "class='btn more_info glyphicon glyphicon-chevron-down' style='width:45px;'></button>&nbsp;".
                            "<button paragraph_id='{$col}' ".
                            "class='btn remove glyphicon glyphicon-remove' style='width:45px;'></button>";
                    }
                }
            }
        }

        $this->load->view('response/datatable', array('json' => $result));
    }

    public function get_paragraph_detail()
    {
        $result = array('success' => false, 'message' => '');
        $paragraph_id = $this->input->post('paragraph_id');

        try {
            $paragraph = new ScrambledParagraph();
            $paragraph->id = $paragraph_id;
            $paragraph->load();
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $result['full_text'] = $paragraph->full_text;
        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function all()
    {
        if (!$this->Permission_Model->check_permission(1)) {
            header('Location: /');
        }

        $data = array();
        $paragraphManager = new ScrambledParagraphManager();
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('keyword_filter', array(
                            'keyword' => $this->keywordManager->allFormatted(),
                            ));

        $data['list'] = $paragraphManager->all();

        $this->load->view('scrambled_paragraph/list', $data);
        $this->load->view('footer', array(
            'private_js' => array('scrambled_paragraph/base.js', 'keyword_filter.js'),
            ));
    }

    public function save()
    {
        $keywords = $this->input->post('keyword');
        $paragraph = $this->input->post('paragraph');
        $type = $this->input->post('type');
        if($type === 'paragraph'){
            $pieces = ScrambledParagraph::createFromParagraph($paragraph);
        }else{
            $pieces = ScrambledParagraph::createFromQuestion($paragraph);
        }
        $id = $this->input->post('scrambled_paragraph_id');

        $sp = new ScrambledParagraph();

        $data = array();
        $data['id'] = $id;
        $data['full_text'] = $paragraph;
        $data['lines'] = array();

        if (count($pieces)) {
            foreach ($pieces as $order_id => $text) {
                $data['lines'][] = array(
                    'order_id' => $order_id,
                    'text' => $text,
                    );
            }
        }

        $sp->load($data);
        $result = $sp->save();

        /*
        * Keywords Logic.
        */

        $this->keywordManager->load($keywords);
        $this->keywordManager->bind('scrambled_paragraph', $sp->id);

        if ($result['success']) {
            $result['success'] = true;
            $result['scrambled_paragraph_id'] = $sp->id;
        } else {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        $this->load->view('response/json', array('json' => $result));
    }

    public function create_exam()
    {
        $question_id = $this->input->post('question_id');
        $option_start = $this->input->post('option_start');
        $exam_name = $this->input->post('exam_name');
        $result = array();

        try {
            $this->Exam_Model->name = $exam_name;
            $this->Exam_Model->question_id = $question_id;
            $this->Exam_Model->option_start = $option_start;
            $this->Exam_Model->table = 'scrambled_paragraph';
            $exam_id = $this->Exam_Model->create();
            $result['success'] = true;
            $result['exam_id'] = $exam_id;
            $this->load->view('response/json', array('json' => $result));
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));
        }
    }

    public function delete()
    {
        try {
            $paragraph = new ScrambledParagraph();
            $paragraph->id = $this->input->post('q_id');
            $paragraph->delete();
            $result['success'] = true;
            $result['removed_id'] = $paragraph->id;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }
        $this->load->view('response/json', array('json' => $result));
    }

    public function parse()
    {
        $result = array();
        $paragraph = $this->input->post('paragraph');
        $type = $this->input->post('type');
        $result['success'] = true;

        if($type === 'paragraph'){
            $result['pieces'] = ScrambledParagraph::createFromParagraph($paragraph);
        }else{
            $result['pieces'] = ScrambledParagraph::createFromQuestion($paragraph);
        }
        $this->load->view('response/json', array('json' => $result));
    }

}
