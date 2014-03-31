/*
 * backend_imagelist.js: Edit functions for image lists
 */
 
// Update image list content
function updateImageListContent() {
	$('#imagelist-changes').val("");
	$('.imagelist-row').each(function(){
		$('#imagelist-changes').val($('#imagelist-changes').val()+encodeURIComponent($(this).children('.imagelist-url').children('img').attr("src"))+"="+encodeURIComponent($(this).find('.imagelist-shorttext div').html())+"="+encodeURIComponent($(this).find('.imagelist-longtext div').html())+"="+encodeURIComponent($(this).find('.image-popup-text div').html())+";");
	});
}

function getImageListOverview(areaID) {
	$.ajax({
		type:'GET',
		url:'backend/ajax_edit.php',
		data:{imagelistoverview:curPageID,imagelistarea:encodeURIComponent(areaID)},
		async:false
	}).done(function(data){
		// Parse data
		var chunks = data.split(";");
		// Create Table
		$('#editing-content').html("<input id='imagelist-changes' type='hidden' value='NO_CHANGES' /><table id='imagelist-table'><thead><tr><th>Image-URL</th><th>Popup-Text</th><th>Short-Text</th><th>Long-Text</th><th>Controls</th></tr></thead><tbody></tbody></table><br /><br /><input type='text' name='imagelist-new-image' id='imagelist-new-image' value='Click here to browse images...'/><button type='button' id='imagelist-add-image'>Add Image-Text-Box</button><br /><br />");
		for(i=0;i<chunks.length;i++) {
			var data = chunks[i].split('=');
			if(data.length != 4) break;
			var url = decodeURIComponent(data[0]).replace(/\+/g, ' ');
			var short_text = decodeURIComponent(data[1]).replace(/\+/g, ' ');
			var long_text = decodeURIComponent(data[2]).replace(/\+/g, ' ');
			var popup_text = decodeURIComponent(data[3]).replace(/\+/g, ' ');
			var imagebox = "<tr class='imagelist-row'>";
			imagebox += "<td class='imagelist-url'><img src='"+url+"' /></td>";
			imagebox += "<td class='image-popup-text'><div contenteditable='true'>"+popup_text+"</div></td>";
			imagebox += "<td class='imagelist-shorttext'><div contenteditable='true'>"+short_text+"</div></td>";
			imagebox += "<td class='imagelist-longtext'><div contenteditable='true'>"+long_text+"</div></td>";
			imagebox += "<td><a href='#' class='imagelist-delete'>Delete</a>&nbsp;|&nbsp;<a href='#' class='imagelist-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='imagelist-down'>Down</a></td>";
			imagebox += "</tr>";
			$('#imagelist-table tbody').append(imagebox);
		}
		
		/*
		 * CALLBACKS
		 */
		$('.imagelist-shorttext,.imagelist-longtext,.image-popup-text').each(function(){
			$(this).children('div').ckeditor({
				filebrowserBrowseUrl : 'plugins/pdw_file_browser/index.php?editor=ckeditor',
				filebrowserImageBrowseUrl : 'plugins/pdw_file_browser/index.php?editor=ckeditor&filter=image',
				filebrowserFlashBrowseUrl : 'plugins/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
				coreStyles_bold: { element: 'b' },
				coreStyles_italic: { element: 'i' },
				enterMode:3
			});
		});
		$('#imagelist-new-image').bind('click',function(){
			window.open("plugins/pdw_file_browser/index.php?editor=standalone&returnID=imagelist-new-image",'pdwfilebrowser', 'width=1000,height=650,scrollbars=no,toolbar=no,location=no');
		});
		$('#imagelist-add-image').bind('click',function(){
			if($('#imagelist-new-image').val() != "Click here to browse images...") {
				var imagebox = "<tr class='imagelist-row'>";
				imagebox += "<td class='imagelist-url'><img src='"+$('#imagelist-new-image').val()+"' /></td>";
				imagebox += "<td class='image-popup-text'><div class='new-edit' contenteditable='true'>Popup-Text</div></td>";
				imagebox += "<td class='imagelist-shorttext'><div class='new-edit' contenteditable='true'>Demotext</div></td>";
				imagebox += "<td class='imagelist-longtext'><div  class='new-edit' contenteditable='true'>Demotext</div></td>";
				imagebox += "<td><a href='#' class='imagelist-delete'>Delete</a>&nbsp;|&nbsp;<a href='#' class='imagelist-up'>Up</a>&nbsp;|&nbsp;<a href='#' class='imagelist-down'>Down</a></td>";
				imagebox += "</tr>";
				$('#imagelist-table tbody').append(imagebox);
				$('.new-edit').each(function(){
					$(this).ckeditor({
						filebrowserBrowseUrl : 'plugins/pdw_file_browser/index.php?editor=ckeditor',
						filebrowserImageBrowseUrl : 'plugins/pdw_file_browser/index.php?editor=ckeditor&filter=image',
						filebrowserFlashBrowseUrl : 'plugins/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
						coreStyles_bold: { element: 'b' },
						coreStyles_italic: { element: 'i' },
						enterMode:3
					});
					$(this).removeClass('.new-edit');
				});
				updateImageListContent();
			}
		});
	});
}

$(document).on("click",".imagelist-up,.imagelist-down,.imagelist-delete",function(){
	var row = $(this).parents('tr:first');
	if($(this).hasClass('imagelist-up')) {
		if(row.prev().hasClass('imagelist-row')) {
			row.insertBefore(row.prev());
		} else {
			return false;
		}
	} else if($(this).hasClass('imagelist-down')) {
		if(row.next().hasClass('imagelist-row')) {
			row.insertAfter(row.next());
		} else {
			return false;
		}
	} else {
		row.remove();
	}
	updateImageListContent();
	return false;
});
