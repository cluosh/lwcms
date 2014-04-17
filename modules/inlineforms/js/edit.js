/*
 * edit.js: Main background editing tool for inline forms
 */

// Function for updating hidden data
function updateInlineformsData() {
	$('.edit-form').each(function(){
		var data = $(this).find('.form-name input').val()+"==";
		// Fields
		$(this).find('.field-row').each(function(){
			var name = $(this).find('.field-name').html();
			if($(this).find('.field-type').val() == 'radio') 
				data += encodeURIComponent(name)+"="+encodeURIComponent(name)+"="+($(this).find('.field-required').is(':checked') ? "1" : "0")+"="+$(this).find('.field-type').val()+"="+($(this).find('.field-display-name').is(':checked') ? "1" : "0")+"="+encodeURIComponent($(this).find('.radio-options').val())+";";
			else
				data += encodeURIComponent(name)+"="+encodeURIComponent(name)+"="+($(this).find('.field-required').is(':checked') ? "1" : "0")+"="+$(this).find('.field-type').val()+"="+($(this).find('.field-display-name').is(':checked') ? "1" : "0")+";";
		});
		data += "==";
		// Preferences
		data += ($(this).find('.before-text .left_column').html() != "" ? "before_left="+encodeURIComponent($(this).find('.before-text .left_column').html())+";" : "");
		data += ($(this).find('.before-text .right_column').html() != "" ? "before_right="+encodeURIComponent($(this).find('.before-text .right_column').html())+";" : "");
		data += ($(this).find('.after-text .left_column').html() != "" ? "after_left="+encodeURIComponent($(this).find('.after-text .left_column').html())+";" : "");
		data += ($(this).find('.after-text .right_column').html() != "" ? "after_right="+encodeURIComponent($(this).find('.after-text .right_column').html())+";" : "");
		data += ($(this).find('.form-db').is(':checked') ? "db=1;" : "");
		data += ($(this).find('.form-captcha').is(':checked') ? "captcha=1;" : "");
		data += ($(this).find('.form-email').val() != "" ? "email="+encodeURIComponent($(this).find('.form-email').val())+";" : "");
		data += ($(this).find('.success-text .success-text-field').html() != "" ? "success_text="+encodeURIComponent($(this).find('.success-text .success-text-field').html())+";" : "");
		// Dyn-Data
		if($(this).find('.dyn-data').length) {
			data += "==";
			data += $(this).find('.dyn-data').val();
		}
		$(this).children('.form-hidden-data').val(data);
	});
}

// Function for sending values to the database
function valueAssign_inlineforms() {
	updateInlineformsData();
	curContentType = 'inlineforms';
	var data = "";
	$('.form-hidden-data').each(function(){
		data += $(this).val()+"!!";
	});
	return data;
}
 
// Function for displaying inline forms overview
function getInlineformsOverview() {
	// Display editing popup
	$('#editing-popup-background').show();
	$('#editing-popup').show();
		
	// Add edit bar
	$('#edit-content-selection').hide();
	$('.editing-buttons').hide();
	$('#edit-button-accept').show();
	$('#edit-button-discard').show();
	
	// Check if inlineforms button exists
	if($('#edit-button-add_form').length) {
		$('#edit-button-add_form').show(); 
	} else {
		$('#edit-bar').prepend('<button id="edit-button-add_form" type="button" class="editing-buttons" style="float:left;">Add form</button>');
	}
	
	// Set Assign Func
	globalAssignFunc = window['valueAssign_inlineforms'];
	
	// Set content area value
	curContentArea = 'none';
	curContentType = 'inlineforms';
	
	// Get information from the database
	$.ajax({
		type:'GET',
		url:'handlers/edit/ajax.php',
		data:{pageid:curPageID,contentArea:curContentArea,contentType:'inlineforms'},
		async:false,
		dataType:"xml"
	}).done(function(data){
		// Evaluate form data
		$('#editing-content').html("");
		$(data).find("form").each(function(){
			var data = "<div class='edit-form'>";
			data += "<div class='form-info'><div class='form-name'><input type='text' value='"+$(this).attr("name")+"' /></div><div class='expandbox'><a class='form-info-expand' href=''>#</a><a class='form-delete' href=''>#</a></div></div>";
			// Get preferences
			var pref = $(this).find('pref');
			data += "<div class='form-preferences'><table>";
			data += "<tr><td>Use CAPTCHA:&nbsp;<input type='checkbox' class='form-captcha' "+(pref.find('captcha').text() == '1' ? "checked='checked'" : "")+"/></td><td></td><td></td></tr>";
			data += "<tr><td>Save data to DB:&nbsp;<input type='checkbox' class='form-db' "+(pref.find('db').text() == '1' ? "checked='checked'" : "")+"/></td><td colspan='2'>Send data to e-mail (sperarate addresses with ';'):&nbsp;<input type='input' class='form-email' value='"+decodeURIComponent(pref.find('email').text()).replace(/\+/g, ' ')+"'/></td></tr>";
			data += "<tr><td><div style='float:left;'>Text before form</div>&nbsp;<a class='form-text-expand before' href=''>#</a></td><td><div style='float:left;'>Text after form</div>&nbsp;<a class='form-text-expand after' href=''>#</a></td><td><div style='float:left;'>Success text</div>&nbsp;<a class='form-text-expand success' href=''>#</a></td>";
			data += "<tr class='before-text'><td><div style='float:left;'>Left column</div><br /><div contenteditable='true' class='left_column new-edit'>"+decodeURIComponent(pref.find('before_left').text()).replace(/\+/g, ' ')+"</div></td><td colspan='2'><div style='float:left;'>Right column</div><br /><div contenteditable='true' class='right_column new-edit'>"+decodeURIComponent(pref.find('before_right').text()).replace(/\+/g, ' ')+"</div></td></tr>";
			data += "<tr class='after-text'><td><div style='float:left;'>Left column</div><br /><div contenteditable='true' class='left_column new-edit'>"+decodeURIComponent(pref.find('after_left').text()).replace(/\+/g, ' ')+"</div></td><td colspan='2'><div style='float:left;'>Right column</div><br /><div contenteditable='true' class='right_column new-edit'>"+decodeURIComponent(pref.find('after_right').text()).replace(/\+/g, ' ')+"</div></td></tr>";
			data += "<tr class='success-text'><td colspan='3'><div style='float:left;'>Success text</div><br /><div contenteditable='true' class='success-text-field new-edit'>"+decodeURIComponent(pref.find('success_text').text()).replace(/\+/g, ' ')+"</div></td></tr>";
			data += "</table></div>";
			// Get fields
			data += "<div class='form-fields'><table><tr><th>Name</th><th>Type</th><th>Preferences</th><th>Controls</th></tr>";
			$(this).find('fields').find('field').each(function(){
				data += "<tr class='field-row'>";
				data += "<td><div contenteditable class='field-name new-edit'>"+decodeURIComponent($(this).find('name').text()).replace(/\+/g, ' ')+"</div></td>";
				data += "<td><select class='field-type'>";
				data += "<option value='text' "+($(this).find('type').text() == 'text' ? "selected='selected'" : "")+">Textfield</option>";
				data += "<option value='password' "+($(this).find('type').text() == 'password' ? "selected='selected'" : "")+">Password</option>";
				data += "<option value='textarea' "+($(this).find('type').text() == 'textarea' ? "selected='selected'" : "")+">Textarea</option>";
				data += "<option value='email' "+($(this).find('type').text() == 'email' ? "selected='selected'" : "")+">E-Mail</option>";
				data += "<option value='checkbox' "+($(this).find('type').text() == 'checkbox' ? "selected='selected'" : "")+">Checkbox</option>";
				data += "<option value='radio' "+($(this).find('type').text() == 'radio' ? "selected='selected'" : "")+">Radio-Buttons</option>";
				data += "<option value='payment' "+($(this).find('type').text() == 'payment' ? "selected='selected'" : "")+">Bank-Data / Payment</option>";
				data += "<option value='dynamic' "+($(this).find('type').text() == 'dynamic' ? "selected='selected'" : "")+">Dynamic</option>";
				data += "</select></td>";
				data += "<td><input type='checkbox' class='field-required' "+($(this).find('req').text() == '1' ? "checked='checked'" : "")+"/>&nbsp;Required field<br /><input type='checkbox' class='field-display-name' "+($(this).find('display_name').text() == '1' ? "checked='checked'" : "")+"/>&nbsp;Display name"+($(this).find('type').text() == 'radio' ? "<br />Options (separate with ';;'):&nbsp;<input type='text' class='radio-options' value='"+$(this).find('radio_options').text()+"'/>" : "")+"</td>";
				data += "<td><a href='#' class='form-field-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='form-field-down'>Down</a>&nbsp;|&nbsp;<a href='#' class='form-field-delete'>Delete</a></td>";
				data += "</tr>";
			});
			data += "</table><button class='edit-form-add-field' type='button'>Add field</button></div>";
			// Dyn data area
			if(pref.find('dyn').length) {
				var xml = $(this).find('dyn').text();
				data += "<div class='dyndata-edit' id='"+$(this).attr("name")+"-dyn-data'><input type='hidden' class='dyn-data' value='"+xml+"' /></div>";
				$.getScript("forms/"+$(this).attr("name")+"/js/edit.js");
			}
			// Close open tags
			data += "<input class='form-hidden-data' type='hidden' value='NO_CHANGES' />";
			data += "</div>";			
			$('#editing-content').append(data);
			$('.new-edit').each(function(){
				$(this).ckeditor({
					filebrowserBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor',
					filebrowserImageBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=image',
					filebrowserFlashBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
					coreStyles_bold: { element: 'b' },
					coreStyles_italic: { element: 'i' },
					enterMode:3
				});
				$(this).removeClass('new-edit');
			});
		});
	});
}

// Do things on load
$(function(){
	// Add button to header edit menu
	$('#backend-left-menu').append('&nbsp;|&nbsp;<a class="toolbar-link" id="backend-inlineforms-button" href="#" title="Add/edit/delete forms.">Forms</a>');
});

$(document).on('click','.form-info-hide,.form-info-expand,.form-delete',function(){
	var info = $(this).parents('div.form-info');
	if($(this).hasClass('form-info-hide')) {
		info.siblings('div').hide();
		$(this).addClass('form-info-expand');
		$(this).removeClass('form-info-hide');
	} else if($(this).hasClass('form-info-expand')) {
		$('.form-preferences').hide();
		$('.form-fields').hide();
		$('.dyndata-edit').hide();
		$('.form-info-hide').addClass('form-info-expand');
		$('.form-info-hide').removeClass('form-info-hide');
		info.siblings('div').show();
		$(this).addClass('form-info-hide');
		$(this).removeClass('form-info-expand');
	} else if($(this).hasClass('form-delete')) {
		$(this).parents('.edit-form:first').remove();
	}
	return false;
});

$(document).on('click','#backend-inlineforms-button,#edit-button-add_form,.edit-form-add-field,.form-field-up,.form-field-down,.form-field-delete',function(){
	if($(this).attr("id") == "backend-inlineforms-button") {
		getInlineformsOverview();
		return false;
	} else if($(this).attr("id") == "edit-button-add_form") {
		var data = "<div class='edit-form'>";
		data += "<div class='form-info'><div class='form-name'><input type='text' value='NEW_FORM' /></div><div class='expandbox'><a class='form-info-expand' href=''>#</a><a class='form-delete' href=''>#</a></div></div>";
		data += "<div class='form-preferences'><table>";
		data += "<tr><td>Use CAPTCHA:&nbsp;<input type='checkbox' class='form-captcha' /></td><td></td><td></td></tr>";
		data += "<tr><td>Save data to DB:&nbsp;<input type='checkbox' class='form-db' /></td><td colspan='2'>Send data to e-mail (sperarate addresses with ';'):&nbsp;<input type='input' class='form-email' value=''/></td></tr>";
		data += "<tr><td><div style='float:left;'>Text before form</div>&nbsp;<a class='form-text-expand before' href=''>#</a></td><td><div style='float:left;'>Text after form</div>&nbsp;<a class='form-text-expand after' href=''>#</a></td><td><div style='float:left;'>Success text</div>&nbsp;<a class='form-text-expand success' href=''>#</a></td>";
		data += "<tr class='before-text'><td><div style='float:left;'>Left column</div><br /><div contenteditable='true' class='left_column new-edit'></div></td><td colspan='2'><div style='float:left;'>Right column</div><br /><div contenteditable='true' class='right_column new-edit'></div></td></tr>";
		data += "<tr class='after-text'><td><div style='float:left;'>Left column</div><br /><div contenteditable='true' class='left_column new-edit'></div></td><td colspan='2'><div style='float:left;'>Right column</div><br /><div contenteditable='true' class='right_column new-edit'></div></td></tr>";
		data += "<tr class='success-text'><td colspan='3'><div style='float:left;'>Success text</div><br /><div contenteditable='true' class='success-text-field new-edit'></div></td></tr>";
		data += "</table></div>";
		data += "<div class='form-fields'><table><tr><th>Name</th><th>Type</th><th>Preferences</th><th>Controls</th></tr>";
		data += "</table><button class='edit-form-add-field' type='button'>Add field</button></div>";
		data += "<input class='form-hidden-data' type='hidden' value='NO_CHANGES' />";
		data += "</div>";
		$('#editing-content').append(data);
		$('.new-edit').each(function(){
			$(this).ckeditor({
				filebrowserBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor',
				filebrowserImageBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=image',
				filebrowserFlashBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
				coreStyles_bold: { element: 'b' },
				coreStyles_italic: { element: 'i' },
				enterMode:3
			});
			$(this).removeClass('new-edit');
		});
		updateInlineformsData();
		return true;
	} else if($(this).hasClass('edit-form-add-field')) {
		data += "<tr class='field-row'>";
		data += "<td><div contenteditable class='field-name new-edit'>NEW_FIELD</div></td>";
		data += "<td><select class='field-type'>";
		data += "<option value='text' selected='selected'>Textfield</option>";
		data += "<option value='password'>Password</option>";
		data += "<option value='textarea'>Textarea</option>";
		data += "<option value='email'>E-Mail</option>";
		data += "<option value='checkbox'>Checkbox</option>";
		data += "<option value='radio'>Radio-Buttons</option>";
		data += "<option value='payment' >Bank-Data / Payment</option>";
		data += "<option value='dynamic'>Dynamic</option>";
		data += "</select></td>";
		data += "<td><input type='checkbox' class='field-required' />&nbsp;Required field<br /><input type='checkbox' class='field-display-name' checked='checked'/>&nbsp;Display name</td>";
		data += "<td><a href='#' class='form-field-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='form-field-down'>Down</a>&nbsp;|&nbsp;<a href='#' class='form-field-delete'>Delete</a></td>";
		data += "</tr>";
		$(this).siblings('table:first').append(data);
		$('.new-edit').each(function(){
			$(this).ckeditor({
				filebrowserBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor',
				filebrowserImageBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=image',
				filebrowserFlashBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
				coreStyles_bold: { element: 'b' },
				coreStyles_italic: { element: 'i' },
				enterMode:3
			});
			$(this).removeClass('new-edit');
		});
		updateInlineformsData();
		return true;
	} else if($(this).hasClass('form-field-up')) {
		var row = $(this).parents('tr:first');
		if(row.prev().hasClass('field-row')) {
			row.insertBefore(row.prev());
		} else {
			return false;
		}
		updateInlineformsData();
		return false;
	} else if($(this).hasClass('form-field-down')) {
		var row = $(this).parents('tr:first');
		if(row.next().hasClass('field-row')) {
			row.insertAfter(row.next());
		} else {
			return false;
		}
		updateInlineformsData();
		return false;
	} else if($(this).hasClass('form-field-delete')) {
		var row = $(this).parents('tr:first');
		row.remove();
		updateInlineformsData();
		return false;
	}
	return false;
});

$(document).on('click','.form-text-hide,.form-text-expand',function(){
	if($(this).hasClass('form-text-hide')) {
		var table = $(this).parents('table:first');
		table.find('.before-text').hide();
		table.find('.after-text').hide();
		table.find('.success-text').hide();
		$(this).addClass('form-text-expand');
		$(this).removeClass('form-text-hide');
	} else if($(this).hasClass('form-text-expand')) {
		var table = $(this).parents('table:first');
		table.find('.before-text').hide();
		table.find('.after-text').hide();
		table.find('.success-text').hide();
		table.find('.form-text-hide').addClass('form-text-expand');
		table.find('.form-text-hide').removeClass('form-text-hide');
		if($(this).hasClass('before')) {
			table.find('.before-text').show();
		} else if($(this).hasClass('after')) {
			table.find('.after-text').show();
		} else if($(this).hasClass('success')) {
			table.find('.success-text').show();
		}
		$(this).addClass('form-text-hide');
		$(this).removeClass('form-text-expand');
	}
	return false;
});

$(document).on('change','.field-type',function(){
	if($(this).val() == 'radio') {
		$(this).parents('tr:first').find('.field-required').parents('td:first').append("<br />Options (separate with ';;'):&nbsp;<input type='text' class='radio-options' />");
	} else {
		if($(this).parents('tr:first').find('.radio-options').length) {
			$(this).parents('tr:first').find('.radio-options').remove();
		}
	}
});
