<?php
 
OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkAppEnabled('files_sharding');
 
if(!OCA\FilesSharding\Lib::checkIP()){
	http_response_code(401);
	exit;
}

$folder = isset($_GET['folder'])?$_GET['folder']:null;
$user_id = isset($_GET['user_id'])?$_GET['user_id']:null;

$ret = OCA\FilesPicoCMS\Lib::dbRemoveSiteFolder($folder, $user_id);
OCP\JSON::encodedPrint($ret);

