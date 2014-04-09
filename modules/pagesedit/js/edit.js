/*
 * edit.js: Main editing javascript file for page editing
 */

// Variables
var themes_select = "";
var template_select = "";
var default_theme = "";
var default_template = "";

// Do things on load
$(function(){
	// Add button to header edit menu
	$('<font>&nbsp;|&nbsp;</font><a class="toolbar-link" id="backend-pages-button" href="#">Pages</a>').insertAfter('#backend-edit-button');
});

// Open up editing popup
$(document).on('click','#backend-pages-button',function(){
	// Display editing popup
	$('#editing-popup-background').show();
	$('#editing-popup').show();
		
	// Add edit bar
	$('#edit-content-selection').hide();
	$('.editing-buttons').hide();
	
	// Check if add page button exists
	if($('#edit-button-add_page').length && $('#edit-button-page_close').length) {
		$('#edit-button-add_page').show(); 
		$('#edit-button-page_close').show();
	} else {
		$('#edit-bar').prepend('<button id="edit-button-add_page" type="button" class="editing-buttons" style="float:left;">Add page</button>');
		$('#edit-bar').append('<button id="edit-button-close" type="button" class="editing-buttons" style="float:right;">Close</button>');
	}
	
	// Set content area value
	curContentArea = 'none';
	
	// Get information from the database
	$.ajax({
		type:'GET',
		url:'handlers/edit/ajax.php',
		data:{pageid:curPageID,contentArea:curContentArea,contentType:'pagesedit'},
		async:false,
		dataType:"xml"
	}).done(function(data){
		$('#editing-content').html("<table><tr><th>Controls</th><th>ID-String</th><th>Title</th><th>Theme</th><th>Template</th></tr></table>");
		themes_select = "<select class='theme-select'>";
		template_select = "";
		$(data).find('themes').find('theme').each(function(){
			themes_select += "<option value='"+$(this).attr('name')+"'>"+$(this).attr('name')+"</option>";	
			template_select += "<select class='template-select theme-"+$(this).attr('name')+"'>";
			$(this).find('template').each(function(){
				template_select += "<option value='"+$(this).text()+"'>"+$(this).text()+"</option>";
			});
			template_select += "</select>";
		});
		themes_select += "</select>";
		var pages = $(data).find('pages');
		pages.find('page').each(function(){
			var row = "<tr class='pages-row' id='page_"+$(this).attr('id')+"'>";
			row += ($(this).find('id_string').text() == 'home' ? "<td></td>" : "<td><a class='pages-delete' href='#'>Delete</a></td>");
			row += "<td><input class='pages-id' type='text' value='"+$(this).find('id_string').text()+"' "+($(this).find('id_string').text() == 'home' ? "disabled='disabled'" : "")+"/></td>";
			row += "<td><input class='pages-title' type='text' value='"+$(this).find('title').text()+"' /></td>";
			row += "<td>"+themes_select+"</td><td>"+template_select+"</td>";
			row += "</tr>";
			$('#editing-content table').append(row);
			$('#page_'+$(this).attr('id')+' .theme-select').val($(this).find('theme').text());
			$('#page_'+$(this).attr('id')+' .theme-'+$(this).find('theme').text()).show();
			$('#page_'+$(this).attr('id')+' .theme-'+$(this).find('theme').text()).val($(this).find('template').text());
			if($(this).find('id_string').text() == 'home') {
				default_theme = $(this).find('theme').text();
				default_template = $(this).find('template').text();
			}
		});
	});

	return false;
});

$(document).on('click','.pages-delete',function(){
	var row = $(this).parents('tr:first');
	$.ajax({
		type:'POST',
		url:'modules/pagesedit/db_changes.php',
		data:{pageid:$(this).parents('tr:first').attr('id').split("_")[1],action:'delete',content:'1'},
		async:false
	}).done(function(data){
		row.remove();
	});
});

$(document).on('change','.pages-id,.pages-title,.template-select,.theme-select',function(){
	var contentVal='';
	if($(this).hasClass("pages-id")) {
		var regex = /[^a-z0-9-_ ]/;
		if(regex.test($(this).val())) {
			alert("Only lowercase letters, numbers, dash and undscore is allowed in ID-string.");
			return false;
		}
		contentVal = "pageID_string="+$(this).val().replace(/ /g,"_");;
	} else if($(this).hasClass("pages-title")) {
		contentVal = "page_title="+encodeURIComponent($(this).val());
	} else if($(this).hasClass("template-select")) {
		contentVal = "template_name="+$(this).val();
	} else if($(this).hasClass("theme-select")) {
		contentVal = "theme_name="+$(this).val();
	}
	$.ajax({
		type:'POST',
		url:'modules/pagesedit/db_changes.php',
		data:{pageid:$(this).parents('tr:first').attr('id').split("_")[1],action:'change',content:contentVal},
		async:false
	})
});

$(document).on('input','.pages-id,.pages-title',function(){
	var contentVal='';
	if($(this).hasClass("pages-id")) {
		var regex = /[^a-z0-9-_ ]/;
		if(regex.test($(this).val())) {
			alert("Only lowercase letters, numbers, dash and undscore is allowed in ID-string.");
			return false;
		}
		contentVal = "pageID_string="+$(this).val().replace(/ /g,"_");;
	} else if($(this).hasClass("pages-title")) {
		contentVal = "page_title="+encodeURIComponent($(this).val());
	}
	$.ajax({
		type:'POST',
		url:'modules/pagesedit/db_changes.php',
		data:{pageid:$(this).parents('tr:first').attr('id').split("_")[1],action:'change',content:contentVal},
		async:false
	})
});

$(document).on('click','#edit-button-add_page',function(){
	$.ajax({
		type:'POST',
		url:'modules/pagesedit/db_changes.php',
		data:{pageid:-1,action:'new',content:'none'}
	}).done(function(data){
		if(data != 'FAIL') {
			var table = $('#editing-content table')
			var row = "<tr class='pages-row' id='page_"+data+"'>";
			row += "<td><a class='pages-delete' href='#'>Delete</a></td>";
			row += "<td><input class='pages-id' type='text' value='new_page'/></td>";
			row += "<td><input class='pages-title' type='text' value='Page-Title' /></td>";
			row += "<td>"+themes_select+"</td><td>"+template_select+"</td>";
			row += "</tr>";
			table.append(row);
			$('#page_'+data+' .theme-select').val(default_theme);
			$('#page_'+data+' .theme-'+default_theme).show();
			$('#page_'+data+' .theme-'+default_theme).val(default_template);
		}
	});
});
