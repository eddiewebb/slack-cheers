<?php

/*
* THis implementation of a 'model' is specific to RedBean ORM.
* http://www.redbeanphp.com/index.php?p=/models
*/


define( 'REDBEAN_MODEL_PREFIX', '' );

spl_autoload_register(function ($class_name) {
	$modelPath = dirname(__FILE__).'/models/' .$class_name . '.php';
	$servicePath = dirname(__FILE__).'/services/' .$class_name . '.php';
	if (file_exists($modelPath)) { 
		require_once $modelPath;
		return true; 
	} else if (file_exists($servicePath)) { 
		require_once $servicePath;
		return true; 
	} 
      return false; 
    
});
