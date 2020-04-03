var remove_dialogs = [];

function create_remove_dialog(path){
	
	if(remove_dialogs[path] != undefined){
		return;
	}
	
	$("#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder[path='"+path+"'] div.dialog").text("Are you sure you want to stop serving the folder "+path+"?");
	
	remove_dialogs[path] =  $("#filesPicoSiteFolders  #filesPicoSiteFoldersList div.siteFolder[path='"+path+"'] div.dialog").dialog({
		title: "Confirm unserve",
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

function getMasterURL(callback){
	$.ajax(OC.linkTo('files_sharding', 'ajax/get_master_url.php'), {
		type: 'GET',
		success: function(data){
			if(data) {
				callback(data.url);
			}
		},
		error: function(data) {
			alert("Unexpected error!");
		}
	});
}

function addSiteDiv(folder, name, rename, placeholder){
	getMasterURL(function(url){
		if(rename){
			$('#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder[path="'+folder+'"] a.site_url').attr('href', url+'sites/'+name);
		}
		else{
			$('#filesPicoSiteFolders #filesPicoSiteFoldersList').append('<div class="siteFolder nowrap'+(placeholder?' placeholder':'')+'" path="'+folder+'">\
		   		<span style="float:left;width:46%;">\
		   		<a href="/index.php/apps/files/?dir='+folder+'"><label class="folder" style="margin-top: 6px;">'+folder+'</label></a>\
		   		</span>\
		   		<span style="float:left;width:46%;">\
		   		<a class="site_url" href="'+url+'sites/'+name+'"><label>'+url+'sites/</label></a><input type="text" value="'+name+'">\
		   		</span>\
		   		<label class="remove_site_folder btn btn-flat" style="margin: 5.7px 0 0 0;">-</label>\
		   		<div class="dialog" display="none"></div>\
		   		</div>');
		}

	});
}

function addSiteFolder(folder, name){
	if(typeof(name)==='undefined'){
		name = folder.substring(folder.lastIndexOf('/') + 1);
	}
	var rename = false;
	if($("#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder[path='"+folder+"']").not('.placeholder').length>0){
		rename = true;
	}
	// When called from Personal web site wizard, don't add site
	if($(".oc-dialog-personal_website:visible").length>0){
		return false;
	}
	var sites = $("#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder");
	if(sites.length>0){
		// If sites already exist, we have already shared the sample site with this user
		postAddSite(folder, name, false, rename);
	}
	else{
		postAddSite(folder, name, true, rename);
	}
}

function postAddSite(folder, name, share, rename){
	$.ajax(OC.linkTo('files_picocms','ajax/add_site.php'), {
		 type:'POST',
		  data:{
			  folder: folder,
			  name: name,
			  share_sample_site: (share?'yes':'no'),
			  rename: (rename?'yes':'no')
		 },
		 dataType:'json',
		 success: function(s){
			if(s.error){
				addSiteDiv(folder, '', false, true);
					OC.dialogs.alert(t("files_picocms", "Name already taken")+": "+folder.replace(/.*\/([^\/]+$)/, '$1')+". "+
							t("files_picocms", "Please choose another")+".", t("files_picocms", "Serving website failed"), function(){}, true);
			}
			else{
				addSiteDiv(folder, name, rename, false);
				$("#filesPicoSiteFolders #filesPicoSiteFoldersList div.siteFolder.placeholder[path='"+folder+"']").remove();
			}
		},
		error:function(s){
			alert(t("files_picocms","Unexpected error! Perhaps name is taken."));
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
		data: {folder: $('#personal_site_folder').text().trim(),
			name: $('input[name="pico_type"]:checked').attr('site_name'),
			content: $('input[name="pico_type"]:checked').attr('content'),
			destination: $('input[name="pico_type"]:checked').attr('destination'),
			theme: $('input[name="pico_type"]:checked').attr('theme'),
			copy_themes: $('input[name="pico_type"]:checked').attr('copyThemes')},
		success: function(jsondata){
			if(jsondata.error){
				$('#personal_site').html('Continue');
				OC.dialogs.alert("Error creating website: "+jsondata.error, "Creating website failed", function(){}, true);
			}
			else{
				if(jsondata.site=='public'){
					if($('input#serve_public_url').attr('checked')!='checked'){
						OC.dialogs.alert('First check off "Serve /public..."', 'Cannot serve', function(){}, true);
					}
					else{
						window.location.href = $('#public_site_url').attr('href');
					}
				}
				else{
					window.location.href = OC.webroot+ '/sites/'+jsondata.site;
				}

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

var choose_site_folder_dialog;

$(document).ready(function(){
		
	$("li").click(function(){
		$(this).css("font-weight", "bold");
	});
	
	var buttons = {};
	buttons[t("files_picocms", "Choose")] = function() {
		folder = $('#chosen_site_folder').text();
		if(folder){
			addSiteFolder(folder);
		}
		choose_site_folder_dialog.dialog("close");
 	};
 	buttons[t("files_picocms", "Cancel")] = function() {
		choose_site_folder_dialog.dialog("close");
	};
	choose_site_folder_dialog = $("#filesPicoSiteFolders div.addSiteFolder div.dialog").dialog({//create dialog, but keep it closed
		title: "Choose new site folder",
		autoOpen: false,
		height: 440,
		width: 620,
		modal: true,
		dialogClass: "chooseSiteFolder",
		buttons: buttons
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
		help_dialog=$(html).dialog({
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
							createPersonalSite(function(){self.dialog("isOpen") && self.dialog( "destroy" );});
						}
					},{
						"id": "cancel",
						"text": "Cancel",
						"click": function() {
							$( this ).dialog( "destroy" );
						}
				}]
			});
		
		help_dialog.on('dialogclose', function(event) {
			$('.ui-dialog.oc-dialog-personal_website').remove();
	 });
		
		$('body').append('<div class="modalOverlay">');
		
		$('.oc-dialog-close').remove();
		
		//$('.ui-helper-clearfix').css("display", "none");
		pico_folder_chosen = false;
		$.ajax(OC.linkTo('files_picocms', 'ajax/get_help.php'), {
			type: 'GET',
			success: function(jsondata){
				if(jsondata) {
					$('.sites-help').html(jsondata.data.page);
					$('#change_site_folder').click( function(ev){
						ev.stopPropagation();
						ev.preventDefault();
						pico_folder_chosen = true;
						chooseSiteFolder(function(file){$('#personal_site_folder').text(file);}, function(file){$('#personal_site_folder').text(file);});
					});
					$('input[name=pico_type]').click(function(ev){
						if(!pico_folder_chosen){
							$('#personal_site_folder').text($(ev.target).attr('folder'))
						}
					});
					$('#personal_site_folder').text($($('input[name=pico_type]:checked')).attr('folder'));
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
	
	$('#serve_public_url').live('click', function(ev){
		ev.stopPropagation();
		ev.preventDefault();
		var checked = $(ev.target).attr('checked');
		var serve = typeof checked=='undefined'?'no':'yes';
		$.ajax(OC.linkTo('files_picocms', 'ajax/set_serve_public_url.php'), {
			type: 'GET',
			data: {serve: serve},
			success: function(jsondata){
				$(ev.target).prop('checked', jsondata.data.serve==='yes'?'checked':'');
			},
			error: function(data) {
				alert("Unexpected error!");
			}
		});
	});
	
	$(document).on('change', '.siteFolder input', function(ev) {
		var name = $(ev.target).val();
		var folder = $(ev.target).closest('div.siteFolder').find('label.folder').first().text();
		addSiteFolder(folder, name);
	});
	
	/*getMasterURL(function(url){
		$('.site_url label').each(
			function(el){$(this).text(url+'sites/'+$(this).text())}
		);
	});*/

});
