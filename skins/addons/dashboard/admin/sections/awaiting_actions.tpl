{if $awaiting_actions.incoming_messages.data>0}
<div class="pull-r-l pull-t push">
  <table class="block-table text-center bg-gray-lighter border-b border-t">
    <tbody>
      <tr>
        <td class="border-r" style="width: 50%;">
          <a href='index.php?target=message_box&sort_field=read_status&sort_direction=1'>
          <div class="h1 font-w700">{$awaiting_actions.incoming_messages.data}</div>
          <div class="h5 text-muted text-uppercase push-5-t">New Messages</div>
          </a>
        </td>
        <td>
            <a href='index.php?target=message_box&sort_field=read_status&sort_direction=1'>
          <div class="push-30 push-30-t">
            <i class="si si-envelope fa-3x text-black-op"></i>
          </div>
          </a>
        </td>
      </tr>
    </tbody>
  </table>
</div>
{/if}

{foreach from=$awaiting_actions item=msg}
{if $msg.hidden eq 0}
<div class='awaiting_action awaiting_action_severity_{$msg.severity}' id='system_message_{$msg.code}'>
{eval var=$msg.message assign=mm}{$mm}
{if $msg.severity neq 'C'}
<span class='awaiting_action_controls'>
	<a href='index.php?target=dashboard_system_messages&action=hide&type=1&code={$msg.code}' class='ajax text-green'>Hide</a> 
    | <a href='index.php?target=dashboard_system_messages&action=delete&code={$msg.code}' class='ajax'>Delete</a>
</span>
{/if}
<div class='clear'></div>
</div>
{else}
{assign var='hidden_awaiting_actions' value=1}
{/if}
{/foreach}
<div class="block-button text-right" id='awaiting_actions_bottom' {if !$hidden_awaiting_actions}style='display: none;'{/if}>
    <a href="index.php?target=dashboard_system_messages&action=show&type=1" class='ajax'>Show all</a>
</div>
