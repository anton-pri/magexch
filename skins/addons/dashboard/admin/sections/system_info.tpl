{foreach from=$system_info item=msg}
{if $msg.hidden eq 0}
<div class='awaiting_action awaiting_action_severity_{$msg.severity}' id='system_message_{$msg.code}'>
{eval var=$msg.message assign=mm}{$mm}
{if $msg.severity neq 'C'}
<span class='awaiting_action_controls'>
	<a href='index.php?target=dashboard_system_messages&action=hide&type=2&code={$msg.code}' class='ajax text-green'>Hide</a> 
    {*| <a href='index.php?target=dashboard_system_messages&action=delete&code={$msg.code}' class='ajax'>Del</a>*}
</span>
{/if}
<div class='clear'></div>
</div>
{else}
{assign var='hidden_system_info' value=1}
{/if}
{/foreach}
<div class="block-button text-right" id='system_info_bottom' {if !$hidden_system_info}style='display: none;'{/if}>
    <a href="index.php?target=dashboard_system_messages&action=show&type=2" class='ajax'>Show all</a>
</div>
