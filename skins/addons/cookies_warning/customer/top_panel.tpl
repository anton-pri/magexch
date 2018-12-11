{if $cookies_warning_enabled || $smarty.get.force_cookie_warning eq 'Y'}
{*if $smarty.get.force_cookie_warning eq 'Y'*}
<div id="cookiePolicy" class="cookiePolicys" style="z-index: 32769; overflow: hidden; display: none;">
<div class="sleeve">
{*
<h2>{$lng.lbl_cookie_policy}</h2>
*}
<form method="POST" name="cookieswarningform">
<input type="hidden" name="cookies_accept" value="1"/>
<div id="cookieWarningText">
{$lng.txt_cookie_warning}
</div>
<br>
<br>
<div id="cookieWarningAcceptButtonBox">
{include file='buttons/button.tpl' button_title=$lng.lbl_cookies_warning_agree style='button' onclick="javascript: document.cookieswarningform.submit();"}
</div>
</form>
</div>
</div>

<script type="text/javascript">
{literal}
$(document).ready(function() { sm("cookiePolicy",380, 0, true, "Cookie Policy"); });
{/literal}
</script>


{/if}
