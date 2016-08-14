var remove_dialogs = [];

function create_remove_dialog(path){
	
	if(remove_dialogs[path] != undefined){
		return;
	}
	
	$("#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder[path='"+path+"'] div.dialog").text("Are you sure you want to stop serving the folder "+path+"?");
	
	remove_dialogs[path] =  $("#filesPicoSiteFolders  #filesPicoSiteFoldersList div.siteFolder[path='"+path+"'] div.dialog").dialog({
		title: "Confirm sync",
		autoOpen: false,
		resizable: true,
		height:180,
		width:320,
		modal: true,
		buttons: {
			"Go": function() {
				removeSiteFolder(path);
				$(this).dialog("close");
			},
			"Cancel": function() {
				$(this).dialog("close");
			}
		}
	});
	
}

function appendSiteDiv(folder){
	$('#filesPicoSiteFolders #filesPicoSiteFoldersList').append('<div class="siteFolder nowrap" path="'+folder+'">\
   		<span style="float:left;width:92%;">\
   		<a href="/sites/'+folder+'"><label>'+folder+'</label></a>\
   		</span>\
   		<label class="remove_site_folder btn btn-flat">-</label>\
   		<div class="dialog" display="none"></div>\
   		</div>');
}

function addSiteFolder(folder){
	
	if($("#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder[path='"+folder+"']").length>0){
		return false;
	}
	
	var sites = $("#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder");
	
	if(sites.length>0){
		// If sites already exist, we have already shared the sample site with this user
		postAddSiteFolder(folder, false);
	}
	else{
		postAddSiteFolder(folder, true);
	}
}

function postAddSiteFolder(folder, share){
	$.ajax(OC.linkTo('files_picocms','ajax/add_site_folder.php'), {
		 type:'POST',
		  data:{
			  folder: folder,
			  share_sample_site: (share?'yes':'no')
		 },
		 dataType:'json',
		 success: function(s){
			 appendSiteDiv(folder, s.folder);
		 },
		error:function(s){
			alert("Unexpected error! Perhaps name is taken.");
		}
	});
}

function removeSiteFolder(folder){
	$.ajax(OC.linkTo('files_picocms','ajax/remove_site_folder.php'), {
		 type:'POST',
		  data:{
			  folder: folder,
		 },
		 dataType:'json',
		 success: function(s){
				if(s.error){
					alert(s.error);
				}
				else{
					$("#filesPicoSiteFolders div#filesPicoSiteFoldersList div.siteFolder[path='"+folder+"']").remove();
				}
		 },
		error:function(s){
			alert("Unexpected error!");
		}
	});
}

function stripTrailingSlash(str) {
	if(str.substr(-1)=='/') {
		str = str.substr(0, str.length - 1);
	}
	if(str.substr(1)!='/') {
		str = '/'+str;
	}
	return str;
}

$(document).ready(function(){
		
	$("li").click(function(){
		$(this).css("font-weight", "bold");
	});

	choose_site_folder_dialog = $("#filesPicoSiteFolders div.addSiteFolder div.dialog").dialog({//create dialog, but keep it closed
		title: "Choose new site folder",
		autoOpen: false,
		height: 440,
		width: 620,
		modal: true,
		buttons: {
			"Choose": function() {
				folder = stripTrailingSlash($('#chosen_site_folder').text());
				addSiteFolder(folder);
				choose_site_folder_dialog.dialog("close");
			},
			"Cancel": function() {
				choose_site_folder_dialog.dialog("close");
			}
		}
	});

	$('#filesPicoSiteFolders div#filesPicoSiteFoldersList div.siteFolder .remove_site_folder').live('click', function(e){
		path = $(this).parent().attr('path');
		create_remove_dialog(path);
		remove_dialogs[path].dialog('open');
	});
	
	$('#filesPicoSiteFolders div.addSiteFolder .add_site_folder').live('click', function(){
	  choose_site_folder_dialog.dialog('open');
	  //choose_site_folder_dialog.load("/apps/chooser/");
	  choose_site_folder_dialog.show();
		$('#loadSiteFolderTree').fileTree({
			//root: '/',
			script: '../../apps/chooser/jqueryFileTree.php',
			//script: '../../apps/files_sharding/jqueryFileTree.php',
			multiFolder: false,
			selectFile: false,
			selectFolder: true,
			folder: '/',
			file: ''
		},
		// single-click
		function(file) {
			$('#chosen_site_folder').text(file);
		},
		// double-click
		function(file) {
			if(file.indexOf("/", file.length-1)!=-1){// folder double-clicked
				addSiteFolder(file);
				choose_site_folder_dialog.dialog("close");
			}
		});
	});

	$("#filesPicoCMSPersonalSettings #sites-info").on("click", function () {
		if($('.sites-help').length){
			return false;
		};
		var html = "<div><h3>Setting up a personal website</h3>\
				<a class='oc-dialog-close close svg'></a>\
				<div class='sites-help'></div></div>";
		$(html).dialog({
			  dialogClass: "oc-dialog",
			  resizeable: true,
			  draggable: true,
			  modal: false,
			  height: 600,
			  width: 720,
				buttons: [{
					"id": "sites_info",
					"text": "OK",
					"click": function() {
						$( this ).dialog( "close" );
					}
				}]
			});

		$('.oc-dialog-close').live('click', function() {
			$(".oc-dialog").remove();
		});

		$('.ui-helper-clearfix').css("display", "none");

		$.ajax(OC.linkTo('files_picocms', 'ajax/get_help.php'), {
			type: 'GET',
			success: function(jsondata){
				if(jsondata) {
					$('.sites-help').html(jsondata.data.page);
				}
			},
			error: function(data) {
				alert("Unexpected error!");
			}
		});

		
	}); 


});
