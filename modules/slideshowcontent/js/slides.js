/*
 * slides.js: Main slideshow module file
 */

// Update content function
function updateSlidesJSContent() {
	$('#slideshow-changes').val($('#slideshow-width-input').val()+";"+$('#slideshow-height-input').val()+";"+$('#slideshow-time-input').val()+";");
	$('.slide-row').each(function(){
		$('#slideshow-changes').val($('#slideshow-changes').val()+encodeURIComponent($(this).children('.slide-image-url').children('img').attr('src'))+"="+encodeURIComponent($(this).find('.slide-image-text div').html())+";");
	});
}

function onEditPopupOpen_slideshowcontent(object) {
	$.ajax({
		type:'GET',
		url:'handlers/edit/ajax.php',
		data:{pageid:curPageID,contentArea:object.attr("id"),contentType:'slideshowcontent'},
		async:false
	}).done(function(data){
		// Process data
		var chunks = data.split(";");
		if(chunks.length < 3) {
			chunks = "600;300;2500;";
			chunks = chunks.split(";");
		}
		var width = chunks[0];
		var height = chunks[1];
		var display_time = chunks[2];
		// Create Table of Elements
		$('#editing-content').html("<input id='slideshow-changes' type='hidden' value='NO_CHANGES'/><table id='slideshow-sizes' stlye='margin-bottom:20px;'><tr><td>Width:</td><td><input type='text' id='slideshow-width-input' class='slide-input' value='"+width+"' disabled='disabled'/></td></tr><tr><td>Height:</td><td><input type='text' id='slideshow-height-input' class='slide-input' value='"+height+"' disabled='disabled'/></td></tr><tr><td>Display-Time (msec):</td><td><input type='text' id='slideshow-time-input' class='slide-input' value='"+display_time+"'/></td></tr></table><table id='slideshow-images-table'><thead><tr><th>Image-URL</th><th>Image-Text</th><th>Controls</th></tr></thead><tbody></tbody></table><br /><br /><input type='text' name='slide-new-image' id='slide-new-image' value='Click here to browse images...'/><button type='button' id='slide-add-image'>Add Image</button><br /><br />");
		for(var i = 3;i < chunks.length;i++) {
			slide_data = chunks[i].split("=");
			if(slide_data.length != 2) break;
			var slide = "<tr class='slide-row'>";
			slide += "<td class='slide-image-url'><img src='"+decodeURIComponent(slide_data[0]).replace(/\+/g, ' ')+"' /></td>";
			slide += "<td class='slide-image-text'><div class='slide-input' contenteditable='true'>"+decodeURIComponent(slide_data[1]).replace(/\+/g, ' ')+"</div></td>";
			slide += "<td><a href='#' class='slide-control-delete'>Delete</a>&nbsp;|&nbsp;<a href='#' class='slide-control-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='slide-control-down'>Down</a></td></tr>";
			$('#slideshow-images-table tbody').append(slide);
		}
		$('.slide-image-text').each(function(){
			$(this).children('div').ckeditor({
				filebrowserBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor',
				filebrowserImageBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=image',
				filebrowserFlashBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
				coreStyles_bold: { element: 'b' },
				coreStyles_italic: { element: 'i' }
			});
		})
	});
}

function valueAssign_slideshowcontent() {
	updateSlidesJSContent();
	return $('#slideshow-changes').val();
}

$(document).on('input','.slide-input',function(){
	updateSlidesJSContent();
});
$(document).on('change','.slide-image-text div',function(){
	updateSlidesJSContent();
});
$(document).on("click",".slide-control-up,.slide-control-down,.slide-control-delete,#slide-new-image,#slide-add-image",function(){
	var row = $(this).parents('tr:first');
	if($(this).hasClass('slide-control-up')) {
		if(row.prev().hasClass('slide-row')) {
			row.insertBefore(row.prev());
		} else {
			return false;
		}
	} else if($(this).hasClass('slide-control-down')) {
		if(row.next().hasClass('slide-row')) {
			row.insertAfter(row.next());
		} else {
			return false;
		}
	} else if($(this).hasClass('slide-control-delete')) {
		row.remove();
	} else if($(this).attr("id") == 'slide-new-image') {
		window.open("thirdparty/pdw_file_browser/index.php?editor=standalone&returnID=slide-new-image",'pdwfilebrowser', 'width=1000,height=650,scrollbars=no,toolbar=no,location=no');
		return true;
	} else if($(this).attr("id") == 'slide-add-image') {
		if($(this).val() != "Click here to browse images...") {
			var slide = "<tr class='slide-row'>";
			slide += "<td class='slide-image-url'><img src='"+decodeURIComponent($('#slide-new-image').val()).replace(/\+/g, ' ')+"' /></td>";
			slide += "<td class='slide-image-text'><div class='slide-input new-edit' contenteditable='true'>Demotext</div></td>";
			slide += "<td><a href='#' class='slide-control-delete'>Delete</a>&nbsp;|&nbsp;<a href='#' class='slide-control-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='slide-control-down'>Down</a></td></tr>";
			$('#slideshow-images-table tbody').append(slide);
			$('.new-edit').each(function(){
				$(this).ckeditor({
					filebrowserBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor',
					filebrowserImageBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=image',
					filebrowserFlashBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
					coreStyles_bold: { element: 'b' },
					coreStyles_italic: { element: 'i' },
					enterMode:3
				});
				$(this).removeClass('.new-edit');
			});
			updateSlidesJSContent();
			return true;
		}
		return false;
	}
	updateSlidesJSContent();
	return false;
});
