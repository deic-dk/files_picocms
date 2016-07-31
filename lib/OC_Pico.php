<?php

namespace OCA\FilesPicoCMS;

class Lib {
	
	static function lookupUser($siteName){
		$users = \OC_Preferences::getUsersForValue('files_picocms', 'site_name', $siteName);
		if(count($users)>1){
			\OCP\Util::writeLog('files_picocms', 'ERROR: Duplicate entries found for site_name '.$siteName, \OCP\Util::ERROR);
			return null;
		}
		elseif(count($users)<1){
			\OCP\Util::writeLog('files_picocms', 'ERROR: no entry found for site_name '.$siteName, \OCP\Util::ERROR);
			return null;
		}
		return $users[0];
	}
	
	static function lookupContentParent($user){
		\OC_Preferences::getValue($user, 'files_picocms', 'content_parent');
	}
	
}