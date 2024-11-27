	<?php 
		$user = \OCP\USER::getUser();
		$email = \OCP\Config::getUserValue($user, 'settings', 'email');
		$master = \OCA\FilesSharding\Lib::getMasterURL();
		$publicUrl = $master."users/".$email."/";
	?>
	<fieldset id="filesPicoSiteFolders" class="section">
	<a href="<?php print_unescaped($_['samplesite_url']);?>" class="right-info-link"><?php p($l->t('More info'));?> &raquo;</a>
	<?php print_unescaped("<h2>".$l->t('Site folders')."</h2>".
	$l->t("Site folders must contain files in markdown format - with extension 'md'.
	These files will be parsed and served up to browsers as html by Pico CMS.
	Click on the plus below to add a folder. You must youself create a \"content\"
	subfolder inside this folder and populate it with pages in Markdown format -
	plus a \"themes\" subfolder if you want to customize your pages.
	A site will be served at the URL %s/sites/site_folder, where
	\"site_folder\" is the name of site folder you choose.", array(OC::$WEBROOT)));?>
	<br /><br />
	<?php p($l->t("Or use the website wizard to create a simple site."));?>
	<br /><br />
	<div><div  class="edit_personal_website btn btn-primary btn-flat">
	<?php p($l->t("Website wizard"));?></div></div>
	<br />
	<div class="siteFolder nowrap">
		<span class="folder">
			<label><?php p($l->t("Folder"));?></label>
		</span>
		<span class="url">
			<label><?php p($l->t("URL"));?></label>
		</span>
		<span>
			<label class="remove"><?php p($l->t("Remove/Add"));?></label>
		</span>
	</div>
	<div id="filesPicoSiteFoldersList">
	<?php foreach($_['site_folders'] as $p){ ?>
		<div class="siteFolder nowrap" path="<?php print($p['path']);?>">
			<span class="folder">
				<a href="<?php print(OC::$WEBROOT);?>/index.php/apps/files/?dir=<?php print($p['path']);?>">
					<label class="folder"><?php print($p['path']);?></label>
				</a>
			</span>
			<span class="url">
				<a class="site_url" href="<?php print($master);?>sites/<?php print($p['site']);?>">
					<label><?php print($master."sites/");?></label>
				</a>
				<input type="text" autocomplete="off" value="<?php print($p['site']);?>" />
			</span>
			<label class="remove_site_folder btn btn-flat">-</label>
			<div class="dialog" display="none"></div>
		</div>
	<?php } ?>
	</div>
	<br />
	<div class="nowrap addSiteFolder">
		<span class="extraSpan"><label></label></span><label title="Add site folder" class="add_site_folder btn btn-flat">+</label>
		<div id="chosen_site_folder"></div>
		<div class="dialog" display="none">
			<div id="loadSiteFolderTree"></div>
		</div>
	</div>
	<div>
		<span>Serve <a href="<?php print(OC::$WEBROOT);?>/index.php/apps/files/?dir=/public">/public</a> as <?php echo "<a id='public_site_url' href='".$publicUrl."'>".$publicUrl;?></a></span>
		<span id="serve_public_url_checkbox">&nbsp;&nbsp;<input id="serve_public_url" type="checkbox" original-title="Serve public folder"
			<?php echo($_['serve_public_url']?"checked='checked'":"");?>></span>
	</div>
</fieldset>