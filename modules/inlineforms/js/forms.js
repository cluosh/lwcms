/*
 * forms.js: Load forms and display
 */
$(document).on('click','.inlineform',function(){
	if($(this).parents('div.inlineform:first').length) {
		var parent = $(this).parents('div.inlineform:first');
		$(this).insertBefore(parent);
		parent.remove();
	} else {
		$(this).wrap('<div class="inlineform"></div>');
		var forminfo = $(this).attr("href").split("?");
		var formname = "";
		var dyndata = "";
		if(forminfo.length > 1) {
			formname = forminfo[0];
			dyndata = forminfo[1];
		} else
			formname = forminfo[0];
		$.ajax({
			type:'GET',
			url:'modules/inlineforms/form.php',
			data:{form:formname,dyn:dyndata},
			async:false,
			dataType:"xml"
		}).done({
		});
	}
});
