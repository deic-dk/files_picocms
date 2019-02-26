<?php

namespace OCA\FilesPicoCMS;

class Lib {
	
	private static function copyFile($srcFile, $destFile, $destView, $srcView=null, $replacements=null,
			$repFilePattern=null){
		if(!empty($srcView) && $srcView===$destView){
			$srcView->copy($srcFile, $destFile);
		}
		else{
			if(empty($srcView)){
				// empty srcView means we're copying an absolute path
				$tmpFile = tempnam(sys_get_temp_dir(), 'files_picocms_');
				\OCP\Util::writeLog('files_picocms', 'Copying '.$srcFile.' to '.$tmpFile, \OCP\Util::WARN);
				copy($srcFile, $tmpFile);
			}
			else{
				$tmpFile = $srcView->toTmpFile($srcFile);
			}
			if(!empty($destView)){
				$destView->fromTmpFile($tmpFile, $destFile);
				/*if(empty($srcView)){
					unlink($tmpFile);
				}
				else{
					$srcView->unlink($tmpFile);
				}*/
				if(!empty($replacements) && !empty($repFilePattern) && preg_match($repFilePattern, $destFile)){
					$str = $destView->file_get_contents($destFile);
					foreach($replacements as $pattern=>$replacement){
						$str = preg_replace($pattern, $replacement, $str);
						\OCP\Util::writeLog('files_picocms', 'Replacing'.$pattern.'=>'.$replacement.
							' in '. $destFile, \OCP\Util::WARN);
					}
					$destView->unlink($destFile);
					\OCP\Util::writeLog('files_picocms', 'Writing '.$str.' with '.serialize($destView), \OCP\Util::WARN);
					$destView->file_put_contents($destFile, $str);
				}
			}
			else{
				\OCP\Util::writeLog('files_picocms', 'ERROR: Cannot copy to remote server '.$destFile, \OCP\Util::ERROR);
				return false;
			}
		}
		return true;
	}
	
	private static function copyRec($src, $dest, $destView, $srcView=null, $replacements=null,
			$repFilePattern=null){
		if((empty($srcView) && !file_exists($src)) || (!empty($srcView) && !$srcView->file_exists($src))){
			\OCP\Util::writeLog('files_picocms', 'No such file or directory '.$src, \OC_Log::ERROR);
			return false;
		}
		
		if((empty($srcView) && is_dir($src)) || (!empty($srcView) && $srcView->is_dir($src))){ // copy dir
			if((empty($srcView) && $dh = opendir($src)) || (!empty($srcView) && $dh = $srcView->opendir($src))){
				if(!$destView->is_dir($dest) && !$destView->mkdir($dest)){
					\OCP\Util::writeLog('files_picocms', 'Could not create '.$dest, \OC_Log::ERROR);
					return false;
				}
				if(empty($dh)){
					\OCP\Util::writeLog('files_picocms', 'Could not read '.$src, \OC_Log::ERROR);
					return false;
				}
				while($dh!==false && $dh!==true && ($file = readdir($dh))!==false){
					\OCP\Util::writeLog('files_picocms', 'Copying file '.$file, \OC_Log::ERROR);
					if(empty($file) || in_array($file, array('.', '..'))){
						continue;
					}
					if((empty($srcView) && is_dir($src.'/'.$file)) || (!empty($srcView) && $srcView->is_dir($src.'/'.$file))){
						self::copyRec($src.'/'.$file, $dest.'/'.$file, $destView, $srcView,
								$replacements, $repFilePattern);
					}
					else{
						self::copyFile($src.'/'.$file, $dest.'/'.$file, $destView, $srcView,
								$replacements, $repFilePattern);
					}
				}
			}
		}
		else{ // copy file
			self::copyFile($src, $dest, $destView, $srcView, $replacements, $repFilePattern);
		}
		return true;
	}
	
	public static $OK = 0;
	public static $SITE_NAME_EXISTS = 1;
	public static $COPY_CONTENT_FAILED = 2;
	
	public static function createPersonalSite($user_id, $folder, $content=null, $destination=null, $theme=null, $copy_themes=false){
		// Serve
		if($folder!='/public' && !self::addSiteFolder($folder, $user_id, '')){
			\OCP\Util::writeLog('files_picocms', 'Adding site '.$folder, \OC_Log::WARN);
			return self::$SITE_NAME_EXISTS;
		}
		// Create directory
		if(!\OC\Files\Filesystem::file_exists($folder)){
			\OC\Files\Filesystem::mkdir($folder);
		}
		
		//$srcView = new \OC\Files\View('/'.$user_id.'/files_picocms');
		$srcView = null;
		$destView = new \OC\Files\View('/'.$user_id.'/files');
	
		// Copy over themes
		if($copy_themes && !empty($theme)){
			$themesFolder = '/samplesite/themes';
			$themesFolder = dirname(__FILE__).$themesFolder;
			\OCP\Util::writeLog('files_picocms', 'Copying themes'.$themesFolder.'-->'.$folder.'/themes', \OC_Log::WARN);
			self::copyRec($themesFolder, $folder.'/themes', $destView, $srcView);
		}
		
		// Add content
		if(!empty($content) && (strlen($content)<3 || substr($content, -3)!='.md')){
			$content = dirname(__FILE__).$content;
			if(!empty($theme)){
				\OCP\Util::writeLog('files_picocms', 'Copying content, fixing theme etc. '.$content.
						'-->'.$folder.'/content', \OC_Log::WARN);
				if(!self::copyRec($content, $folder.(empty($destination)?'':'/'.$destination), $destView, $srcView,
						array('/^Theme:.*$/m'=>'Theme: '.$theme,
								'/^Date:.*$/m'=>'Date: '.date("j M Y"),
								'/^Author:.*$/m'=>'Author: '.$user_id,
								'/^Site:.*$/m'=>'Site: '.($theme=='deic-blog'?\OC_User::getDisplayName($user_id):'Sample Site')),
						'|.*\.md$|')){
					return self::$COPY_CONTENT_FAILED;
				}
			}
			else{
				\OCP\Util::writeLog('files_picocms', 'Copying content', \OC_Log::WARN);
				if(!self::copyRec($content, $folder.(empty($destination)?'':'/'.$destination), $destView, $srcView)){
					return self::$COPY_CONTENT_FAILED;
				}
			}
		}
		elseif(!empty($theme) && strlen($content)>3 && substr($content, -3)=='.md'){
			$srcName = basename($content);
			if(!self::copyFile(dirname(__FILE__).$content, $folder.'/'.(empty($destination)?$srcName:$destination), $destView, $srcView,
					array('/^Theme:.*$/m'=>'Theme: '.$theme,
							'/^Date:.*$/m'=>'Date: '.date("j M Y"),
							'/^Author:.*$/m'=>'Author: '.$user_id,
							'/^Site:.*$/m'=>'Site: '.($theme=='deic-blog'?\OC_User::getDisplayName($user_id):'Sample Site')),
					'|.*\.md$|')){
						return self::$COPY_CONTENT_FAILED;
					}
		}
		\OCP\Util::writeLog('files_picocms', 'Adding site done', \OC_Log::WARN);
		return self::$OK;
	}
	
	public static function addSiteFolder($folder, $user_id, $group, $shareSampleSite=false){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbAddSiteFolder($folder, $user_id, $group, $shareSampleSite);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('addSiteFolder', Array('folder'=>urlencode($folder), 'user_id' =>$user_id,
					'group'=>urlencode($group), 'share_sample_site'=>($shareSampleSite?'yes':'no')),
					false, true, null, 'files_picocms');
			if(!empty($ret['error'])){
				return false;
			}
			else{
				return true;
			}
		}
	}
	
	public static function dbAddSiteFolder($folder, $user_id, $group='', $shareSampleSite=false){
		$parts = pathinfo($folder);
		$site = $parts['basename'];
		// Site is just the top-level element of folder (a path).
		// Check if site name is taken
		if(self::dbLookupSiteInfo($site)){
			return false;
		}
		$query = \OC_DB::prepare(
				'INSERT INTO `*PREFIX*files_picocms` (`uid`, `site`, `path`, `gid`) VALUES (?, ?, ?, ?)');
		$result = $query->execute(Array($user_id, $site, $folder, $group));
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
			return true;
		}
		$path = \OCP\Config::getAppValue('files_picocms', 'samplesitepath');
		$loggedin_user = \OCP\USER::getUser();
		$old_user = null;
		
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
		
		if(!$share){
			return true;
		}
		
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
		
		// If not already shared, share
		$ok = false;
		try{
			$ok = self::doShareSampleSite($path, $owner, $user_id);
		}
		catch(\Exception $e){
			\OCP\Util::writeLog('files_picocms', 'ERROR '.$e->getMessage(), \OC_Log::ERROR);
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
		return $ok;
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
		
		if(\OCP\App::isEnabled('files_sharding') && !\OCA\FilesSharding\Lib::onServerForUser($owner)){
			$alreadySharedItem = \OCP\Share::getItemShared('file', $sampleFolderId);
		}
		else{
			$alreadySharedItem = \OCA\Files\Share_files_sharding\Api::getItemShared('file', $sampleFolderId);
		}
		
		//$sampleFolderPath = \OC\Files\Filesystem::getPath($sampleFolderId);
		\OCP\Util::writeLog('files_picocms', 'PATH: '.$path.'-->'.$sampleFolderId.'-->'.
				serialize($alreadySharedItem), \OC_Log::WARN);
		
		if(empty($alreadySharedItem) && !empty($sampleFolderId) /*&& !empty($sampleFolderPath) && '/'.$path==$sampleFolderPath*/){
			if(\OCP\App::isEnabled('files_sharding')){
				return \OCA\Files\Share_files_sharding\Api::shareItem(
						'folder',
						$sampleFolderId,
						\OCP\Share::SHARE_TYPE_USER,
						$user_id,
						\OCP\PERMISSION_READ
				);
			}
			elseif(empty($alreadySharedItem)){
				return \OCP\Share::shareItem(
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
		return true;
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
		$query = \OC_DB::prepare('SELECT * FROM `*PREFIX*files_picocms` WHERE `uid` = ?');
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
			$ret = \OCA\FilesSharding\Lib::ws('removeSiteFolder', Array('folder'=>urlencode($folder), 'user_id' =>$user_id),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbRemoveSiteFolder($folder, $user_id){
		$query = \OC_DB::prepare(
				'DELETE FROM `*PREFIX*files_picocms` WHERE `path` = ? AND `uid` = ?');
		$result = $query->execute(Array($folder, $user_id));
		if(\OCP\DB::isError($result)){
			\OCP\Util::writeLog('files_picocms', \OC_DB::getErrorMessage($result), \OC_Log::ERROR);
			return false;
		}
		return true;
		
	}
	
	public static function dbLookupSiteInfo($site){
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
			return $row;
		}
		return null;
	}
	
	public static function lookupSiteInfo($site){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbLookupSiteInfo($site);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('lookupSiteInfo', Array('site'=>urlencode($site)),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbSetSampleFolder($owner, $path){
		$ret1 = \OCP\Config::setAppValue('files_picocms', 'samplesiteowner', $owner);
		$ret2 = \OCP\Config::setAppValue('files_picocms', 'samplesitepath', $path);
		return $ret1 && $ret2;
	}
	
	public static function dbGetSampleFolder(){
		$owner = \OCP\Config::getAppValue('files_picocms', 'samplesiteowner');
		$path = \OCP\Config::getAppValue('files_picocms', 'samplesitepath');
		return array('owner'=>$owner, 'path'=>$path);
	}
	
	public static function setSampleFolder($owner, $path){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbSetSampleFolder($owner, $path);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('setSampleFolder', Array('owner'=>$owner, 'path'=>urlencode($path)),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function getSampleFolder(){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbGetSampleFolder();
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('getSampleFolder', Array(),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function getServePublicUrl($user){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbGetServePublicUrl($user)=='yes';
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('getServePublicUrl', Array('user'=>$user),
					false, true, null, 'files_picocms');
			return $ret=='yes';
		}
	}
	
	public static function dbGetServePublicUrl($user){
		$ret = \OCP\Config::getUserValue($user, 'files_picocms', 'servepublicurl');
		return $ret;
	}
	
	public static function setServePublicUrl($user, $serve){
		if(!\OCP\App::isEnabled('files_sharding') || \OCA\FilesSharding\Lib::isMaster()){
			return self::dbSetServePublicUrl($user, $serve);
		}
		else{
			$ret = \OCA\FilesSharding\Lib::ws('setServePublicUrl', Array('user'=>$user, 'serve'=>$serve?'yes':'no'),
					false, true, null, 'files_picocms');
			return $ret;
		}
	}
	
	public static function dbSetServePublicUrl($user, $serve){
		$ret = \OCP\Config::setUserValue($user, 'files_picocms', 'servepublicurl', $serve?'yes':'no');
		return $ret;
	}
	
}