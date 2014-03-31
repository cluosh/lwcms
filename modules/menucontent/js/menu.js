/*
 * menu.js: Main menucontent module javascript file
 */

// Update menu content
function updateMenuContent() {
	$('#menu-content-hidden').val("");
	$('.menu-edit-row').each(function(){
		if($(this).hasClass('selected')) {
			$('#menu-content-hidden').val($('#menu-content-hidden').val()+$(this).children('.menu-edit-pagename').html()+"="+encodeURIComponent($(this).children('.menu-edit-display-name').children('input').val())+";");
		}
	});
}

function onEditPopupOpen_menucontent(object) {
	$.ajax({
		type:'GET',
		url:'handlers/edit/ajax.php',
		data:{pageid:curPageID,contentArea:object.attr("id"),contentType:'menucontent'},
		async:false
	}).done(function(data){
		var dataparts = data.split("!!DATA!!");
		// Process menu info
		$('#editing-content').html("<input type='hidden' id='menu-content-hidden' value='NO_CHANGES' /><table id='menu-edit-table'><tr><th>Add to Menu</th><th>Pagename</th><th>Displayed name</th><th>Controls</th></tr></table>");
		var menuinfo = dataparts[1].split(";");
		var pages_strings = dataparts[0].split("!!");
		for(var i=0;i < menuinfo.length;i++) {
			var split_string = menuinfo[i].split("=");
			if(split_string.length == 4 || split_string.length == 2) {
				var row = "<tr class='menu-edit-row selected'>";
				row += "<td class='menu-edit-checkbox'><input type='checkbox' checked='checked'/></td>";
				row += "<td class='menu-edit-pagename'>"+decodeURIComponent(split_string[1]).replace(/\+/g, ' ')+"</td>";
				row += "<td class='menu-edit-display-name'><input type='text' value='"+decodeURIComponent(split_string[0]).replace(/\+/g, ' ')+"' /></td>";
				row += "<td class='menu-edit-controls'><a href='#' class='menu-uplink'>Up</a> <a href='#' class='menu-downlink'>Down</a></td>";
				row += "</tr>";
				var index = $.inArray(decodeURIComponent(split_string[1]).replace(/\+/g, ' '),pages_strings);
				pages_strings.splice(index,1);
				$('#menu-edit-table').append(row);
			}
		}
		for(var i=0;i < pages_strings.length;i++) {
			var row = "<tr class='menu-edit-row'>";
			row += "<td class='menu-edit-checkbox'><input type='checkbox'/></td>";
			row += "<td class='menu-edit-pagename'>"+pages_strings[i]+"</td>";
			row += "<td class='menu-edit-display-name'><input type='text' value='' /></td>";
			row += "<td class='menu-edit-controls'><a href='#' class='menu-uplink'>Up</a> <a href='#' class='menu-downlink'>Down</a></td>";
			row += "</tr>";
			$('#menu-edit-table').append(row);
		}
	});
}

function valueAssign_menucontent() {
	updateMenuContent();
	return $('#menu-content-hidden').val();
}

$(document).on('change','.menu-edit-checkbox input',function(){
	if($(this).is(':checked')) {
		var row = $(this).parents('tr:first');
		row.addClass('selected');
		while(row.prev().hasClass('menu-edit-row') && !row.prev().hasClass('selected')) row.insertBefore(row.prev());
	} else {
		var row = $(this).parents('tr:first');
		row.removeClass('selected');
		while(row.next().hasClass('menu-edit-row') && row.next().hasClass('selected')) row.insertAfter(row.next());
	}
	updateMenuContent();
});

$(document).on('input','.menu-displayed-name input',function(){
	updateMenuContent();
});

$(document).on('click','.menu-uplink,.menu-downlink',function(){
	var row = $(this).parents('tr:first');
	if($(this).hasClass('menu-uplink')) {
		if(row.prev().hasClass('menu-edit-row') && row.prev().hasClass('selected')) row.insertBefore(row.prev());
	} else {
		if(row.next().hasClass('menu-edit-row') && row.next().hasClass('selected')) row.insertAfter(row.next());
	}
	updateMenuContent();
	return false;
});

