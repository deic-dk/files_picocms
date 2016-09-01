$(document).ready(function() {
 	$('.edit-button').click(function() {
 		var group = $('.edit-button').attr('group');
 		var id = $('.edit-button').attr('id');
		var owner = $('.edit-button').attr('user');
 		var path = $('.edit-button').attr('path');
 		var pathArr = path.split('/');
 		var file = pathArr.pop();
 		var dir = pathArr.join('/');
 		// This would require to load the files_texteditor and files_markdown js and css
 		//window.showFileEditor(dir, file);
 		window.location.href = '/index.php/apps/files/?dir='+dir+'&group='+group+'&owner='+owner+'&id='+id;
	});
});		

