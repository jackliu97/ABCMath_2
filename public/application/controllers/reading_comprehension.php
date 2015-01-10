<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\ReadingComprehension\ReadingComprehension;
use \ABCMath\ReadingComprehension\Question;
use \ABCMath\Grouping\Keyword;
use \ABCMath\Grouping\KeywordManager;
use \ABCMath\Db\Datatable;

class Reading_Comprehension extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->User_Model->check_permission(
            array(
                'reading_comprehension.view',
                'reading_comprehension.edit', )
            ) == false) {
            header('Location: /landing');
        }

        $this->session->set_userdata('section', 'reading_comprehension');

        $this->keywordManager = new KeywordManager();
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('footer');
    }

    public function create($id = '')
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array(
            'reading_comprehension_id' => $id,
            );

        if ($id != '') {
            $reading = new ReadingComprehension($id);
            $reading->load();
            $data['reading_comprehension'] = $reading;
        }

        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('keyword_form', array(
                            'existing_keyword' => $this->keywordManager->getKeywordByQuestion('reading_comprehension', $id),
                            'keyword' => $this->keywordManager->allFormatted(),
                            ));

        $this->load->view('reading_comprehension/create', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('reading_comprehension/base.js', 'keyword_form.js'),
                                        ));
    }

    public function list_all()
    {
        $this->session->set_userdata('sub_section', __FUNCTION__);

        $data = array();
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('reading_comprehension/list_all');
        $this->load->view('footer', array(
                                        'private_js' => array('reading_comprehension/list_all.js'),
                                        'datatable' => true,
                                        ));
    }

    public function list_action()
    {
        $result = array('success' => false, 'message' => '');
        $list = new ReadingComprehension();
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

    public function get_passage_detail()
    {
        $result = array('success' => false, 'message' => '');
        $paragraph_id = $this->input->post('paragraph_id');

        try {
            $reading_comprehension = new ReadingComprehension();
            $reading_comprehension->id = $paragraph_id;
            $reading_comprehension->load();
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->load->view('response/json', array('json' => $result));

            return;
        }

        $result['full_text'] = $reading_comprehension->full_text;
        $result['success'] = true;
        $this->load->view('response/json', array('json' => $result));
    }

    public function save()
    {
        $reading_comprehension = new ReadingComprehension();
        $data = array();
        $data['id'] = $this->input->post('reading_comprehension_id');
        $data['full_text'] = $this->input->post('paragraph');
        $data['lines'] = $this->input->post('lines');
        $data['questions'] = array();
        $questions = $this->input->post('questions');
        $keywords = $this->input->post('keyword');

        if (count($questions)) {
            foreach ($questions as $question) {
                $q_data = array();
                $q_data['id'] = isset($question['id']) ? $question['id'] : '';
                $q_data['reading_comprehension_id'] = $data['id'];
                $q_data['text'] = $question['question'];
                $q_data['original_text'] = $question['original_text'];
                $q_data['choices'] = array();

                if (count($question['choice'])) {
                    foreach ($question['choice'] as $k => $choice) {
                        $c_data = array();
                        $c_data['text'] = $choice;
                        if ($k == $question['is_answer']) {
                            $c_data['is_answer'] = '1';
                        } else {
                            $c_data['is_answer'] = '0';
                        }
                        $q_data['choices'][] = $c_data;
                    }
                }

                $data['questions'][] = $q_data;
            }
        }

        $reading_comprehension->load($data);
        $result = $reading_comprehension->save();

        /*
        * Keywords Logic.
        */

        if ($keywords !== false) {
            $this->keywordManager->load($keywords);
            $this->keywordManager->bind('reading_comprehension', $reading_comprehension->id);
        }

        if ($result['success']) {
            $result['success'] = true;
            $result['reading_comprehension_id'] = $reading_comprehension->id;
            $this->load->view('response/json', array('json' => $result));
        } else {
            $result['success'] = false;
            $result['message'] = 'Save failed!';
            $this->load->view('response/json', array('json' => $result));
        }

        return true;
    }

    public function delete()
    {
        $question_id = $this->input->post('questionid');

        $reading_comprehension = new ReadingComprehension();
        $reading_comprehension->id = $question_id;
        $reading_comprehension->delete();

        $return = array('success' => true, 'removed_id' => $question_id);
        $this->load->view('response/json', array('json' => $return));

        return true;
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
            $this->Exam_Model->table = 'reading_comprehension';
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
}
