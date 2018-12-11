<div class="events">
<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_event}</th>
    <th>{$lng.lbl_giftreg_html_card}</th>
    <th>{$lng.lbl_status}</th>
    <th>{$lng.lbl_giftreg_products}</th>
    <th>{$lng.lbl_recipients}</th>
    <th>{$lng.lbl_sent}</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>
{if $events_list}
{foreach from=$events_list item=event}
<tr{cycle values="class='cycle'",}>
    <td class="event-title"><b>{$event.title}</b> <i>{$event.event_date|date_format}</i></td>
    <td>{if $event.html_content}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>
{if $event.status eq 'P'}
<img src="{$ImagesDir}/private.gif" width="19" height="19" alt="{$lng.lbl_private|escape}" />
{elseif $event.status eq 'G'}
<img src="{$ImagesDir}/public.gif" width="19" height="19" alt="{$lng.lbl_public|escape}" />
{else}
<img src="{$ImagesDir}/access_denied.gif" width="19" height="19" alt="{$lng.lbl_disabled|escape}" />
{/if}
    </td>
    <td align="center">{$event.products}</td>
    <td align="center">{$event.emails}</td>
    <td align="center">
        {if $event.sent_date gt 0}<span title="{$lng.lbl_sent_date|escape}: {$event.sent_date|date_format:$config.Appearance.datetime_format}">{$event.sent_date|date_format:$config.Appearance.date_format}</span>{elseif $event.allow_to_send}<a href="index.php?target=gifts&mode=events&event_id={$event.event_id}&amp;mode=send" title="{$lng.lbl_giftreg_send_notification|escape}"><b>{$lng.lbl_go}</b></a>{else}{$lng.txt_not_available}{/if}
    </td>
    <td style="text-align: right;"><a href="index.php?target=gifts&mode=events&event_id={$event.event_id}">{$lng.lbl_modify}</a></td>
    <td style="text-align: right;"><a href="index.php?target=gifts&mode=events&event_id={$event.event_id}&action=delete">{$lng.lbl_delete}</a></td>
</tr>
{/foreach}
{else}
<tr>
    <td colspan="7" align="center">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>

{if $events_lists_count lt $config.estore_gift.events_lists_limit}
<div class="button_left_align">{include file='buttons/button.tpl' button_title=$lng.lbl_giftreg_new_event style='btn' href="index.php?target=gifts&mode=events&event_id="}</div>
{else}
{$lng.txt_giftreg_max_allowed_events_msg}
{/if}
<br/>
{$lng.lbl_event_used_max}: {$events_lists_count|default:"0"}/{$config.estore_gift.events_lists_limit}
</div>
