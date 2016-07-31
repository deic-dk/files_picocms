<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkLoggedIn();

$folder = $_POST['folder'];

if(isset($_POST['user_id'])){
	$user_id = $_POST['user_id'];
}
else{
	$user_id = OCP\USER::getUser();
}

OC_Log::write('files_picocms',"Adding folder: ".$folder." for ".$user_id, OC_Log::WARN);


if(empty($folder) || empty($user_id) ||
		!OCA\FilesPicoCMS\Lib::addSiteFolder($folder, $user_id)){
	$ret['error'] = "Failed adding folder ".$folder;
}
else{
	$ret['msg'] = "Added folder ".$folder;
}

OCP\JSON::encodedPrint($ret);
