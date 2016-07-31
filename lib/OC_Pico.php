<?php

namespace OCA\FilesPicoCMS;

class Lib {
	
	public static function dbLookupSitePath($site){
		$query = \OC_DB::prepare('SELECT * FROM `*PREFIX*files_picocms` WHERE `site` = ?');
		$result = $query->execute(Array($site));
		if(\OCP\DB::isError($result)){
			\OCP\Util::writeLog('files_sharding', \OC_DB::getErrorMessage($result), \OC_Log::ERROR);
		}
		$results = $result->fetchAll();
		if(count($results)>1){
			\OCP\Util::writeLog('files_sharding', 'ERROR: Duplicate entries found for server '.$node, \OCP\Util::ERROR);
		}
		foreach($results as $row){
			return $row['user'].'/'.$row['path'];
		}
		return null;
	}
	
	public static function dbAddSiteFolder($folder, $user_id){
		$parts = pathinfo($folder);
		$site = $parts['basename'];
		// Site is just the top-level element of folder (a path).
		// Check if site name is taken
		if(self::dbLookupSitePath($site)){
			return false;
		}
		$query = \OC_DB::prepare(
				'INSERT INTO `*PREFIX*files_picocms` (`user`, `site`, `path`) VALUES (?, ?, ?)');
		$result = $query->execute(Array(user_id, $site, $folder));
		if(\OCP\DB::isError($result)){
			\OCP\Util::writeLog('files_picocms', \OC_DB::getErrorMessage($result), \OC_Log::ERROR);
			return false;
		}
		return true;
	}
	
	public static function addSiteFolder($folder, $user_id){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbAddSiteFolder($folder, $user_id);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('addSiteFolder', Array('folder'=>$folder, 'user_id' =>$user_id),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbRemoveSiteFolder($folder, $user_id){
		$query = \OC_DB::prepare(
				'DELETE FROM `*PREFIX*files_picocms` WHERE `path` = ? AND `user` = ?');
		$result = $query->execute(Array($folder, $user_id));
		if(\OCP\DB::isError($result)){
			\OCP\Util::writeLog('files_picocms', \OC_DB::getErrorMessage($result), \OC_Log::ERROR);
			return false;
		}
		return true;
		
	}
	
	public static function removeSiteFolder($folder, $user_id){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::removeSiteFolder($folder, $user_id);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('removeSiteFolder', Array('folder'=>$folder, 'user_id' =>$user_id),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbLookupSitePath($site, $user_id){
		return \OC_Preferences::setValue($user_id, 'files_picocms', 'site_name');
	}
	
	public static function lookupSitePath($site, $user_id){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbLookupSitePath($site, $user_id);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('lookupSitePath', Array('site'=>$site, 'user_id' =>$user_id),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
}