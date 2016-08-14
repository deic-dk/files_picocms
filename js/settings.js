$(document).ready(function() {
 	$('#sampleDirSubmit').click(function() {
		owner = $('#sampleDirOwner').val();
		path = $('#sampleDirPath').val();
		$.ajax(OC.linkTo('files_picocms','ajax/set_sample_folder.php'), {
			 type:'POST',
			  data:{
			  	'owner': owner,
			  	'path': path
			 },
			 dataType:'json',
			 success: function(data){
				 OC.msg.finishedSaving('#ownerChange', {status: 'success', data: {message:  'Sample dir/owner changed'}});
			 },
			error:function(data){
				alert("Unexpected error!");
			}
		});
	});
});		

