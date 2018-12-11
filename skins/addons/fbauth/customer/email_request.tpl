<script type="text/javascript">
{literal}
	function isValidEmail(email) {
		var email = email.replace(/^\s+|\s+$/g, '');
		return (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i).test(email);
	}

	function fb_ok() {
		var email = document.getElementById("email2").value;

		if (email == "" || !isValidEmail(email)) {
			alert("Wrong email");
			return false;
		}
		else {
			fb_login(email);
		}
	}
{/literal}
</script>

<div style="text-align: center;">
	<span style="color: red;">{$lng.txt_facebook_email_limited_access}</span><br /><br />
	<input type="text" value="" id="email2" size="30" /><br /><br />
	{include file="buttons/button.tpl" button_title=$lng.lbl_ok href="javascript:fb_ok();" style="button"}
</div>