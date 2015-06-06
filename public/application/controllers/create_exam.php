<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\Grouping\KeywordManager;
use \ABCMath\ReadingComprehension\ReadingComprehensionExam;
use \ABCMath\ReadingComprehension\ReadingComprehensionList;
use \ABCMath\Template\Template;
use \ABCMath\File\FileManager;

class Create_Exam extends CI_Controller
{

    protected $_template;

    public function __construct()
    {
        parent::__construct();

        if (($this->User_Model->check_login() === false) || 
            ($this->User_Model->in_group('Administrator') === false)) {
            $this->session->sess_destroy();
            header('Location: /login');
        }

        $this->keywordManager = new KeywordManager();
        $this->session->set_userdata('section', 'create_exam');
        $this->_template = new Template(Template::FILESYSTEM);
        ABCMath\Permission\Navigation::$config['quicklink'] = false;
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('navbar');
        $this->load->view('exam/create');
        $this->load->view('footer', array(
                                        'private_js' => array('exam/base.js'),
                                        ));
    }

    public function scrambled_paragraph(){
        $id_list = $this->input->post('id_list');
        $format = $this->input->post('format');
        $return = array('success' => true);

        $formattedExam = '';
        $templatePath = 'Exam/Format/reading_comprehension.txt.twig';

    }

    public function reading_comprehension(){

        $id_list = $this->input->post('id_list');
        $format = $this->input->post('format');
        $return = array('success' => true);

        $formattedExam = '';
        $templatePath = 'Exam/Format/reading_comprehension.txt.twig';

        if(empty(trim($id_list))){
            
            $list = new ReadingComprehensionList();
            $listing = $list->fetchAll();

            do{
                
                $exam = ReadingComprehensionExam::getExamById($listing);
                $formattedExam .= $this->_template->render($templatePath, array('exams'=>$exam));
                $listing = $list->nextPage()->fetchAll();

            }while (!empty($listing));

            $exam_questions = 'all';

        }else{

            $id_array = explode('_', $id_list);
            $exam = ReadingComprehensionExam::getExamById($id_array);
            $formattedExam = $this->_template->render($templatePath, array('exams'=>$exam));
            $exam_questions = implode('_', $id_array);
        }

        $filePath = './files/downloads/reading_comprehension_' . 
                        $exam_questions . '_' . date('Y_m_d_h_i_s') . ".{$format}";
        $this->load->helper('file');
        if(!write_file($filePath, $formattedExam)){

            $return['success'] = false;
            $return['message'] = 'Failed to write file.';

        }else{

            $fm = new FileManager();
            $fm->set($filePath);
            $id = $fm->save();
            $return['file_id'] = $id;

        }

        $this->load->view('response/json', array('json' => $return));
    }
}
