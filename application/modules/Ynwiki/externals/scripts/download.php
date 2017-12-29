<?php

###############################################################
# File Download 1.31
###############################################################
###############################################################
# Sample call:
#    download.php?f=phptutorial.zip
#
# Sample call (browser will try to save with new file name):
#    download.php?f=phptutorial.zip&fc=php123tutorial.zip
###############################################################

// Allow direct file download (hotlinking)?
// Empty - allow hotlinking
// If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
define('ALLOWED_REFERRER', '');

// Download folder, i.e. folder where you keep all files for download.
// MUST end with slash (i.e. "/" )
define('BASE_DIR','../../../../../public/ynwiki');
define('FILE_DIR','../../../../../');
// log downloads?  true/false
define('LOG_DOWNLOADS',true);

// log file name
define('LOG_FILE',FILE_DIR.'temporary/downdloads.log');

// log file name

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));






include APPLICATION_PATH . '/application/modules/Ynwiki/cli.php';

// Allowed extensions list in format 'extension' => 'mime type'
// If myme type is set to empty string then script will try to detect mime type 
// itself, which would only work if you have Mimetype or Fileinfo extensions
// installed on server.




$allowed_ext = array (

  // archives
  'zip' => 'application/zip',
  'rar' => 'application/rar',

  // documents
  'pdf' => 'application/pdf',
  'txt' => 'application/txt',
  'html' => 'application/html',
  'php' => 'application/php',
  'tpl' => 'application/tpl',
  'doc' => 'application/msword',
  'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'xls' => 'application/vnd.ms-excel',
  'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'ppt' => 'application/vnd.ms-powerpoint',
  'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
  
  // executables
  'exe' => 'application/octet-stream',

  // images
  'gif' => 'image/gif',
  'png' => 'image/png',
  'jpg' => 'image/jpeg',
  'jpeg' => 'image/jpeg',

  // audio
  'mp3' => 'audio/mpeg',
  'wav' => 'audio/x-wav',

  'xmind' => '',
  
  // video
  'mpeg' => 'video/mpeg',
  'mpg' => 'video/mpeg',
  'mpe' => 'video/mpeg',
  'mov' => 'video/quicktime',
  'avi' => 'video/x-msvideo',
  
  //widget
  'tar' => 'application/x-tar',
  'tgz' => 'application/x-compressed-tar',
  'tar.gz' => 'application/x-compressed-tar' 
  
);



####################################################################
###  DO NOT CHANGE BELOW
####################################################################

// If hotlinking not allowed then make hackers think there are some server problems
if (ALLOWED_REFERRER !== ''
&& (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),strtoupper(ALLOWED_REFERRER)) === false)
) {
  die("Internal server error. Please contact system administrator.");
}

// Make sure program execution doesn't time out
// Set maximum script execution time in seconds (0 means no limit)
set_time_limit(0);

if (!isset($_GET['f']) || empty($_GET['f'])) {
  die("Please specify file name for download.");
}


if (!isset($_GET['fi']) || empty($_GET['fi'])) {
	die("The page you have attempted to access could not be found..");
}
else{
	
	
	
	$viewer = Engine_Api::_()->user()->getViewer();
	if($viewer->getIdentity() == 0) die;
	
	
	$page = Engine_Api::_()->getItem('ynwiki_page', $_GET['fi']);
	
	$arrFile = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'ynwiki_page', 'auth_file');
	$arrFile = explode(',',$arrFile);
	foreach ($arrFile as $key => $file_name) {
		$arrFile[$key] = trim($file_name);		
	}
	
	if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($page, $viewer, 'view')->checkRequire()) die;
	
	$i = 1;
	while($page->parent_page_id != 0 && $i <= 20) {		
		if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($page->getParentPage(), $viewer, 'view')->checkRequire()) die;		
		$page = $page->getParentPage();
		$i++;
	}


	
	
}



// Nullbyte hack fix
if (strpos($_GET['f'], "\0") !== FALSE) die('');

// Get real file name.
// Remove any path info to avoid hacking by adding relative path, etc.
$fname = basename($_GET['f']);
// Check if the file exists
// Check in subfolders too
 // find_file

// get full file path (including subfolders)
// $file_path = '';
// find_file(BASE_DIR, $fname, $file_path);

// if (!is_file($file_path)) {
//   die("File does not exist. Make sure you specified correct file name."); 
// }

$file_path =    FILE_DIR. $_GET['f'];

// file size in bytes
$fsize = filesize($file_path); 

// file extension
$fext = strtolower(substr(strrchr($fname,"."),1));

// check if allowed extension

if(!in_array($fext, $arrFile)){
	die("Not allowed file type.");
}
// get mime type
if ($allowed_ext[$fext] == '') {
  $mtype = '';
  // mime type is not set, get from server settings
  if (function_exists('mime_content_type')) {
    $mtype = mime_content_type($file_path);
  }
  else if (function_exists('finfo_file')) {
    $finfo = finfo_open(FILEINFO_MIME); // return mime type
    $mtype = finfo_file($finfo, $file_path);
    finfo_close($finfo);  
  }
  if ($mtype == '') {
    $mtype = "application/force-download";
  }
}
else {
  // get mime type defined by admin
  $mtype = $allowed_ext[$fext];
}

// Browser will try to save file with this filename, regardless original filename.
// You can override it if needed.

if (!isset($_GET['fc']) || empty($_GET['fc'])) {
  $asfname = $fname;
}
else {
  // remove some bad chars
  $asfname = str_replace(array('"',"'",'\\','/'), '', $_GET['fc']);
  if ($asfname === '') $asfname = 'YouNetWiki';
}
// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: $mtype");
header("Content-Disposition: attachment; filename=\"$asfname\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . $fsize);

// download
// @readfile($file_path);
ob_end_clean();
$file = @fopen($file_path,"rb");
if ($file) {
  while(!feof($file)) {
    print(fread($file, 1024*8));
    flush();
    if (connection_status()!=0) {
      @fclose($file);
      die();
    }
  }
  @fclose($file);
}

// log downloads
if (!LOG_DOWNLOADS) die();

$f = @fopen(LOG_FILE, 'a+');
if ($f) {
  @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$fname."\n");
  @fclose($f);
}
exit;
?>