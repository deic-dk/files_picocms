<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkLoggedIn();

require_once('apps/files_picocms/lib/OC_Pico.php');

$folder = $_GET['folder'];
$content = $_GET['content'];
$theme = $_GET['theme'];
$user_id = OCP\USER::getUser();

$parts = pathinfo($folder);
$site = $parts['basename'];
// Site is just the top-level element of folder (a path).

OC_Log::write('files_picocms',"Creating personal site: ".$folder, OC_Log::WARN);

if(empty($folder) || empty($user_id) ||
		!OCA\FilesPicoCMS\Lib::createPersonalSite($user_id, $folder, $content, $theme)){
	$ret['error'] = "Failed creating site ".$folder;
}
else{
	$ret['msg'] = "Created site ".$folder;
	$ret['site'] = $site;
}

OCP\JSON::encodedPrint($ret);
