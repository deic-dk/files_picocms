<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkLoggedIn();

require_once('apps/files_picocms/lib/OC_Pico.php');

$serve = $_GET['serve'];
$user = \OCP\USER::getUser();

OC_Log::write('files_picocms',"Setting serving of public folder to  ".$serve." for ".$user, OC_Log::WARN);

$ret = [];

if(empty($serve) ||
		!OCA\FilesPicoCMS\Lib::setServePublicUrl($user, $serve=='yes')){
			$ret['error'] = "Failed changing serving  to ".$serve;
}
else{
	$ret['msg'] = "Changed serving to ".$serve;
	$ret['data'] = ['serve'=>$serve];
}

OCP\JSON::encodedPrint($ret);
