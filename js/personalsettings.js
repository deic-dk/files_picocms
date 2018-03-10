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
		alert(folder);
		return false;
	}
	// When called from Personal web site wizard, don't add site
	if($(".oc-dialog-personal_website:visible").length>0){
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
			 if(s.error){
					OC.dialogs.alert("Error serving website: name already taken: "+folder.replace(/.*\/([^\/]+$)/, '$1'), "Serving website failed", function(){}, true);
			 }
			 else{
				 appendSiteDiv(folder, s.folder);
			 }
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
					alert('Could not remove site folder. '+s.error);
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

function addSpinner(){
	var spinner = '<img src="'+ OC.imagePath('core', 'loading-small.gif') +'">';
	$('#personal_site').html(spinner);
	$('#personal_site').css('width', '68px');
}

function createPersonalSite(callback) {
	addSpinner();
	$.ajax(OC.linkTo('files_picocms', 'ajax/create_personal_site.php'), {
		type: 'GET',
		data: {folder: $('#personal_site_folder').text().trim(), content: $('input[name="pico_content"]:checked').val(),
			theme: $('input[name="pico_theme"]:checked').val()},
		success: function(jsondata){
			if(jsondata.error){
				$('#personal_site').html('Continue');
				OC.dialogs.alert("Error creating website: "+jsondata.error, "Creating website failed", function(){}, true);
			}
			else{
				window.location.href = OC.webroot+ '/sites/'+jsondata.site+
				($('input[name="pico_content"]:checked').val()=='/samplesite/content-sample_blog'?'/profile':'');
			}
		},
		error: function(data) {
			alert("Unexpected error!");
			callback();
		}
	});
};

function chooseSiteFolder(callback1, callback2){
  choose_site_folder_dialog.dialog('open');
  //choose_site_folder_dialog.load("/apps/chooser/");
  choose_site_folder_dialog.show();
  $('.chooseSiteFolder').css('z-index', '200');
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
		callback1(stripTrailingSlash(file));
	},
	// double-click
	function(file) {
		if(file.indexOf("/", file.length-1)!=-1){// folder double-clicked
			callback2(stripTrailingSlash(file));
			choose_site_folder_dialog.dialog("close");
		}
	});
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
		dialogClass: "chooseSiteFolder",
		buttons: {
			"Choose": function() {
				folder = $('#chosen_site_folder').text();
				if(folder){
					addSiteFolder(folder);
				}
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
	
	$('.add_site_folder').live('click', function(){
		chooseSiteFolder(function(file){$('#chosen_site_folder').text(file);}, addSiteFolder);
	});
	
	$(".edit_personal_website").on("click", function (ev) {
		ev.stopPropagation();
		ev.preventDefault();
		if($('.sites-help').is(':visible')){
			return false;
		};
		var html = "<div><h3>Website wizard</h3>\
				<a class='oc-dialog-close close svg'></a>\
				<div class='sites-help'></div></div>";
		var self = $( this );
		$(html).dialog({
			  dialogClass: "oc-dialog-personal_website",
			  resizeable: true,
			  draggable: true,
			  modal: false,
			 // height: 480,
			  width: 580,
				buttons: [{
					"id": "personal_site",
					"text": "Continue",
					"click": function() {
							createPersonalSite(function(){self.dialog( "close" );});
						}
					},{
						"id": "cancel",
						"text": "Cancel",
						"click": function() {
							$( this ).dialog( "close" );
						}
				}]
			});
		
		$('body').append('<div class="modalOverlay">');
		
		$('.oc-dialog-close').remove();
		
		//$('.ui-helper-clearfix').css("display", "none");

		$.ajax(OC.linkTo('files_picocms', 'ajax/get_help.php'), {
			type: 'GET',
			success: function(jsondata){
				if(jsondata) {
					$('.sites-help').html(jsondata.data.page);
					$('#change_site_folder').live('click', function(ev){
						ev.stopPropagation();
						ev.preventDefault();
						chooseSiteFolder(function(file){$('#personal_site_folder').text(file);}, function(file){$('#personal_site_folder').text(file);});
					});
				}
			},
			error: function(data) {
				alert("Unexpected error!");
			}
		});
		
		$('.oc-dialog-personal_website').css('z-index', '200');
		$('#colorbox').css('z-index', '100');
		$('#cboxOverlay').css('z-index', '100');
		
	}); 

});
