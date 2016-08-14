<?php

OC_Util::checkAdminUser();

OCP\Util::addScript('files_picocms', 'settings');

$tmpl = new OCP\Template('files_picocms', 'settings');

$tmpl->assign('samplesiteowner', OCP\Config::getAppValue('files_picocms', 'samplesiteowner', ''));
$tmpl->assign('samplesitepath', OCP\Config::getAppValue('files_picocms', 'samplesitepath', 'samplesite'));

return $tmpl->fetchPage();

