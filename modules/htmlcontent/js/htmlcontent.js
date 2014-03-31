/*
 * htmlcontent.js: Contains edit code for htmlcontent module
 */

function onEditPopupOpen_htmlcontent(object) {
	$('#editing-content').html("<textarea id='ckeditor-area' class='ckeditor-area'>"+object.html()+"</textarea>");
	$('#editing-content textarea').ckeditor({
		filebrowserBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor',
		filebrowserImageBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=image',
		filebrowserFlashBrowseUrl : 'thirdparty/pdw_file_browser/index.php?editor=ckeditor&filter=flash',
		coreStyles_bold: { element: 'b' },
		coreStyles_italic: { element: 'i' }
	});
}

function valueAssign_htmlcontent() {
	return $('.ckeditor-area').ckeditorGet().getData();
}
