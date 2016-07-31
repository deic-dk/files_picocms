<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkLoggedIn();

$folder = $_POST['folder'];
$user_id = isset($_POST['user_id'])?$_POST['user_id']:\OCP\USER::getUser();

OC_Log::write('files_picocms',"Unserving folder: ".$folder, OC_Log::WARN);

if(empty($folder) ||
		!OCA\FilesPicoCMS\Lib::removeSiteFolder($folder, $user_id)){
	$ret['error'] = "Failed unserving folder ".$folder;
}
else{
	$ret['msg'] = "Unserved folder ".$folder;
}

OCP\JSON::encodedPrint($ret);
