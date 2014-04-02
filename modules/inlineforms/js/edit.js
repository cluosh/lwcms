/*
 * edit.js: Main background editing tool for inline forms
 */

// Function for updating hidden data
function updateInlineformsData() {
	$('.edit-form').each(function(){
		var data = $(this).find('.form-name input').val()+"==";
		var custom_checks = "";
		// Fields
		$('.field-row').each(function(){
			var name = $(this).find('.field-name').val();
			data += name.replace(/\W/g, '')+"="+encodeURIComponent(name)+"="+($(this).find('.field-required').is(':checked') ? "1" : "0")+"="+$(this).find('.field-type').val()+";";
			if($(this).find('.field-customcheck').is(':checked')) 
				custom_checks += 'custom_check='+name.replace(/\W/g, '')+";";
		});
		data += "==";
		// Preferences
		data += ($(this).find('.form-defcheck').is(':checked') ? "default_check=-1;" : "");
		data += ($(this).find('.form-db').is(':checked') ? "db=1;" : "");
		data += ($(this).find('.form-captcha').is(':checked') ? "captcha=1;" : "");
		data += ($(this).find('.form-email').val() != "" ? "email="+encodeURIComponent($(this).find('.form-email').val())+";" : "");
		data += custom_checks;
		$(this).children('.form-hidden-data').val(data);
	});
}

// Function for sending values to the database
function valueAssign_inlineforms() {
	updateInlineformsData();
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
	
	// Get information from the database
	$.ajax({
		type:'GET',
		url:'handlers/edit/ajax.php',
		data:{pageid:curPageID,contentArea:'none',contentType:'inlineforms'},
		async:false,
		dataType:"xml"
	}).done(function(data){
		// Evaluate form data
		$('#editing-content').html("");
		$(data).find("form").each(function(){
			var data = "<div class='edit-form'>";
			data += "<div class='form-info'><div class='form-name'><input type='text' value='"+$(this).attr("name")+"' /></div><a class='form-info-expand' href=''>#</a></div>";
			// Get preferences
			var pref = $(this).find('pref');
			data += "<div class='form-preferences'><table>";
			data += "<tr><td><input type='checkbox' class='form-defcheck' "+(pref.find('defcheck').text() == '1' ? "checked='checked'" : "")+"/></td><td>Check fields for validity</td><td><input type='checkbox' class='form-captcha' "+(pref.find('captcha').text() == '1' ? "checked='checked'" : "")+"/></td><td>Use CAPTCHA</td></tr>";
			data += "<tr><td><input type='checkbox' class='form-db' "+(pref.find('db').text() == '1' ? "checked='checked'" : "")+"/></td><td>Save data to DB</td><td><input type='input' class='form-email' value='"+pref.find('email').text()+"'/></td><td>Send data to e-mail (sperarate addresses with ';')</td></tr>";
			data += "</table></div>";
			// Get fields
			data += "<div class='form-fields'><table><tr><th>Name</th><th>Type</th><th>Preferences</th><th>Controls</th></tr>";
			$(this).find('fields').find('field').each(function(){
				data += "<tr class='field-row'>";
				data += "<td><input type='text' class='field-name' value='"+$(this).find('name').text()+"' /></td>";
				data += "<td><select class='field-type'>";
				data += "<option value='text' "+($(this).find('type').text() == 'text' ? "selected='selected'" : "")+">Textfield</option>";
				data += "<option value='password' "+($(this).find('type').text() == 'password' ? "selected='selected'" : "")+">Password</option>";
				data += "<option value='textarea' "+($(this).find('type').text() == 'textarea' ? "selected='selected'" : "")+">Textarea</option>";
				data += "<option value='email' "+($(this).find('type').text() == 'email' ? "selected='selected'" : "")+">E-Mail</option>";
				data += "<option value='checkbox' "+($(this).find('type').text() == 'checkbox' ? "selected='selected'" : "")+">Checkbox</option>";
				data += "<option value='dynamic' "+($(this).find('type').text() == 'dynamic' ? "selected='selected'" : "")+">Dynamic</option>";
				data += "</select></td>";
				data += "<td><input type='checkbox' class='field-required' "+($(this).find('req').text() == '1' ? "checked='checked'" : "")+"/>&nbsp;Required field<br /><input type='checkbox' class='field-customcheck' "+($(this).find('customcheck').text() == '1' ? "checked='checked'" : "")+"/>&nbsp;Custom validation</td>";
				data += "<td><a href='#' class='form-field-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='form-field-down'>Down</a>&nbsp;|&nbsp;<a href='#' class='form-field-delete'>Delete</a></td>";
				data += "</tr>";
			});
			data += "</table><button class='edit-form-add-field' type='button'>Add field</button></div>";
			// Close open tags
			data += "<input class='form-hidden-data' type='hidden' value='NO_CHANGES' />";
			data += "</div>";			
			$('#editing-content').append(data);
		});
	});
}

// Do things on load
$(function(){
	// Add button to header edit menu
	$('#backend-left-menu').append('&nbsp;|&nbsp;<a class="toolbar-link" id="backend-inlineforms-button" href="#">Forms</a>');
});

$(document).on('click','#backend-inlineforms-button,#edit-button-add_form,.edit-form-add-field,.form-field-up,.form-field-down,.form-field-delete',function(){
	if($(this).attr("id") == "backend-inlineforms-button") {
		getInlineformsOverview();
		return false;
	} else if($(this).attr("id") == "edit-button-add_form") {
		var data = "<div class='edit-form'>";
		data += "<div class='form-info'><div class='form-name'><input type='text' value='NEW_FORM' /></div><a class='form-info-expand' href=''>#</a></div>";
		data += "<div class='form-preferences'><table>";
		data += "<tr><td><input type='checkbox' class='form-defcheck' checked='checked' /></td><td>Check fields for validity</td><td><input type='checkbox' class='form-captcha' /></td><td>Use CAPTCHA</td></tr>";
		data += "<tr><td><input type='checkbox' class='form-db' /></td><td>Save data to DB</td><td><input type='input' class='form-email' value=''/></td><td>Send data to e-mail (sperarate addresses with ';')</td></tr>";
		data += "</table></div>";
		data += "<div class='form-fields'><table><tr><th>Name</th><th>Type</th><th>Preferences</th><th>Controls</th></tr>";
		data += "</table><button class='edit-form-add-field' type='button'>Add field</button></div>";
		data += "<input class='form-hidden-data' type='hidden' value='NO_CHANGES' />";
		data += "</div>";
		$('#editing-content').append(data);
		updateInlineformsData();
		return true;
	} else if($(this).hasClass('edit-form-add-field')) {
		data += "<tr class='field-row'>";
		data += "<td><input type='text' class='field-name' value='NEW_FIELD' /></td>";
		data += "<td><select class='field-type'>";
		data += "<option value='text' selected='selected'>Textfield</option>";
		data += "<option value='password'>Password</option>";
		data += "<option value='textarea'>Textarea</option>";
		data += "<option value='email'>E-Mail</option>";
		data += "<option value='checkbox'>Checkbox</option>";
		data += "<option value='dynamic'>Dynamic</option>";
		data += "</select></td>";
		data += "<td><input type='checkbox' class='field-required' />&nbsp;Required field<br /><input type='checkbox' class='field-customcheck' />&nbsp;Custom validation</td>";
		data += "<td><a href='#' class='form-field-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='form-field-down'>Down</a>&nbsp;|&nbsp;<a href='#' class='form-field-delete'>Delete</a></td>";
		data += "</tr>";
		$(this).siblings('table:first').append(data);
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
