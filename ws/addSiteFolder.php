<?php

require_once __DIR__ . '/../../../lib/base.php';
require_once __DIR__ . '/../lib/OC_Pico.php';

OCP\JSON::checkAppEnabled('files_picocms');
 
if(!OCA\FilesSharding\Lib::checkIP()){
	http_response_code(401);
	exit;
}

$folder = isset($_GET['folder'])?$_GET['folder']:null;
$user_id = isset($_GET['user_id'])?$_GET['user_id']:null;
$shareSampleSite = isset($_GET['share_sample_site'])?$_GET['share_sample_site']=='yes':false;

if(OCA\FilesPicoCMS\Lib::dbAddSiteFolder($folder, $user_id)){
	$ret['msg'] = "Added site ".$folder;
	$ret['site'] = $site;
}
else{
	$ret['error'] = "Failed serving site ".$folder;
}

OCP\JSON::encodedPrint($ret);

