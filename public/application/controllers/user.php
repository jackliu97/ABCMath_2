<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class User extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->session->set_userdata('section', 'user');


	}

	public function index(){
		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('footer');
	}

	public function create_user($id=''){
		$this->session->set_userdata('sub_section', __FUNCTION__);

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('user/create_user');
		$this->load->view('footer', array(
										'private_js'=> array('user/base.js')
										));
	}

	public function create_group(){
		$this->session->set_userdata('sub_section', __FUNCTION__);

		$data = array();
		$data['permissions'] = $this->Permission_Model->get_all_permissions();

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('user/create_group', $data);
		$this->load->view('footer', array(
										'private_js'=> array('user/base.js')
										));
	}

	public function add_new(){

		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$password_re = $this->input->post('password_re');
		$return = array();

	}

	public function all_user(){
		$this->session->set_userdata('sub_section', __FUNCTION__);
		
		$data = array();
		$this->load->view('header');
		$this->load->view('navbar');

		$data['users'] = $this->User_Model->get_all();

		$this->load->view('user/all_user', $data);
		$this->load->view('footer', array(
										'private_js'=> array('user/base.js')
										));
	}

	public function all_groups(){
		$this->session->set_userdata('sub_section', __FUNCTION__);

		$data = array();
		$data['groups'] = $this->Permission_Model->get_all_groups();
		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('user/all_groups', $data);
		$this->load->view('footer', array(
										'private_js'=> array('user/base.js')
										));

	}

	public function edit_group($group_id){
		$data = array(
			'group_id' => $group_id
			);
		try{
			$data['group'] = Sentry::findGroupById($group_id);
			$data['permissions'] = $this->Permission_Model->get_all_permissions();
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e){
			$data['group'] = array();
		}

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('user/edit_group', $data);
		$this->load->view('footer', array(
										'private_js'=> array('user/base.js')
										));
	}

	public function edit_user_group($user_id){
		$user = Sentry::findUserByID($user_id);
		$data = array(
			'user_id' => $user_id
			);

		$data['groups'] = $this->Permission_Model->get_all_groups();
		$data['existing_group'] = $this->Permission_Model->get_user_groups($user);

		$this->load->view('header');
		$this->load->view('navbar');
		$this->load->view('user/edit_user_group', $data);
		$this->load->view('footer', array(
										'private_js'=> array('user/base.js')
										));
	}

	public function save_group(){
		$name = $this->input->post('name');
		$permission = $this->input->post('permission');
		$group_id = $this->input->post('group_id');

		if(!$group_id){
			$return = $this->Permission_Model->create_group($name, $permission);
		}else{
			$return = $this->Permission_Model->update_group($name, $permission, $group_id);
		}

		$this->load->view('response/json', array('json'=>$return));
	}

	public function delete_user(){
		$user_id = $this->input->post('user_id');

		$return = $this->User_Model->delete_user($user_id);

		$this->load->view('response/json', array('json'=>$return));
	}

	public function delete_group(){
		$group_id = $this->input->post('group_id');

		$return = $this->Permission_Model->delete_group($group_id);

		$this->load->view('response/json', array('json'=>$return));
	}


	public function save(){
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$password_re = $this->input->post('password_re');
		
		$return = $this->User_Model->add_new($email, $password, $password_re);
		$this->load->view('response/json', array('json'=>$return));
		return true;

	}

	public function save_user_permissions(){
		$user_id = $this->input->post('user_id');
		$permission = $this->input->post('permission');

		try{
			$this->User_Model->update_user_groups($user_id, $permission);
			$return = array('success'=>true, 'id'=>$user_id);

		}catch(Exception $e){

			$return['success'] = false;
			$return['message'] = $e->getMessage();

		}
		$this->load->view('response/json', array('json'=>$return));
		return true;

	}


	public function delete(){
		$question_id = $this->input->post('questionid');
		$this->ReadingComprehension_Model->delete((int)$question_id);
		$return = array('success'=>true, 'removed_id'=>$question_id);
		$this->load->view('response/json', array('json'=>$return));
		return true;
	}

}