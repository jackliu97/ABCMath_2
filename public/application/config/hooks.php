<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/


//hook to initialize external configurations.
$hook['pre_system'][] = array(
                                'class'    => 'Bootstrap',
                                'function' => 'init_external_config',
                                'filename' => 'Bootstrap.php',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );


//initialize autoloader.
$hook['pre_controller'][] = array(
                                'class'    => 'Bootstrap',
                                'function' => 'init_autoloader',
                                'filename' => 'Bootstrap.php',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );


//hook to initialize user admin/permission.
$hook['pre_controller'][] = array(
                                'class'    => 'Bootstrap',
                                'function' => 'init_users',
                                'filename' => 'Bootstrap.php',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */