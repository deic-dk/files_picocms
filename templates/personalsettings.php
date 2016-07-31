<fieldset id="filesPicoDataFolders" class="section">
	<h2><?php p($l->t('Data folders')); ?></h2>
	These folders are intended to hold data: Their content is NOT synchronized.<br />
	Any files put in them locally, will be uploaded and then deleted locally.
	<br />
	<br />
	<div id="filesPicoSiteFoldersList">
	<?php foreach($_['site_folders'] as $p){ 
		$path = $p['folder'];
		?>
		<div class="siteFolder nowrap" path="<?php print($path);?>">
			<span style="float:left;width:92%;"><label><?php print($path);?></label></span><label class="remove_site_folder btn btn-flat">-</label>
			<div class="dialog" display="none"></div>
		</div>
	<?php } ?>
	</div>
	<br />
	<div class="nowrap addSiteFolder">
		<span style="float:left;width:92%;"><label></label></span><label class="add_site_folder btn btn-flat">+</label>
		<div id="chosen_folder" style="visibility:hidden;display:none;"></div>
		<div class="dialog" display="none">
			<div id="loadFolderTree"></div>
			<div id="file" style="visibility: hidden; display:inline;"></div>
		</div>
	</div>
</fieldset>