<?php
 
require_once __DIR__ . '/../../../lib/base.php';
require_once __DIR__ . '/../lib/OC_Pico.php';

OCP\JSON::checkAppEnabled('files_accounting');
OCP\JSON::checkAppEnabled('files_sharding');
 
if(!OCA\FilesSharding\Lib::checkIP()){
        http_response_code(401);
        exit;
}

$user_id = isset($_GET['user_id'])?$_GET['user_id']:null;

$ret = OCA\FilesPicoCMS\Lib::dbGetSiteFoldersList($user_id);
OCP\JSON::encodedPrint($ret);


