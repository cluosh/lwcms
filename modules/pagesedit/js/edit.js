/*
 * edit.js: Main editing javascript file for page editing
 */

// Do things on load
$(function(){
	// Add button to header edit menu
	$('#backend-left-menu').append('&nbsp;|&nbsp;<a class="toolbar-link" id="backend-pages-button" href="#">Pages</a>');
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
		$('#edit-bar').append('<button id="edit-button-page_close" type="button" class="editing-buttons" style="float:right;">Close</button>');
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
		var themes_select = "<select class='theme-select'>";
		var template_select = "";
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
		});
	});

	return false;
});

$(document).on('click','.pages-delete',function(){
	$.ajax({
		type:'POST',
		url:'modules/pagesedit/db_changes.php',
		data:{pageid:$(this).parents('tr:first').attr('id').split("_")[1],action:'delete',content:'1'},
		async:false
	}).done({
		$(this).parents('tr:first').remove();
	});
});

$(document).on('change','.pages-id,.pages-title,.template-select,.theme-select',function(){
	$.ajax({
		type:'POST',
		url:'modules/pagesedit/db_changes.php',
		data:{pageid:$(this).parents('tr:first').attr('id').split("_")[1],action:'delete',content:'1'},
		async:false
	}).done({
		$(this).parents('tr:first').remove();
	});
});
