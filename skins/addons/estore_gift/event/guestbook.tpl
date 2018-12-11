{if $guestbook}
{include file='common/subheader.tpl' title=$lng.lbl_giftreg_posted_messages}

{include file='common/navigation.tpl'}

{foreach from=$guestbook item=gb}
<div class="gifts_guestbook {if $gb.moderator} moderator{/if}">
    <div class="date">{$gb.date|date_format}</div>
    <div class="title">{$gb.name}
    {if $allow_edit}
    - [<a href="index.php?target=gifts&mode=events&event_id={$event_id}&message_id={$gb.message_id}&action=delete_gbm">{$lng.lbl_delete}</a>]
    {/if}
    </div>
    <div class="subject">{$gb.subject}</div>
    <div class="message">{$gb.message}</div>
</div>
{/foreach}
{include file='common/navigation.tpl'}
{/if}

{include file='common/subheader.tpl' title=$lng.lbl_giftreg_post_new_message}

<form action="{$script_name}" method="post" name="gbadd_form">
<input type="hidden" name="event_id" value="{$event_data.event_id}" />
<input type="hidden" name="action" value="guestbook" />

<div class="input_field_1">
    <label>{$lng.lbl_your_name}</label>
    <input type="text" size="40" name="gb_details[name]" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_subject}</label>
    <input type="text" size="40" name="gb_details[subject]" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_your_message}</label>
    <textarea cols="55" rows="7" name="gb_details[message]"></textarea>
</div>

{include file='buttons/submit.tpl' href="javascript:cw_submit_form('gbadd_form')"}
</form>
