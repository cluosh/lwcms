/*
 * forms.js: Load forms and display
 */
$(document).on('click','a.inlineform',function(){
	if($(this).siblings('div.inlineform:first').length) {
		var sibling = $(this).siblings('div.inlineform:first');
		sibling.remove();
	} else {
		if($('div.inlineform').length) {
			$('div.inlineform').each(function(){ $(this).remove(); });
		}
		$('<div class="inlineform" style="border:1px solid black;padding:5px"></div>').insertAfter($(this));
		var forminfo_unsplit = $(this).attr("href")
		var forminfo = forminfo_unsplit.split("?");
		var formname = "";
		var dyndata = "";
		if(forminfo.length > 1) {
			formname = forminfo[0];
			dyndata = encodeURIComponent(forminfo[1]);
		} else
			formname = forminfo[0];
		$.ajax({
			type:'GET',
			url:'modules/inlineforms/form.php',
			data:{form:formname,dyn:dyndata},
			async:false,
			dataType:"xml"
		}).done(function(data){
			// Build form
			var form = "<form name='"+formname+"' method='POST' class='inlineform'><table>";
			var pref = $(data).find('pref');
			// Add text before row
			form += (pref.find('dyn_before_left').text() != "" || pref.find('dyn_before_right').text() != "" ? "<tr class='before-text-row'><td class='left-column'>"+decodeURIComponent(pref.find('dyn_before_left').text()).replace(/\+/g, ' ')+"</td><td class='right-column'>"+decodeURIComponent(pref.find('dyn_before_right').text()).replace(/\+/g, ' ')+"</td></tr>" : "");
			form += (pref.find('before_left').text() != "" || pref.find('before_right').text() != "" ? "<tr class='before-text-row'><td class='left-column'>"+decodeURIComponent(pref.find('before_left').text()).replace(/\+/g, ' ')+"</td><td class='right-column'>"+decodeURIComponent(pref.find('before_right').text()).replace(/\+/g, ' ')+"</td></tr>" : "");
			$(data).find('fields').find('field').each(function(){
				form += "<tr class='input-row'>";
				form += "<td class='descriptor'>"+($(this).find('display_name').text() == "1" ? ($(this).find('req').text() == "1" ? "* " : "")+decodeURIComponent($(this).find('name').text()).replace(/\+/g, ' ') : "")+"</td>";
				switch($(this).find('type').text()) {
					case 'text':
						form += "<td class='input-field'><input type='text' name='form_"+$(this).attr('name')+"' /></td>";
						break;
					case 'password':
						form += "<td class='input-field'><input type='password' name='form_"+$(this).attr('name')+"' /></td>";
						break;
					case 'textarea':
						form += "<td class='input-field' style='vertical-align:top;'><textarea name='form_"+$(this).attr('name')+"'></textarea></td>";
						break;
					case 'email':
						form += "<td class='input-field'><input type='text' name='form_"+$(this).attr('name')+"' /></td>";
						break;
					case 'checkbox':
						form += "<td class='input-field'><input type='text' name='form_"+$(this).attr('name')+"'</td>";
						break;
					case 'radio':
						form += "<td class='input-field'>";
						var name = $(this).attr('name');
						$(this).find('radio_options').find('button').each(function(){
							form += "<input type='radio' name='form_"+name+"' value='"+$(this).text()+"' />&nbsp;"+$(this).text()+"&nbsp;"; 
						});
						form += "</td>";
						break;
					case 'payment':
						form += "<td class='input-field'>";
						form += "<input type='radio' name='form_"+$(this).attr('name')+"' value='bank' class='bank-transfer'> Bank Transfer<br />";
						form += "<input type='radio' name='form_"+$(this).attr('name')+"' class='credit-card' value='american_express'> American Express <input type='radio' name='form_"+$(this).attr('name')+"' class='credit-card' value='visa'> Visa <input type='radio' name='form_"+$(this).attr('name')+"' class='credit-card' value='mastercard'> Mastercard <br />";
						form += "<div class='payment-info' style='display:none;'><br /><input type='text' name='form_"+$(this).attr('name')+"_card_no' /> Card No. <br /><input type='text' name='form_"+$(this).attr('name')+"_card_holder' /> Card Holder<br /><input type='text' name='form_"+$(this).attr('name')+"_expire_date' /> Expire Date</div>";
						form += "</td>";
						break;
					case 'dynamic':
						form += "<td class='input-field'>"+decodeURIComponent($(this).find('content').text()).replace(/\+/g, ' ')+"</td>";
						break;
				}
				form += "</tr>";
			});
			// Add captcha if specified
			if(pref.find('captcha').text() == "1") {
				form += '<tr class="captcha_row"><td class="left-column">* Image Verification</td><td><img id="captcha" src="thirdparty/securimage/securimage_show.php?'+Math.random()+'" alt="CAPTCHA Image" /><br /><input type="text" name="captcha_code" size="10" maxlength="6" />&nbsp;&nbsp;<a href="#" class="change-captcha-img">change Image</a><br /></td></tr>';
			}
			// Add text after row
			form += (pref.find('dyn_after_left').text() != "" || pref.find('dyn_after_right').text() ? "<tr class='after-text-row'><td class='left-column'>"+decodeURIComponent(pref.find('dyn_after_left').text()).replace(/\+/g, ' ')+"</td><td class='right-column'>"+decodeURIComponent(pref.find('dyn_after_right').text()).replace(/\+/g, ' ')+"</td></tr>" : "");
			form += (pref.find('after_left').text() != "" || pref.find('after_right').text() ? "<tr class='after-text-row'><td class='left-column'>"+decodeURIComponent(pref.find('after_left').text()).replace(/\+/g, ' ')+"</td><td class='right-column'>"+decodeURIComponent(pref.find('after_right').text()).replace(/\+/g, ' ')+"</td></tr>" : "");
			form += "<tr><td></td><td><input type='submit' value='Submit' name='submit' /></td></tr></table><input type='hidden' name='formID' value='"+forminfo_unsplit+"' /></form>";
			form += "<script type='text/javascript'>"+decodeURIComponent($(data).find('javascript').text()).replace(/\+/g, ' ')+"</script>";
			$('div.inlineform').append(form);
		});
		$('html, body').animate({
			scrollTop: $('div.inlineform').offset().top
		},500);
		return false;
	}
	return false;
});

function check_captcha() {
	if($('.captcha_row').is(':visible') == false) {
		return true;
	}
	var return_value = false;
	
	$.ajax({
		type:'POST',
		url:'modules/inlineforms/form.php',
		data:{captcha_code:$('form.inlineform input[name=captcha_code]').val()},
		async:false
	}).done(function(data){
		if(data == "TRUE") {
			$('.captcha_row').hide();
			return_value = true;
		} else {
			return_value = false;
		}
	});
	return return_value;
}

function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}

$(document).on('submit','form.inlineform',function(){
	var formdata = $(this).serialize();
	
	if(!form_checks())
		return false;

	// Check if cookies are enabled
	var backup = document.cookie;
	document.cookie = "test=\"\";";
	if(document.cookie.indexOf('test') == -1)
	{
	   alert('Cookies must be enable for website to function properly.');
	   return false;
	}
	else
		document.cookie = backup;
		
	$.ajax({
		type:'POST',
		url:'modules/inlineforms/form.php',
		data:formdata,
		async:false
	}).done(function(data){
		// Delete old text
		$('.form-text').remove();
		if($(data).find('success').length) {
			$('<div class="form-text">'+decodeURIComponent($(data).find('success').text()).replace(/\+/g, ' ')+'</div>').insertBefore('form.inlineform');
			$('form.inlineform').hide();
		} else {
			$('<div class="form-text">'+data+'</div>').insertBefore('form.inlineform');
		}
	});
		
	return false;
});

$(document).on('click','.change-captcha-img',function(){
	$('#captcha').attr('src','thirdparty/securimage/securimage_show.php?' + Math.random());
	return false;
});
