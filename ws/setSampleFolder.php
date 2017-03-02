<?php
 
require_once __DIR__ . '/../../../lib/base.php';
require_once __DIR__ . '/../lib/OC_Pico.php';

OCP\JSON::checkAppEnabled('files_picocms');
 
if(!OCA\FilesSharding\Lib::checkIP()){
	http_response_code(401);
	exit;
}

$owner = isset($_GET['owner'])?$_GET['owner']:null;
$path = isset($_GET['path'])?$_GET['path']:null;

$ret = OCA\FilesPicoCMS\Lib::dbChangeSampleFolder($owner, $path);
OCP\JSON::encodedPrint($ret);

