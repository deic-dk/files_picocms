<?php

OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::checkAppEnabled('files_sharding');
OCP\JSON::checkAppEnabled('chooser');
OCP\User::checkLoggedIn();

OCP\Util::addscript('files_picocms', 'personalsettings');
OCP\Util::addStyle('files_sharding', 'personalsettings');

//OCP\Util::addStyle('chooser', 'jqueryFileTree');

//OCP\Util::addscript('chooser', 'jquery.easing.1.3');
//OCP\Util::addscript('chooser', 'jqueryFileTree');

require_once('apps/files_picocms/lib/OC_Pico.php');

$tmpl = new OCP\Template('files_picocms', 'personalsettings');

$user_id = OCP\USER::getUser();

$tmpl->assign('site_folders', OCA\FilesPicoCMS\Lib::getSiteFoldersList($user_id));
$sampleSitePath = OCP\Config::getAppValue('files_picocms', 'samplesitepath', 'samplesite');
$pathArr = pathinfo($sampleSitePath);
if(OCP\App::isEnabled('files_sharding')){
	$masterUrl = OCA\FilesSharding\Lib::getMasterURL();
	$sampleSiteUrl = $masterUrl . '/sites/' . $pathArr['basename'];
}
else{
	$sampleSiteUrl = OC::$WEBROOT . 'sites/' . $pathArr['basename'];
}
$tmpl->assign('samplesite_url', $sampleSiteUrl);
$createPersonalSiteUrl = OC::$WEBROOT . '/apps/files_picocms/index.php';
$tmpl->assign('create_personal_site_url', $createPersonalSiteUrl);

$tmpl->assign('serve_public_url', OCA\FilesPicoCMS\Lib::getServePublicUrl($user_id));

return $tmpl->fetchPage();