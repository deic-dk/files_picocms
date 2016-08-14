<fieldset id="filesPicoSiteFolders" class="section">
	<h2><?php p($l->t('Site folders')); ?></h2>
	These folders are intended to hold files in markdown format - with extension 'md'.
	These files will be parsed and served up to browsers as html by Pico CMS.
	<br />
	<br />
	<div id="filesPicoSiteFoldersList">
	<?php foreach($_['site_folders'] as $p){ 
		?>
		<div class="siteFolder nowrap" path="<?php print($p['path']);?>">
			<span style="float:left;width:92%;"><a href="/sites/<?php print($p['path']);?>"><label><?php print($p['path']);?></label></a></span>
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