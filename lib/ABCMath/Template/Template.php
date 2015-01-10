<?php
namespace ABCMath\Template;

use \Twig_Autoloader;

/**
* Twig wrapper.
*
*
*/

class Template {

	protected $_envOptions;
	protected $_environment;
	public $loader;
	public $templateDir;
	protected $test;

	const FILESYSTEM = 1;

	public function __construct($type=NULL, $test=false){


		//this is where all the twig templates will be stored.
		$templateMainDir = __DIR__ . 
				DIRECTORY_SEPARATOR . 
				'TwigTemplates' . 
				DIRECTORY_SEPARATOR;

		//if this is a test, we use the test folder
		//else we store cache in the templates folder.
		if($test === true){
			$cacheMainDir = __DIR__ . 
				'..' . DIRECTORY_SEPARATOR . 
				'..' . DIRECTORY_SEPARATOR . 
				'Test' . 
				DIRECTORY_SEPARATOR . 'Cache';
		}else{
			$cacheMainDir = '/tmp/twig_cache';
		}


		//twig env options
		$this->_envOptions = array(
			'debug' => false,
			'charset' => 'utf-8'
			//'cache' => $cacheMainDir,
			//'strict_variables' => true
		);

		$this->templateDirs = array(
			$templateMainDir
			);


		//here we can control what type of filesystem we want.
		if($type === self::FILESYSTEM){
			$this->loader = new \Twig_Loader_Filesystem($this->templateDirs);
		}else{
			$this->loader = new \Twig_Loader_String();
		}

		$this->_environment = new \Twig_Environment(
			$this->loader,
			$this->_envOptions);
	}

	public function render($template, array $variables){
		return $this->_environment->render($template, $variables);
	}

}