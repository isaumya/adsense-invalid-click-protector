(function($){
	var click = 0;
	$(".aicp").click(function(){
		++count; //checking how many times uses click on the ads
		//if the user click on ads for more than 3 times
		if( count > 3 ) {
			// Now it's AJAX time to handle the data and push it to database
			jQuery.ajax({
				type: 'POST',
				url: AICP.ajaxurl,
				data: {
					"action": "process_data", 
					"nonce": AICP.nonce,
					"ip": AICP.ip,
					"countryName": AICP.countryName,
					"countryCode": AICP.countryCode,
					"click_count": count
				},
			});
		}
	});
})(jQuery);