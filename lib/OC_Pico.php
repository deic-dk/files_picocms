<?php

namespace OCA\FilesPicoCMS;

class Lib {
	
	public static function addSiteFolder($folder, $user_id, $shareSampleSite=false){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbAddSiteFolder($folder, $user_id, $shareSampleSite);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('addSiteFolder', Array('folder'=>$folder, 'user_id' =>$user_id,
					'share_sample_site'=>($shareSampleSite?'yes':'no')),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbAddSiteFolder($folder, $user_id, $shareSampleSite=false){
		$parts = pathinfo($folder);
		$site = $parts['basename'];
		// Site is just the top-level element of folder (a path).
		// Check if site name is taken
		if(self::dbLookupSitePath($site)){
			return false;
		}
		$query = \OC_DB::prepare(
				'INSERT INTO `*PREFIX*files_picocms` (`user`, `site`, `path`) VALUES (?, ?, ?)');
		$result = $query->execute(Array($user_id, $site, $folder));
		if(\OCP\DB::isError($result)){
			\OCP\Util::writeLog('files_picocms', \OC_DB::getErrorMessage($result), \OC_Log::ERROR);
			return false;
		}
		if($shareSampleSite){
			return self::shareSampleSite($user_id);
		}
		return true;
		
	}
	
	private static function shareSampleSite($user_id){
		$owner = \OCP\Config::getAppValue('files_picocms', 'samplesiteowner');
		if($owner==$user_id){
			return;
		}
		$path = \OCP\Config::getAppValue('files_picocms', 'samplesitepath');
		$loggedin_user = \OCP\USER::getUser();
		$old_user = null;
		if(isset($owner)){
			if(!empty($loggedin_user) && !empty($owner) && $owner!==$loggedin_user){
				$old_user = $loggedin_user;
				\OC\Files\Filesystem::tearDown();
				\OC_User::setUserId($owner);
				//\OC_Util::teardownFS();
				\OC_Util::setupFS($owner);
				\OC\Files\Filesystem::init($owner, '/'.$owner.'/files');
				\OCP\Util::writeLog('files_picocms', 'Changed user from '.$old_user.' to '.\OCP\USER::getUser(), \OC_Log::WARN);
			}
		}
		
		// Check if already shared
		if(\OCP\App::isEnabled('files_sharding') && !\OCA\FilesSharding\Lib::onServerForUser($owner)){
			$allShares = \OCA\Files\Share_files_sharding\Api::getFilesSharedWithMe();
		}
		else{
			$allShares = \OCP\Share::getItemsSharedWith('file');
		}
		\OCP\Util::writeLog('files_picocms', 'Shares: '.serialize($allShares), \OC_Log::WARN);
		$share = true;
		foreach($allShares as $share){
			if($share['uid_owner']==$owner && $share['file_target']=='/'.$path){
				$share = false;
				break;
			}
		}
		
		// If not, share
		if($share){
			self::doShareSampleSite($path, $owner, $user_id);
		}
		
		if(!empty($old_user)){
			\OCP\Util::writeLog('files_picocms', 'Changing user back to '.$old_user, \OC_Log::WARN);
			//\OC_Util::teardownFS();
			\OC\Files\Filesystem::tearDown();
			\OC_User::setUserId($old_user);
			\OC_Util::setupFS($old_user);
			//\OC\Files\Filesystem::init($old_user, '/'.$old_user);
			\OCP\Util::writeLog('files_picocms', 'Changed user back to '.\OCP\USER::getUser(), \OC_Log::WARN);
		}
	}
	
	private static function doShareSampleSite($path, $owner, $user_id){
		if(\OCP\App::isEnabled('files_sharding') && !\OCA\FilesSharding\Lib::onServerForUser($owner)){
			$arr = array('user_id' => $loggedin_user, 'path'=>urlencode('/'.$path), 'owner'=>$owner);
			$dataServer = \OCA\FilesSharding\Lib::getServerForUser($owner, true);
			$fileInfo = \OCA\FilesSharding\Lib::ws('getFileInfoData', $arr, false, true, $dataServer);
		}
		else{
			$fileInfo = \OC\Files\Filesystem::getFileInfo('/'.$path);
		}
		
		$sampleFolderId = $fileInfo['fileid'];
		\OCP\Util::writeLog('files_picocms', 'INFO: '.$path.':'.$fileInfo['fileid'], \OC_Log::WARN);
		
		//$sampleFolderPath = \OC\Files\Filesystem::getPath($sampleFolderId);
		\OCP\Util::writeLog('files_picocms', 'PATH: '.$path.'-->'.$sampleFolderId, \OC_Log::WARN);
		
		if(!empty($sampleFolderId) /*&& !empty($sampleFolderPath) && '/'.$path==$sampleFolderPath*/){
			if(\OCP\App::isEnabled('files_sharding')){
				\OCA\Files\Share_files_sharding\Api::shareItem(
						'folder',
						$sampleFolderId,
						\OCP\Share::SHARE_TYPE_USER,
						$user_id,
						\OCP\PERMISSION_READ
				);
			}
			else{
				\OCP\Share::shareItem(
						'folder',
						$sampleFolderId,
						\OCP\Share::SHARE_TYPE_USER,
						$user_id,
						\OCP\PERMISSION_READ
				);
			}
		}
		// Now set parent to -1 to prevent showing the item in the file listing
		/*if(\OCP\App::isEnabled('files_sharding')){
			$query = \OC_DB::prepare('UPDATE `*PREFIX*share` SET `parent` = ? WHERE `item_source` = ?');
			$query->execute(array(-1, $sampleFolderId));
		}*/
	}
	
	public static function getSiteFoldersList($user_id){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbGetSiteFoldersList($user_id);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('getSiteFoldersList', Array('user_id' =>$user_id),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbGetSiteFoldersList($user_id){
		$query = \OC_DB::prepare('SELECT * FROM `*PREFIX*files_picocms` WHERE `user` = ?');
		$result = $query->execute(Array($user_id));
		if(\OCP\DB::isError($result)){
			\OCP\Util::writeLog('files_picocms', \OC_DB::getErrorMessage($result), \OC_Log::ERROR);
		}
		return $result->fetchAll();
	}
	
	public static function removeSiteFolder($folder, $user_id){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbRemoveSiteFolder($folder, $user_id);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('removeSiteFolder', Array('folder'=>$folder, 'user_id' =>$user_id),
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
	
	public static function dbLookupSitePath($site){
		$query = \OC_DB::prepare('SELECT * FROM `*PREFIX*files_picocms` WHERE `site` = ?');
		$result = $query->execute(Array($site));
		if(\OCP\DB::isError($result)){
			\OCP\Util::writeLog('files_picocms', \OC_DB::getErrorMessage($result), \OC_Log::ERROR);
		}
		$results = $result->fetchAll();
		if(count($results)>1){
			\OCP\Util::writeLog('files_picocms', 'ERROR: Duplicate entries found for server '.$node, \OCP\Util::ERROR);
		}
		foreach($results as $row){
			return $row['user'].'/files/'.$row['path'];
		}
		return null;
	}
	
	public static function lookupSitePath($site){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbLookupSitePath($site);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('lookupSitePath', Array('site'=>$site),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbSetSampleFolder($owner, $path){
		$ret1 = \OCP\Config::setAppValue('files_picocms', 'samplefolderowner', $owner);
		$ret2 = \OCP\Config::setAppValue('files_picocms', 'samplefolderpath', $path);
		return $ret1 && $ret2;
	}
	
	public static function setSampleFolder($owner, $path){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbSetSampleFolder($owner, $path);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('setSampleFolder', Array('owner'=>$owner, 'path'=>$path),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
}