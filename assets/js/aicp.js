(function($){
	var aicpCookies = Cookies.noConflict();
	// Fire the code only of any ad codes exists
	if ( $(".aicp").length > 0 ) {
		if( typeof aicpCookies.get('click_count') === 'undefined' ) {
			var count = 0;
		} else {
			var count = aicpCookies.get('click_count');
		}

		//if the user has already reached the click limit, there is no point of showing the ads
		//just do display none
		if( count >= AICP.clickLimit ) {
			$(".aicp").css({ display: "none" });
		} else {
			$(".aicp").click(function(){
				++count; //checking how many times uses click on the ads
				/* Saving this value to the cookie in case the user reloads the page and the counter gets reset */
				aicpCookies.set('click_count', count, { expires: ( AICP.clickCounterCookieExp )/24 });
				//if the user click on ads for more than 3 times
				if( count >= AICP.clickLimit ) {
					// If the visitor is click bombing, stop showing ads immidiately.
					$(".aicp").css({ display: "none" });
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
						success: function( data ){
							console.log( "You are now blocked from seeing ads." );
						}
					});
				}
			});
		}
	}
})(jQuery);