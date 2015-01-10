<?php
use Illuminate\Database\Capsule\Manager as Capsule;

class Bootstrap{

	public function init_external_config(){
		require_once(__DIR__.
				DIRECTORY_SEPARATOR.'..'.
				DIRECTORY_SEPARATOR.'..'.
				DIRECTORY_SEPARATOR.'..'.
				DIRECTORY_SEPARATOR.'config'.
				DIRECTORY_SEPARATOR.'config.php');
	}

	public function init_autoloader(){
		require_once(__DIR__.
				DIRECTORY_SEPARATOR.'..'.
				DIRECTORY_SEPARATOR.'..'.
				DIRECTORY_SEPARATOR.'..'.
				DIRECTORY_SEPARATOR.'lib'.
				DIRECTORY_SEPARATOR.'Autoload.php');
	}

	public function init_users(){
		// Create the Sentry alias
		class_alias('Cartalyst\Sentry\Facades\Native\Sentry', 'Sentry');

		// Create a new Database connection
		$capsule = new Capsule;

		$capsule->addConnection([
		    'driver'    => 'mysql',
		    'host'      => DB_HOSTNAME,
		    'database'  => DB_DATABASE,
		    'username'  => DB_USERNAME,
		    'password'  => DB_PASSWORD,
		    'charset'   => 'utf8',
		    'collation' => 'utf8_unicode_ci',
		]);

		$capsule->bootEloquent();
	}

}