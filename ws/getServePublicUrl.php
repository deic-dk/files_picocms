<?php
 
require_once __DIR__ . '/../../../lib/base.php';
require_once __DIR__ . '/../lib/OC_Pico.php';

OCP\JSON::checkAppEnabled('files_picocms');
 
if(!OCA\FilesSharding\Lib::checkIP()){
	http_response_code(401);
	exit;
}

$user = $_GET['user'];

$ret = OCA\FilesPicoCMS\Lib::dbGetServePublicUrl($user);
OCP\JSON::encodedPrint($ret);

