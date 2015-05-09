<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use \ABCMath\Grouping\KeywordManager;
use \ABCMath\ReadingComprehension\ReadingComprehensionExam;
use \ABCMath\Template\Template;

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

    public function reading_comprehension($id_list, $format='rtf'){

        $id_array = explode('_', $id_list);
        $exam = ReadingComprehensionExam::getExamById($id_array);


        $formattedExam = $this->_template->render(
            'Exam/Format/reading_comprehension.rtf.twig',
            array('exams'=>$exam)
            );

        $this->load->helper('download');
        $filename = 'reading_comprehension_' . implode('_', $id_array) . '.rtf';

        force_download($filename, $formattedExam);



    }
}
