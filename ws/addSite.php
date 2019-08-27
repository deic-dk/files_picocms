<?php

require_once __DIR__ . '/../../../lib/base.php';
require_once __DIR__ . '/../lib/OC_Pico.php';

OCP\JSON::checkAppEnabled('files_picocms');
 
if(!OCA\FilesSharding\Lib::checkIP()){
	http_response_code(401);
	exit;
}

$folder = isset($_GET['folder'])?$_GET['folder']:null;
$name = isset($_GET['name'])?$_GET['name']:null;
$group = isset($_GET['group'])?$_GET['group']:null;
$user_id = isset($_GET['user_id'])?$_GET['user_id']:null;
$shareSampleSite = isset($_GET['share_sample_site'])?$_GET['share_sample_site']=='yes':false;
$rename = isset($_GET['rename'])?$_GET['rename']=='yes':false;

$ret = [];

if(OCA\FilesPicoCMS\Lib::dbAddSite($folder, $name, $user_id, $group, $shareSampleSite, $rename)){
	$ret['msg'] = "Added site ".$folder;
}
else{
	$ret['error'] = "Failed serving site ".$folder;
}

OCP\JSON::encodedPrint($ret);

