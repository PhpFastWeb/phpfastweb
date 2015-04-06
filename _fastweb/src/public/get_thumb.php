<?php
  
  die;
  
  require_once(dirname(__FILE__)."/../functions.inc.php");
  require_once(dirname(__FILE__)."/../url.class.php");  
  require_once(dirname(__FILE__)."/../website.class.php");
  require_once(dirname(__FILE__)."/../image.class.php");

  
  $img = new image();
  $img->filename = url::read('file');
  
  //Obtenemos el directorio base del portal
  $base_dir = clean_path(dirname(__FILE__).'/../..');
  
  //Si no contiene "..", sistema nuevo (el bueno)
  if ( false === strpos( url::read('dir') , ".." )) {

  	$dir = url::read('dir');
	//$dir =  $base_dir.$dir;
  
  }	else {
  	die("**Error: directorio no debe contener '..'");
    //Si contiene "..", sistema antiguo, parche
  	//$dir = str_ireplace('..','',url::read('dir'));
  	//$dir = dirname(__FILE__).'/../../aljarafe'.$dir;
  
  }
  
  
  $img->dir 			 = $dir;
  $img->thumb_max_height = url::read('thumb_max_height');
  $img->thumb_max_width  = url::read('thumb_max_width');
  $img->thumb_cache_dir_sufix = url::read('thumb_cache_dir_sufix');
  /*
  echo $img->dir.$img->filename."<br />";
  echo is_file($img->dir.$img->filename);
  die;
  */
  
  $img->send_thumbnail();
