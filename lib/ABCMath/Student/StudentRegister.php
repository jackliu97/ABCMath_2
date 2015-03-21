<?php
namespace ABCMath\Student;

use ABCMath\Base;
use \Exception;
use \Swift_Attachment as Swift_Attachment;
use \Swift_MailTransport as Swift_MailTransport;
use \Swift_Mailer as Swift_Mailer;
use \Swift_Message as Swift_Message;

/**
 * This class registers a student.
 *
 */

class StudentRegister extends Base
{

    protected $_rawData;
    public function __construct()
    {
        parent::__construct();
    }

    public function __get($key)
    {
        if(empty($key)){
            return;
        }

        return isset($this->_rawData[$key]) ? $this->_rawData[$key] : null;
    }

    public function __set($key, $value)
    {
        if(empty($key)){
            return;
        }

        $this->_rawData[$key] = $value;
    }

    public function register(){
        $result = array(
            'success' => false,
            'message' => '');

        $student_data_format = array(
            'first_name',
            'last_name',
            'gender',
            'email',
            'email2',
            'dob',
            'telephone',
            'telephone2',
            'cellphone',
            'address1',
            'address2',
            'city',
            'state',
            'zip',
            'pickup_method',
            'registration_date');

        $registration_data_format = array(
            'first_class',
            'second_class',
            'third_class',
            'fourth_class',
            'school',
            'gpa',
            'grade'
            );

        $student_data = array();
        $registration_data = array();

        foreach($student_data_format as $key){
            $student_data[$key] = array_get($this->_rawData, $key);
        }

        foreach($registration_data_format as $key){
            $registration_data[$key] = array_get($this->_rawData, $key);
        }

        $this->_conn->beginTransaction();

        $external_id_result = $this->make_external_id();
        if($external_id_result['success'] === false){
            $this->_conn->rollback();
            return $external_id_result;
        }

        $student_data['external_id'] = $external_id_result['external_id'];


        $student_result = $this->insert_student($student_data);
        if($student_result['success'] === false){
            $this->_conn->rollback();
            return $student_result;
        }


        $registration_data['student_id'] = $student_result['student_id'];
        $registration_result = $this->insert_student_registration($registration_data);
        if($registration_result['success'] === false){
            $this->_conn->rollback();
            return $registration_result;
        }

        $emailResult = $this->email_confirmation();

        $this->_conn->commit();
        $result['success'] = true;
        return $result;
    }

    public function make_external_id(){
        try{
            $this->_conn->insert('increment', array());
        }catch (Exception $e){
            error_log($e->getMessage());

            return array(
                    'success'=>false,
                    'message'=>'An error occurred while attempting to save. ' . 
                        'Please contact front desk for further help.'
                );
        }

        return array(
                    'success'=>true,
                    'external_id'=> date('y') . $this->_conn->lastInsertId()
                );
    }

    public function insert_student( $student_data ){

        try{
            $this->_conn->insert('students', $student_data);
        }catch (Exception $e){
            error_log($e->getMessage());

            return array(
                    'success'=>false,
                    'message'=>'An error occurred while attempting to save. ' . 
                        'Please contact front desk for further help.'
                );
        }

        return array(
                    'success'=>true,
                    'student_id'=>$this->_conn->lastInsertId()
                );
    }

    public function insert_student_registration( $registration_data ){
        try{
            $this->_conn->insert('student_registration', $registration_data);

            return array(
                    'success'=>true,
                    'student_id'=>$this->_conn->lastInsertId()
                );

        }catch (Exception $e){
            error_log($e->getMessage());

            return array(
                    'success'=>false,
                    'message'=>'An error occurred while attempting to save. ' . 
                        'Please contact front desk for further help.'
                );
        }
    }

    public function make_email_body(){

        $body = 'Hi! ' . "\n" . 
            'This is to confirm that your registration ' . 
            "for {$this->_rawData['first_name']} {$this->_rawData['first_name']} has been " . 
            'successfully processed!';

        $body .= "\n\n";

        $body .= "If you have any further questions at all, " .
            'don\'t hesistate to contact us at 718-888-7866';

        return $body;
    }

    public function email_confirmation(){
        $date = date( 'F jS, Y g:i A' );
        $title = "ABCMath registration for {$this->_rawData['first_name']} on {$date}";

        $body = $this->make_email_body();

        $transport = Swift_MailTransport::newInstance();
        $mailer = Swift_Mailer::newInstance( $transport );

        $message = Swift_Message::newInstance( $title )
        ->setFrom( array( 'abcmathacademy@gmail.com' => 'ABCMath Academy' ) )
        ->setTo( array( $this->_rawData['email'] ) )
        ->setBody( $body )
        ;
        $result = $mailer->send( $message );
        return $result;

    }
}