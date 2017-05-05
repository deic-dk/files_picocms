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

if(empty($folder)){
	$ret['error'] = "Failed creating site. No folder name ".$folder;
}
if(empty($user_id)){
	$ret['error'] = "Failed creating site. No owner ".$user_id;
}
		
$res = OCA\FilesPicoCMS\Lib::createPersonalSite($user_id, $folder, $content, $theme);

switch($res){
	case OCA\FilesPicoCMS\Lib::$OK:
		$ret['msg'] = "Created site ".$folder;
		$ret['site'] = $site;
		break;
	case OCA\FilesPicoCMS\Lib::$COPY_CONTENT_FAILED:
		$ret['error'] = "Failed creating site: Copy failed.";
		break;
	case OCA\FilesPicoCMS\Lib::$SITE_NAME_EXISTS:
		$parts = pathinfo($folder);
		$site = $parts['basename'];
		$ret['error'] = "Site name taken: ".$site;
		break;
	default:
		$ret['error'] = "Failed creating site ".$folder;
}

OCP\JSON::encodedPrint($ret);
