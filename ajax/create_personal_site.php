<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkLoggedIn();

require_once('apps/files_picocms/lib/OC_Pico.php');

$folder = $_GET['folder'];
$content = $_GET['content'];
$destination = $_GET['destination'];
$theme = $_GET['theme'];
$name = $_GET['name'];
$user_id = OCP\USER::getUser();
$copy_themes = !empty($_GET['copy_themes']) && $_GET['copy_themes']=='yes';

$parts = pathinfo($folder);
$site = empty($name)?$parts['basename']:$name;
// Site is just the top-level element of folder (a path).

OC_Log::write('files_picocms',"Creating personal site: ".$folder, OC_Log::WARN);

if(empty($folder)){
	$ret['error'] = "Failed creating site. No folder name ".$folder;
}
if(empty($user_id)){
	$ret['error'] = "Failed creating site. No owner ".$user_id;
}

$res = OCA\FilesPicoCMS\Lib::createPersonalSite($user_id, $folder, $name, $content, $destination, $theme, $copy_themes);

switch($res){
	case OCA\FilesPicoCMS\Lib::$OK:
		$ret['msg'] = "Created site ".$folder;
		$ret['site'] = $site;
		break;
	case OCA\FilesPicoCMS\Lib::$COPY_CONTENT_FAILED:
		$ret['error'] = "Failed creating site: Copy failed.";
		break;
	case OCA\FilesPicoCMS\Lib::$SITE_NAME_EXISTS:
		$ret['error'] = "Site name taken: ".$site;
		break;
	default:
		$ret['error'] = "Failed creating site ".$folder;
}

OCP\JSON::encodedPrint($ret);
