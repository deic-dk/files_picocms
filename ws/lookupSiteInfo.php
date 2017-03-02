<?php
 
require_once __DIR__ . '/../../../lib/base.php';
require_once __DIR__ . '/../lib/OC_Pico.php';

OCP\JSON::checkAppEnabled('files_accounting');
OCP\JSON::checkAppEnabled('files_sharding');
 
if(!OCA\FilesSharding\Lib::checkIP()){
        http_response_code(401);
        exit;
}

$site = isset($_GET['site'])?$_GET['site']:null;

$ret = OCA\FilesPicoCMS\Lib::dbLookupSiteInfo($site);
OCP\JSON::encodedPrint($ret);


