$(document).ready(function() {
 	$('.edit-button').click(function() {
 		var group = $('.edit-button').attr('group');
 		var id = $('.edit-button').attr('id');
 		var parentid = $('.edit-button').attr('parentid');
		var owner = $('.edit-button').attr('owner');
 		var path = $('.edit-button').attr('path');
 		var pathArr = path.split('/');
 		var file = pathArr.pop();
 		var dir = pathArr.join('/');
 		// This would require to load the files_texteditor and files_markdown js and css
 		//window.showFileEditor(dir, file);
 		window.location.href =(dir==''?'/themes/deic_theme_oc7/apps/files/ajax/download.php?dir=/&files='+file+'&id='+id+'&owner='+owner:
 			'/index.php/apps/files/?dir='+dir+'&group='+group+'&owner='+owner+'&id='+parentid);
	});
});		

