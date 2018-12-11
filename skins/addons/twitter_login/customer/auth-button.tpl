{*
<div class="button_left_align">
	<a href="index.php?target=twitter_login_start"><img src="{$SkinDir}/addons/twitter_login/images/Twitter.png"></a>
</div>
*}
{if $twitter_login_authUrl ne ''}
<a class="login" href="{$twitter_login_authUrl}"><img src="{$SkinDir}/addons/twitter_login/images/Twitter.png" alt="Twitter Login" title="Twitter Login" /></a>
{/if}
