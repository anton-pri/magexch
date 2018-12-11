<div id="fb-root"></div>
<script>
	var fbauth_app_id 		= '{$config.fbauth.fbauth_app_id}';
	var fbauth_app_secret	= '{$config.fbauth.fbauth_app_secret}';
	var channel_url 		= '{$SkinDir}/addons/fbauth/customer/channel.html';
	var current_location 	= '{$current_location}';
{literal}
	window.fbAsyncInit = function() {
		// init the FB JS SDK
		FB.init({
			appId      : fbauth_app_id, // App ID from the App Dashboard
			channelUrl : channel_url, // Channel File for x-domain communication
			status     : true, // check the login status upon init?
			cookie     : true, // set sessions cookies to allow your server to access the session?
			xfbml      : true  // parse XFBML tags on this page?
		});
	};

	// Load the SDK's source Asynchronously
	// Note that the debug version is being actively developed and might 
	// contain some type checks that are overly strict. 
	// Please report such bugs using the bugs tool.
	(function(d, debug) {
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); 
		js.id = id; 
		js.async = true;
		js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
		ref.parentNode.insertBefore(js, ref);
	}(document, /*debug*/ false));

	function fb_login(email2) {
		FB.login(function(response) {
			if (response.authResponse) {
				var accessToken = response.authResponse.accessToken;
				fb_user_login(accessToken, email2);
			}
		}, {scope: 'email'});
	}

	function fb_user_login(accessToken, email2) {
		FB.api('/me', function(response) {
			var email = response.email;

			if (email2 != "") {
				email = email2;
			}
			var query = "accessToken=" 	+ accessToken + 
						"&id=" 			+ response.id + 
						"&app_secret=" 	+ fbauth_app_secret + 
						"&email=" 		+ email + 
						"&first_name=" 	+ response.first_name + 
						"&last_name=" 	+ response.last_name + 
						"&gender=" 		+ response.gender;
			document.location = current_location + "/index.php?target=fb_auth&" + query;
		});
	}
{/literal}
</script>