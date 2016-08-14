<fieldset id="picoSettings" class="section">

<h2><?php p($l->t('Pico CMS'));?></h2>

<?php
echo "<label>Owner of sample site folder</label>
<input type='text' id='sampleDirOwner' value=\"".$_['samplesiteowner']."\" original-title='' />
<label>Path of sample site folder</label>
<input type='text' id='sampleDirPath' value=\"".$_['samplesitepath']."\" original-title='' />
<input type='submit' value='Save' id = 'sampleDirSubmit' original-title='' />
<span id='ownerChange' class='msg'></span>";
?>

</fieldset>

