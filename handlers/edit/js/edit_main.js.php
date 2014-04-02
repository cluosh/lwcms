/*
 * edit_main.js: Main javascript document for lCMS-Backend
 */

// Set content infos
var curContentArea = "";
var curContentType = 0;
var curContent = "";
var globalAssignFunc = 0;
	
// jQuery-Document ready function, backend core
$(function(){
	// Wrap displayed content
	$('body').html('<div id="backend-wrapper">'+$('body').html()+'</div>');
	
	// Add editing toolbar
	$('body').prepend('<div id="backend-edit-toolbar"></div>');
	// Left menu
	$('#backend-edit-toolbar').append('<div id="backend-left-menu"><a href="#" id="backend-edit-button" class="toolbar-link">Edit</a></div>');
	// Right menu
	$('#backend-edit-toolbar').append('<div id="backend-right-menu"><a href="index.php?logout=<?php echo (isset($_GET['edit_page']) && $_GET['edit_page'] != "" ? $_GET['edit_page'] : "home"); ?>" id="backend-logout-button" class="toolbar-link">Logout</a></div>');
	
	// Add editing popup
	$('body').append('<div id="editing-popup"><div id="edit-bar"><div id="edit-content-selection">Content-Type:&nbsp;<select id="content-type-select"><?php echo $this->modman->displayContentSelectOptions(); ?></select></div><button class="editing-buttons" type="button" id="edit-button-accept">Accept changes</button><button class="editing-buttons" type="button" id="edit-button-discard">Discard changes</button></div><div id="editing-content"></div></div><div id="editing-popup-background"></div>');
	$('#editing-popup').hide();
	$('#editing-popup-background').hide();
	
	/*
	 * CALLBACKS
	 */
	$('.contentArea').bind('click',function(){
		if($('#backend-edit-button').hasClass("active")) {
			// Display editing popup
			$('#editing-popup-background').show();
			$('#editing-popup').show();
			
			// Add edit bar
			$('#edit-content-selection').show();
			$('.editing-buttons').hide();
			$('#edit-button-accept').show();
			$('#edit-button-discard').show();
			
			// Reset content information and set content Area
			curContentArea = $(this).attr('id');
			curContentType = "htmlcontent";
			curContent = "";
			
			// Specific editing function
			<?php
				foreach($this->modman->returnContentModules() as $module) {
					echo "if($(this).hasClass('area_".$module."')) {
					curContentType = '".$module."';
					$('#content-type-select').val(curContentType);
					var fn = window['onEditPopupOpen_".$module."'];
					fn($(this));
				}\n";
				}
			?>
		}
	});
	$('#edit-button-accept').bind('click',function(){
		// Get Content
		var fn = 0;
		<?php 
		foreach($this->modman->returnContentModules() as $module) {
			echo "if(curContentType == '".$module."') {
			fn = window['valueAssign_".$module."'];
		}\n";
		}
		?>
		
		fn = fn ? fn : globalAssignFunc;
		curContent = fn();
		globalAssignFunc = 0;
		
		// Send content Information
		$.ajax({
			type:"POST",
			url:"handlers/edit/ajax.php",
			data:{pageid:curPageID,contentArea:curContentArea,contentType:curContentType,content:curContent},
			async:false
		}).done(function(data){
			if(data != "FAIL") {
				// Clean up popup
				$('#'+curContentArea).html(data);
				$('#editing-content').html("");
				$('#editing-popup').hide();
				$('#editing-popup-background').hide();
			} else {
				alert("Something went wrong. Could not write to Database.");
			}
		});
	});
	$('#edit-button-discard,#editing-popup-background,#edit-button-close').bind('click',function(){
		// Clean up popup
		$('#editing-content').html("");
		$('#editing-popup').hide();
		$('#editing-popup-background').hide();
	});
	$('#backend-edit-button').bind('click',function(){
		$(this).toggleClass("active");
		
		// Append edit stylesheet
		if($(this).hasClass("active")) {
			$('head').append('<link rel="stylesheet" title="backend_edit" type="text/css" href="handlers/edit/css/edit.css" />');
		} else {
			$('link[title=backend_edit]').prop('disabled',true);
		}
		
		return false;
	});
	$('#edit-content-selection').bind('change',function(){
		$('#'+curContentArea).removeClass('area'+curContentType);
		$('#'+curContentArea).addClass('area'+$('#edit-content-selection option:selected').val());
		$('#editing-content').html("");
		$('#'+curContentArea).click();
	});
});
