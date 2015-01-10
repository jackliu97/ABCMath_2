<?php
use \ABCMath\Db\Connection;

class Permission_Model extends CI_Model{

	public $id;
	public $name;

	protected $_conn;

	const ADMIN = 1;

	public function __construct(){
		parent::__construct();
		$this->_conn = Connection::getConnection();
	}

	public function check_permission($permission_id){
		$session_data = $this->session->all_userdata();
		if(in_array(1, $session_data['user_permission'])){
			return true;
		}

		return in_array($permission_id, $session_data['user_permission']);
	}

	public function is_admin($permissions){
		return in_array(self::ADMIN, $permissions);
	}

	public function create_group($name, $permissions){
		$message = '';
		$success = false;
		$permission_param = array();

		foreach($permissions as $rule){
			if(!$rule['name']){
				continue;
			}

			$permission_param[$rule['name']] = $rule['permission'];
		}

		try{
			$group = Sentry::createGroup(array(
				'name'        => $name,
				'permissions' => $permission_param,
			));

			$message = 'Group added successfully';
			$success = true;
		}
		
		catch (Cartalyst\Sentry\Groups\NameRequiredException $e){
			$message = 'Name field is required';
		}

		catch (Cartalyst\Sentry\Groups\GroupExistsException $e){
			$message = 'Group already exists';
		}

		return array('success'=>$success, 'message'=>$message);
	}

	public function update_group($name, $permissions, $group_id){
		$message = '';
		$success = false;
		$permission_param = array();

		foreach($permissions as $rule){
			if(!$rule['name']){
				continue;
			}

			$permission_param[$rule['name']] = $rule['permission'];
		}

		try{
			$group = Sentry::findGroupById($group_id);
			$group->name = $name;
			$group->permissions = $permission_param;

			if($group->save()){
				$message = 'Group updated successfully.';
				$success = true;
			}else{
				$message = 'Group information not updated.';
			}
		}
		
		catch (Cartalyst\Sentry\Groups\NameRequiredException $e){
			$message = 'Name field is required';
		}

		catch (Cartalyst\Sentry\Groups\GroupExistsException $e){
			$message = 'Group already exists';
		}

		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e){
			$message = 'Group was not found.';
		}

		return array('success'=>$success, 'message'=>$message);
	}

	public function delete_group($group_id){
		$message = '';
		$success = false;

		try{
			$group = Sentry::findGroupById($group_id);
			$group->delete();
			$message = 'Group successfully deleted.';
			$success = true;
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e){
			$message = 'Group was not found.';
		}

		return array('success'=>$success, 'message'=>$message);
	}


	public function get_user_groups($user, $id_only=false){
		$groups = $user->getGroups();
		$return = array();

		if(count($groups)){
			foreach($groups as $group){
				if($id_only){
					$return[]= $group->id;
				}else{
					$return[$group->id]= $group->name;
				}
			}
		}

		return $return;
	}


	public function get_all_permissions(){

		$qb = $this->_conn->createQueryBuilder();
		$qb->select('name', 'rule')
			->from('permissions', 'p');
		$data = $qb->execute()->fetchAll();


		return $data;
	}

	public function get_all_groups(){
		$groups = Sentry::findAllGroups();

		foreach($groups as $group){
			$return [$group->id]= $group->name;
		}

		return $return;
	}

}