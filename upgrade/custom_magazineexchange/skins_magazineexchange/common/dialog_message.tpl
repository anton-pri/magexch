
<div id='top_message' class='info'>

<table width="100%" cellpadding="5" border="0"><tr><td width="79">
	<img src='{$AltImagesDir}/admin/Avatar_Shocked_Head.png' alt="Whoops, there's a problem" /> </td><td align="center">

<span id='top_message_content'>

{if $alt_content}{$alt_content}{else}{$top_message.content}{/if}</span>
    {if $top_message.anchor}
    <div class="link">
	<a href="#{$top_message.anchor}">{$lng.lbl_go_to_last_edit_section}</a>
	<a href="#{$top_message.anchor}"><img src="{$ImagesDir}/goto_arr.gif" width="12" height="10" alt="" />    </div>
    {/if}</td></tr></table>

</div>
<script>
$(document).ready(function() {ldelim}
	{if $top_message.content or $alt_content}
	setTimeout(function() {ldelim} show_top_message('{$top_message.type}') {rdelim},300); 
	{/if}
	$('#top_message').click(toggle_top_message);
{rdelim}
);
</script>

