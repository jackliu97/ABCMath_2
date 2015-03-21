<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
use \ABCMath\Entity\EntityManager;
use \ABCMath\Entity\Entity;
use \ABCMath\Entity\EntityField;
use \ABCMath\Db\Datatable;
use \ABCMath\Course\ABCClassManager;
use ABCMath\Student\StudentRegister;

class register extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->message = array();
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('public_navbar');
        $class_manager = new ABCClassManager();
        $data['all_courses'] = $class_manager->getClassForRegistration('options');

        $this->load->view('register/register', $data);
        $this->load->view('footer', array(
                                        'private_js' => array('register/register.js')
                                        ));
    }

    public function save()
    {
        $data = array(
            'success' => false,
            'message' => '',
            'missing' => array()
            );
        $fields = array(
            'first_name'=>'required-text',
            'middle_name'=>'text',
            'last_name'=>'required-text',
            'dob_month'=>'required-text',
            'dob_day'=>'required-text',
            'dob_year'=>'required-text',
            'gender'=>'required-text',
            'address1'=>'required-text',
            'address2'=>'text',
            'city'=>'required-text',
            'state'=>'required-text',
            'zip'=>'required-integer',
            'telephone'=>'required-telephone',
            'telephone2'=>'telephone',
            'cellphone'=>'telephone',
            'email'=>'required-email',
            'email2'=>'email',
            'school'=>'text',
            'gpa'=>'text',
            'grade'=>'required-text',
            'pickup_method'=>'text',
            'other_pickup_method'=>'text',
            'first_class'=>'text',
            'second_class'=>'text',
            'third_class'=>'text',
            );
        $missing = array();

        $inputs = $this->input->post(null, true);
        $missing = $this->validate_inputs($inputs, $fields);

        if(count($missing)){
            $data['missing'] = $missing;
            $data['message'] = implode('<br />', $this->message);
            $this->load->view('response/json', array('json' => $data));
            return false;
        }

        $result = $this->load_student( $inputs );

        $data['success'] = $result['success'];
        $data['message'] = $result['message'] ? $result['message'] : 
            "You have successfully registered! <br/>" . 
            "A verification has been sent to your primary email, <b>{$inputs['email']}</b>";
        $this->load->view('response/json', array('json' => $data));

    }

    private function load_student( $inputs ){

        //set dob
        $mm = $inputs['dob_month'];
        $dd = $inputs['dob_day'];
        $yyyy = $inputs['dob_year'];
        
        $inputs['dob'] = DateTime::createFromFormat(
            'n-j-Y G:i:s',
            "{$mm}-{$dd}-{$yyyy} 00:00:00")->format('Y-m-d');

        $now = new DateTime();
        $inputs['registration_date'] = $now->format('Y-m-d');

        $inputs['pickup_method'] = empty($inputs['pickup_method']) ? $inputs['other_pickup_method'] : $inputs['pickup_method'];
        unset($inputs['dob_month']);
        unset($inputs['dob_day']);
        unset($inputs['dob_year']);
        unset($inputs['other_pickup_method']);

        $student = new StudentRegister();

        foreach($inputs as $key=>$value){
            $student->{$key} = $value;
        }

        return $student->register();
    }


    private function validate_inputs( array $inputs, array $fields ){
        $missing = array();

        foreach($fields as $key=>$rule){
            $value = trim(array_get($inputs, $key, ''));
            if(stripos($rule, 'required') !== false && $value === ''){
                $missing []= $key;
            }
        }

        if(count($missing)){
            $this->message []= 'Please fill out all required fields. (they are marked with red borders)';
        }

        //validate dob
        $mm = intval($inputs['dob_month']);
        $dd = intval($inputs['dob_day']);
        $yyyy = intval($inputs['dob_year']);

        if(!checkdate($mm, $dd, $yyyy)){
            $missing []= 'dob_month';
            $missing []= 'dob_day';
            $missing []= 'dob_year';
            $this->message []= 'The date of birth you selected is invalid.';
        }

        //at least one class must be picked.
        if(empty($inputs['first_class']) && empty($inputs['second_class']) && empty($inputs['third_class'])){
            $missing []= 'first_class';
            $missing []= 'second_class';
            $missing []= 'third_class';
            $this->message []= 'You must select at least one class to register.';
        }

        //transportation must be specified.
        if(empty($inputs['pickup_method']) && empty(trim($inputs['other_pickup_method']))){
            $missing []= 'pickup_method';
            $missing []= 'other_pickup_method';
            $this->message []= 'You must let us know how this student is going home.';
        }


        return $missing;
    }
}