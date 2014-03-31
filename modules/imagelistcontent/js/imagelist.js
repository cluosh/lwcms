/*
 * imagelist.js: Javascript definitions for imagelist content
 */
 
var trigger = false;

$(function(){
	$('body').append("<div class='image-popup'></div>");
});

$(document).on('click','.read-more',function(){
	var imagebox = $(this).parents('.image-list-box');
	imagebox.siblings().each(function(){
		$(this).children('.hidden-text').hide();
	});
	imagebox.children('.text').hide();
	imagebox.children('.hidden-text').show();
	imagebox.children('.hidden-text').css('min-height',(imagebox.siblings('.image-list-box').length-imagebox.index()+1)*104-20+"px");
	return false;
});

$(document).on('click','.image-list-hide',function(){
	var hidden_text = $(this).parents('.hidden-text');
	hidden_text.hide();
	hidden_text.siblings('.text').show();
	return false;
});

$(document).on('mouseenter','.image-list-box img',function(e){
	var image_popup = $('.image-popup');
	image_popup.html($(this).attr("alt"));
	image_popup.show();
});

$(document).on('mousemove','.image-list-box img,.image-popup', function(e){
	var image_popup = $('.image-popup');
	if(image_popup.is(':visible')) {
		image_popup.css("top",e.pageY+5);
		image_popup.css("left",e.pageX+5);
		trigger = true;
	}
});

$(document).mousemove(function(){
	if($('.image-popup').is(':visible')) {
		trigger = !trigger;
		if(trigger)
			$('.image-popup').hide();
	}
});
