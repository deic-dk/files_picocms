<?php 
$user = \OCP\USER::getUser();
//$websitesFolder = "/websites";
//$folder = "/websites/" . preg_replace("/[@|\.]/", "_", $user);
$publicFolder = "/public";
$email = \OCP\Config::getUserValue($user, 'settings', 'email');
$master = \OCA\FilesSharding\Lib::getMasterURL();
$publicUrl = $master."users/".$email."/";
$randomName = md5(uniqid(rand(), true));
?>
<p>Click "Continue" to create your website be redirected to
your new site. Notice that any existing site/files will be overwritten.
If you choose type "Blog" type, you can edit pages by clicking "Show file".</p>
<!--<p>Notice that if any file (e.g. "my_file.md") in the folder "/public/" (which you can simply create), with a
name ending in ".md" or ".html" will be served under <?php print($publicUrl);?> (as e.g. <?php print($publicUrl);?>my_file).</p>-->
<div>Type:</div>
<div>
	<input type="radio" name="pico_type" theme="deic-wiki" copyThemes="yes"
		content="/samplesite/content-sample_wiki" destination="content" folder="/wiki"
		site_name="<?php print($randomName);?>" />Wiki<br />
	<input type="radio" name="pico_type" theme="deic-blog" copyThemes="no"
		content="/samplesite/content-sample_blog/profile.md" destination="index.md"
		folder="<?php print($publicFolder);?>" site_name="" checked />Single public profile page<br />
	<input type="radio" name="pico_type" theme="deic-blog" copyThemes="yes"
		content="/samplesite/content-sample_blog" destination="content"
		folder="/blog" site_name="<?php print($email);?>"/>Blog (includes profile page)<br />
	<input type="radio" name="pico_type" theme="deic-doc" copyThemes="yes"
		content="/samplesite/content" destination="content" folder="/documentation"
		site_name="<?php print($randomName);?>" />Documentation<br />
	<input type="radio" name="pico_type" theme="default"copyThemes="yes" 
		content="/samplesite/content" destination="content" folder="/website"
		site_name="<?php print($randomName);?>" />PicoCMS default
</div>
<br />
<p>Your website will be created in <label id="personal_site_folder"><?php print($publicFolder);?></label>&nbsp;<a href="" id="change_site_folder" style="color: #2a6496;">change</a></p>
<br />

