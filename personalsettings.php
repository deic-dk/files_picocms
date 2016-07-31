<?php

OCP\JSON::checkAppEnabled('files_sharding');
OCP\User::checkLoggedIn();

OCP\Util::addscript('files_sharding', 'personalsettings');
OCP\Util::addStyle('files_sharding', 'personalsettings');

OCP\Util::addStyle('chooser', 'jqueryFileTree');

OCP\Util::addscript('chooser', 'jquery.easing.1.3');
OCP\Util::addscript('chooser', 'jqueryFileTree');

$errors = Array();

$tmpl = new OCP\Template('files_picocms', 'personalsettings');

$user_id = OCP\USER::getUser();

$tmpl->assign('site_folders', OCA\FilesSharding\Lib::getSiteFoldersList($user_id));

return $tmpl->fetchPage();