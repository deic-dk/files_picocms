<?php
 
require_once __DIR__ . '/../../../lib/base.php';
require_once __DIR__ . '/../lib/OC_Pico.php';

OCP\JSON::checkAppEnabled('files_picocms');
 
if(!OCA\FilesSharding\Lib::checkIP()){
	http_response_code(401);
	exit;
}

$serve = $_GET['serve'];
$user = $_GET['user'];

$ret = OCA\FilesPicoCMS\Lib::dbSetServePublicUrl($user, $serve==='yes');
OCP\JSON::encodedPrint($ret);

