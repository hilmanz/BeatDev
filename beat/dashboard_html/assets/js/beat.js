
	$(function() {		
		$("#tablesorter-top10venue").tablesorter();
	});	
$(document).ready(function() { 	
// date picker
	$(function() {
		$( "#from" ).datepicker({
		showOn: "button",
		buttonImage: assets_domain+"/img/icon/icon_date_button.png",
		buttonImageOnly: true,
		currentDate:"setDate",
		dateFormat:"yy-mm-dd",
		//defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
		$( "#to" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$( "#to" ).datepicker({
		showOn: "button",
		buttonImage: assets_domain+"/img/icon/icon_date_button.png",
		buttonImageOnly: true,
		dateFormat:"yy-mm-dd",
		//defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
		$( "#from" ).datepicker( "option", "maxDate", selectedDate );
		}
		});
	});

});

$(document).ready(function() { 	
// date picker
	$(function() {
		$( "#from1" ).datepicker({
		showOn: "button",
		buttonImage: assets_domain+"/img/icon/icon_date_button.png",
		buttonImageOnly: true,
		currentDate:"setDate",
		dateFormat:"yy-mm-dd",
		//defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
		$( "#to1" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$( "#to1" ).datepicker({
		showOn: "button",
		buttonImage: assets_domain+"/img/icon/icon_date_button.png",
		buttonImageOnly: true,
		dateFormat:"yy-mm-dd",
		//defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
		$( "#from1" ).datepicker( "option", "maxDate", selectedDate );
		}
		});
	});

});

    $(window).load(function(){
      $('.flexslider').flexslider({
        animation: "slide",
        start: function(slider){
          $('body').removeClass('loading');
        }
      });
    });

