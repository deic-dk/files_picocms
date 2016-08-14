<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkLoggedIn();

require_once('apps/files_picocms/lib/OC_Pico.php');

$owner = $_POST['owner'];
$path = $_POST['path'];

OC_Log::write('files_picocms',"Changing sample folder owner to ".$path." of ".$owner, OC_Log::WARN);

if(empty($owner) || empty($path) ||
		!OCA\FilesPicoCMS\Lib::setSampleFolder($owner, $path)){
	$ret['error'] = "Failed changing folder  to ".$path;
}
else{
	$ret['msg'] = "Changed folder to ".$path;
}

OCP\JSON::encodedPrint($ret);
