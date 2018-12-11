<div id='top_message' class='info'>
	<span id='top_message_content'>{if $alt_content}{$alt_content}{else}{$top_message.content}{/if}</span>
    {if $top_message.anchor}
    <div class="link">
	<a href="#{$top_message.anchor}">{$lng.lbl_go_to_last_edit_section}</a>
	<a href="#{$top_message.anchor}"><img src="{$ImagesDir}/goto_arr.gif" width="12" height="10" alt="" /></a>
    </div>
    {/if}
	<div id='docket'><img src='{$ImagesDir}/envelope.png' alt='envelope' /></div>    
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

