<ul class="list list-timeline pull-t">

{foreach from=$system_messages item=msg}
<li class='system_message system_message_severity_{$msg.severity}' id='system_message_{$msg.code}'>
{if $msg.hidden eq 0}
    <div class="list-timeline-time">{$msg.date|date_format:$config.Appearance.datetime_format}</div>
    <i class="fa fa-check list-timeline-icon bg-green"></i>
    <div class="list-timeline-content">
      {eval var=$msg.message}
      {if $msg.severity neq 'C'}
      <span class='system_message_controls'>
	  <a href='index.php?target=dashboard_system_messages&action=hide&type=0&code={$msg.code}' class='ajax'>Hide</a>
         |
         <a href='index.php?target=dashboard_system_messages&action=delete&code={$msg.code}' class='ajax'>Delete</a>
      </span>
    </div>
{/if}
{else}
{assign var='hidden_system_messages' value=1}
{/if}
</li>
{/foreach}
</ul>

<div class="block-button text-right" id='system_messages_bottom' {if !$hidden_system_messages}style='display: none;'{/if}>
    <a href="index.php?target=dashboard_system_messages&action=show&type=0" class='ajax btn btn-minw btn-info'>Show all</a>
</div>
