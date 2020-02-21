$(document).ready(function() {
 	$('.edit-button').click(function() {
 		var group = $('.edit-button').attr('group');
 		var id = $('.edit-button').attr('id');
 		var parentid = $('.edit-button').attr('dir_id');
		var owner = $('.edit-button').attr('owner');
		var user = $('.edit-button').attr('user');
 		var path = $('.edit-button').attr('path');
 		var userhomeurl = $('.edit-button').attr('host');
 		var pathArr = path.split('/');
 		var file = pathArr.pop();
 		var dir = pathArr.join('/');
 		// NOTICE: To trigger opening the file, we call the files app with the id of the parent dir and the name of the file
 		window.location.href = userhomeurl+'/index.php/apps/files/?dir='+dir+'&group='+group+'&owner='+(user==owner?'':owner)+'&id='+
 			(user==owner?'':parentid)+'&file='+encodeURIComponent(file);
	});
});		

