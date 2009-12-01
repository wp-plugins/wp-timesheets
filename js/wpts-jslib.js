jQuery(function() {

	jQuery("#ts_time_in, #ts_time_out").timePicker({
		step: 15,
		show24Hours: false
	});	
	// Store time used by duration.
	
	var oldTime = jQuery.timePicker("#ts_time_in").getTime();
	var duration = (jQuery.timePicker("#ts_time_out").getTime() - oldTime);
	jQuery("#ts_hours_display").html(time_diff(duration)+' hrs');
	//jQuery("#ts_hours").val(time_diff(duration));
		
	// Keep the duration between the two inputs.
	jQuery("#ts_time_in").change(function() {
		if (jQuery("#ts_time_out").val()) { // Only update when second input has a value.
			// Calculate duration.
			var duration = (jQuery.timePicker("#ts_time_out").getTime() - oldTime);
			var time = jQuery.timePicker("#ts_time_in").getTime();	
			// Calculate and update the time in the second input.
			jQuery.timePicker("#ts_time_out").setTime(new Date(new Date(time.getTime() + duration)));
			jQuery("#ts_hours_display").html(time_diff(duration)+' hrs');
			oldTime = time;
		}
	});
	
	// Validate.
	jQuery("#ts_time_out").change(function() {
		// Store time used by duration.
		var oldTime = jQuery.timePicker("#ts_time_in").getTime();
		var duration = (jQuery.timePicker("#ts_time_out").getTime() - oldTime);
			if(jQuery.timePicker("#ts_time_in").getTime() > jQuery.timePicker(this).getTime()) {
				jQuery(this).addClass("error");
			} else {
				jQuery(this).removeClass("error");
			}
		jQuery("#ts_hours_display").html(time_diff(duration)+' hrs');
	});

});

function time_diff(duration){
	// Calculate formated timediff
	timediff = duration;
	hours = Math.floor(timediff / (1000 * 60 * 60)); 
	timediff -= hours * (1000 * 60 * 60);
	mins = Math.floor(timediff / (1000 * 60)); 
	timediff -= mins * (1000 * 60);
	return hours+':'+mins;
}