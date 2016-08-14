<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('files_picocms');
OCP\JSON::callCheck();

$tmpl = new OCP\Template("files_picocms", "help");
$page = $tmpl->fetchPage();
OCP\JSON::success(array('data' => array('page'=>$page)));
