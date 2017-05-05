$(document).ready(function() {
	$('.showPopup').fancybox();

});
	$(function() {		
		$("#tablesorter-top10venue").tablesorter();
	});	
$(document).ready(function() { 	
// date picker
	$(function() {
		$( "#from" ).datepicker({
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