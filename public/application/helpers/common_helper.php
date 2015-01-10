<?php

if ( ! function_exists('convert_smart_quotes'))
{
	function convert_smart_quotes($string) 
	{ 
    	return iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    }
}

if ( ! function_exists('print_array'))
{
	function print_array($array = array())
	{
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}

if ( ! function_exists('check_mark_icon'))
{
	function check_mark_icon($show)
	{
		if($show == '1'){
			return '<i class="icon-ok"></i>';
		}
		return '<i class="icon-remove"></i>';
	}
}

if ( ! function_exists('print_s'))
{
	function print_s($string = '')
	{
		echo '<pre>';
		print($string);
		echo '</pre>';
	}
}

if ( ! function_exists('format_date'))
{
	/**
	* inputs date (yyyy-mm-dd), outputs in given format.
	*/
	function format_date($in_date)
	{
		try {
			$date = new DateTime($in_date);
			return $date->format('M d, Y');
		} catch (Exception $e) {
			return false;
		}
	}
}

if ( ! function_exists('get_error_message'))
{
	function get_error_message($code)
	{
		switch($code){
			case 'external_id_exists' :
				return 'External ID already exists.';
			break;
			
			case 'description_exists' :
				return 'Description already exists.';
			break;
			
			case 'description_blank' :
				return 'Description is blank.';
			break;
			
			case 'class_code_blank' :
				return 'Class Code is blank.';
			break;
			
			case 'student_id_not_exist' :
				return 'Student ID does not exist.';
			break;
			
			default:
				return $code;
		}
		
		return '';
	}
}

if ( ! function_exists('parse_error'))
{
	function parse_error($errors){
		if($errors === false)
			return '';
			
		
		$error_msg = array();
		foreach($errors as $error){
			$error_msg []= get_error_message($error);
		}
		
		return implode('<br />', $error_msg);
	}
}

if ( ! function_exists('logged_in'))
{
	function logged_in($session){
	
		if(array_key_exists('user_type', $session) && $session['user_type'] === 'admin'){
			return true;
		}
		
		return false;
	
	}
}

if ( ! function_exists('admin_check'))
{
	function admin_check($session){
	
		if(array_key_exists('user_type', $session) && $session['user_type'] === 'admin'){
			return true;
		}
		
		return false;
	
	}
}

if ( ! function_exists('semester_dropdown_options'))
{
	function semester_dropdown_options(){
		return array(
				''			=> 'Select',
				'Summer'	=> 'Summer',
				'Spring'	=> 'Spring',
				'Fall'		=> 'Fall');
	}
}

if ( ! function_exists('predict_semister'))
{
	function predict_semister(){
		$month = (int)date('m');
				
		if(in_array($month, array(3, 4, 5))){
			return 'Summer';
		}
		
		if(in_array($month, array(6, 7, 8, 9))){
			return 'Fall';
		}
		
		if(in_array($month, array(10, 11, 12, 1))){
			return 'Spring';
		}
		
		return '';
	}
}

if ( ! function_exists('days_of_week'))
{
	function days_of_week()
	{
		return array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
	}
}

if ( ! function_exists('year_dropdown_options'))
{
	function year_dropdown_options(){
		$current_year = (int)date('Y');
		return array(
			$current_year+1	=> $current_year+1,
			$current_year	=> $current_year,
			$current_year-1	=> $current_year-1,
			$current_year-2	=> $current_year-2,
			$current_year-3 => $current_year-3);
	}
}

if ( ! function_exists('time_dropdown_options'))
{
	function time_dropdown_options($start, $end){
	
		$time = array();
		for($i=(int)$start; $i<=(int)$end; $i++){
			$t = DateTime::createFromFormat('G', $i);
			$time[date_format($t, 'H:i:s')] = date_format($t, 'g:i:s A');
		}
		
		return $time;
	}
}

if ( ! function_exists('date_fields'))
{
	function date_fields(){
		return array('dob', 'registration_date');
	}
}

if ( ! function_exists('time_fields'))
{
	function time_fields(){
		return array('start_time', 'end_time');
	}
}

if ( ! function_exists('all_payment_types'))
{
	function all_payment_types(){
		return array('cash', 'check');
	}
}

if ( ! function_exists('valid_payment_type'))
{
	function valid_payment_type($type){
		return in_array($type, all_payment_types());
	}
}

if ( ! function_exists('dollar_format'))
{
	function dollar_format($param){
		return '$' . number_format($param, 2);
	}
}

if ( ! function_exists('payment_types'))
{
	function payment_types(){
		return array('cash'=>'cash', 'check'=>'check');
	}
}

if ( ! function_exists('good_image_extensions'))
{
	function good_image_extensions(){
		return array('jpg', 'jpeg', 'gif', 'png');
	}
}

if ( ! function_exists('validate_image'))
{
	function validate_image($file){
		$org_name	= $file['name'];
		$type		= $file['type'];
		$extension	= array_pop(explode('.', $org_name));
		
		if(in_array($extension, good_image_extensions())){
			return true;
		}
		
		return false;
	}
}