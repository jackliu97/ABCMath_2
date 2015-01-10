<?php
namespace ABCMath\Db;

use \Doctrine\DBAL\Configuration,
	\Doctrine\DBAL\DriverManager;

class Connection{

	protected static $_config = NULL;
	protected static $_conn = NULL;

	public static function getConnection(){
		if(!is_null(self::$_conn)){
			return self::$_conn;
		}

		if(is_null(self::$_config)){
			self::$_config = new Configuration();
			$connectionParams = array(
				'dbname' 	=> DB_DATABASE,
				'user' 		=> DB_USERNAME,
				'password' 	=> DB_PASSWORD,
				'host' 		=> DB_HOSTNAME,
				'driver' 	=> 'pdo_mysql',
			);
		}
		self::$_conn = DriverManager::getConnection($connectionParams, self::$_config);
		return self::$_conn;
	}


}

?>