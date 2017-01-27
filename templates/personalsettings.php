<fieldset id="filesPicoSiteFolders" class="section">
	<a href="<?php print($_['samplesite_url']);?>" class="right-info-link">More info &raquo;</a>
	<h2><?php p($l->t('Site folders')); ?></h2>
	Site folders must contain files in markdown format - with extension 'md'.
	These files will be parsed and served up to browsers as html by Pico CMS.
	Click on the plus below to add a folder. You must youself create a "content"
	subfolder inside this folder and populate it with pages in Markdown format -
	plus a "themes" subfolder if you want to customize your pages.
	Or click <a class="edit_personal_website" href="#">here</a>
	to create a simple personal site.
	<br /><br />
	<div id="filesPicoSiteFoldersList">
	<?php foreach($_['site_folders'] as $p){
		$parts = pathinfo($p['path']);
		$site = $parts['basename'];
		?>
		<div class="siteFolder nowrap" path="<?php print($p['path']);?>">
			<span style="float:left;width:92%;">
			<a href="<?php print(OC::$WEBROOT);?>/sites/<?php print($site);?>">
			<label><?php print($p['path']);?></label></a></span>
			<label class="remove_site_folder btn btn-flat">-</label>
			<div class="dialog" display="none"></div>
		</div>
	<?php } ?>
	</div>
	<br />
	<div class="nowrap addSiteFolder">
		<span style="float:left;width:92%;"><label></label></span><label class="add_site_folder btn btn-flat">+</label>
		<div id="chosen_site_folder" style="visibility:hidden;display:none;"></div>
		<div class="dialog" display="none">
			<div id="loadSiteFolderTree"></div>
		</div>
	</div>
</fieldset>