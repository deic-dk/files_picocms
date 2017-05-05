<?php 
$user = \OCP\USER::getUser();
$websitesFolder = "/websites";
$folder = preg_replace("/[@|\.]/", "_", $user);
$folder = $websitesFolder. "/" . $folder;
?>

<p>Your website will be created in the folder <label id="personal_site_folder" folder="<?php print($folder);?>">
<?php print($folder);?></label>&nbsp;<a href="" id="change_site_folder" style="color: #2a6496;">change</a></p>

<div>Template:</div>
<div>
	<input type="radio" name="pico_content" value="/samplesite/content-sample_blog" checked />Profile/blog<br />
	<input type="radio" name="pico_content" value="/samplesite/content" />PicoCMS
</div>
<br />
<div>Theme:</div>
<div>
	<input type="radio" name="pico_theme" value="deic-blog" checked />Blog<br />
	<input type="radio" name="pico_theme" value="deic-doc" />Documentation<br />
	<input type="radio" name="pico_theme" value="default" />PicoCMS
</div>
<br />
<p>After clicking "Continue", your website will be created and you will be redirected to
your new site. If you choose "Profile/blog" as your template, you can edit pages by clicking
"Show file".</p>

