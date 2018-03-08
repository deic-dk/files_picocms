<?php

OC_Util::checkAdminUser();

require_once('apps/files_picocms/lib/OC_Pico.php');

OCP\Util::addScript('files_picocms', 'settings');

$tmpl = new OCP\Template('files_picocms', 'settings');

$folderArr = OCA\FilesPicoCMS\Lib::getSampleFolder();

$tmpl->assign('samplesiteowner', $folderArr['owner']);
$tmpl->assign('samplesitepath', $folderArr['path']);

return $tmpl->fetchPage();

