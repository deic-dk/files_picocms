<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkLoggedIn();

require_once('apps/files_picocms/lib/OC_Pico.php');

$folder = $_POST['folder'];
$name = $_POST['name'];
$group = empty($_POST['group'])?'':$_POST['group'];

$shareSampleSite = !empty($_POST['share_sample_site'])?$_POST['share_sample_site']=='yes':false;
$rename = !empty($_POST['rename'])?$_POST['rename']=='yes':false;

if(isset($_POST['user_id'])){
	$user_id = $_POST['user_id'];
}
else{
	$user_id = OCP\USER::getUser();
}

OC_Log::write('files_picocms',"Adding site: ".$folder.":".$name." for ".$user_id."-->".$shareSampleSite, OC_Log::WARN);

$ret = [];

if(empty($folder) || empty($user_id) ||
		!OCA\FilesPicoCMS\Lib::addSite($folder, $name, $user_id, $group, $shareSampleSite, $rename)){
	$ret['error'] = "Failed adding folder ".$folder;
}
else{
	$ret['msg'] = "Added folder ".$folder;
}

OCP\JSON::encodedPrint($ret);
