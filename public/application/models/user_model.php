<?php

class User_Model extends CI_Model{

	public function authenticate($email, $password, $remember=false){
		$success = false;
		$message = '';
		$credentials = array(
			'email'    => $email,
			'password' => $password,
		);

		try{
			$user = Sentry::authenticate($credentials, $remember);
			$success = true;
		}

		catch (Cartalyst\Sentry\Users\LoginRequiredException $e){
			$message = 'Login field is required.';
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e){
			$message = 'Password field is required.';
		}
		catch (Cartalyst\Sentry\Users\WrongPasswordException $e){
			$message = 'Wrong password, try again.';
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e){
			$message = 'User was not found.';
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e){
			$message = 'User is not activated.';
		}

		return array('success'=>$success, 'message'=>$message);
	}

	public function logout(){
		Sentry::logout();
	}

	public function update_user_groups($user_id, $permission){
		$message = '';
		$success = false;

		$user = Sentry::findUserByID($user_id);
		$existing_permission = $this->Permission_Model->get_user_groups($user, true);

		$id_to_remove = array_diff($existing_permission, $permission);
		$id_to_add = array_diff($permission, $existing_permission);

		if(count($id_to_remove)){
			foreach($id_to_remove as $id){
				$group = Sentry::findGroupById($id);
				$user->removeGroup($group);
			}
		}

		if(count($id_to_add)){
			foreach($id_to_add as $id){
				$group = Sentry::findGroupById($id);
				$user->addGroup($group);
			}
		}
	}

	public function add_new($email, $password, $password_re, $activate=true){
		$message = '';
		$success = false;

		if($password != $password_re){
			$message = 'Passwords do not match.';
			return array('success'=>$success, 'message'=>$message);
		}

		try{

			$user = Sentry::createUser(array(
				'email'     => $email,
				'password'  => $password,
				'activated' => $activate,
			));
			$message = 'User added successfully';
			$success = true;

		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e){
			$message = 'Login field is required.';
		}

		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e){
			$message = 'Password field is required.';
		}

		catch (Cartalyst\Sentry\Users\UserExistsException $e){
			$message = 'User with this login already exists.';
		}

		catch (Cartalyst\Sentry\Users\GroupNotFoundException $e){
			$message = 'Group was not found.';
		}

		return array('success'=>$success, 'message'=>$message);

	}

	public function delete_user($user_id){
		$message = '';
		$success = false;

		try{
			$user = Sentry::findUserByID($user_id);
			$user->delete();
			$message = 'User successfully deleted.';
			$success = true;
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e){
			$message = 'User was not found.';
		}

		return array('success'=>$success, 'message'=>$message);
	}

	public function get_all(){
		$users = Sentry::findAllUsers();
		$return = array();

		if(count($users)){
			foreach($users as $user){
				$return [$user->id]= $user->email;
			}
		}

		return $return;
	}

	public function get_groups(){
		try {
			// Get the current active/logged in user
			$user = Sentry::getUser();
			$groups = $user->getGroups();
			$return = array();

			if(count($groups)){
				foreach($groups as $group){
					$return []= $group->name;
				}
			}

			return $return;
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e){
			return false;
		}
	}

	public function check_permission($permissions){

		if(!$this->check_login()){
			return false;
		}

		if(!is_array($permissions)){
			$permissions = array($permissions);
		}

		try {
			// Get the current active/logged in user
			$user = Sentry::getUser();

			if($user->hasAccess('admin')){
				return true;
			}

			return $user->hasAnyAccess($permissions);
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e){
			return false;
		}

	}

	public function check_login(){
		return Sentry::check();
	}

	public function get_user(){
		return Sentry::getUser();
	}

}