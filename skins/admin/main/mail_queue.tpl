{* E-mail *}
{if $smarty.get.mode neq 'add'}

{capture name=section}
{capture name=block}

<p>Emails from spool are being sent by crontab. Here you can see what will be sent next crontab run. </p>

<div class="form-group">
Total in email queue: {$mail_spool_total}
<form method='POST' name='mail_spool_form'>
    <input type='hidden' name='mode' value='email' />
    <input type='hidden' name='action' value='delete_email' />
<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
     <th class="text-center"><input type='checkbox' class='select_all' class_to_select='mail_checkbox' /></th>
     <th>#</th>
     <th>To</th>
     <th>Subject</th>
</tr>
</thead>
{math equation='x/y' x=$smarty.const.MAIL_SPOOL_TTL y=$smarty.const.SECONDS_PER_HOUR assign='ttl'}
{foreach from=$mail_spool item=mail}
<tr>
    <td align="center"><input type='checkbox' class='mail_checkbox' name='delete[{$mail.mail_id}]' /></td>
    <td><b>{$mail.mail_id}</b></td>
    <td><b>{$mail.mail_to}</b></td>
    <td><b>{$mail.subject}</b>
{if ($mail.send-$mail.created)>constant('MAIL_SPOOL_TTL')}
{assign var='extend_flag' value=1}
<br />
<span class='has-error'><span class='help-block'>
    {$lng.txt_mail_spool_obsolete|substitute:ttl:$ttl}
    </span>
</span>
{/if}    
    
    </td>
</tr>
<tr><td colspan="4">
<div style="border: 1px solid black; padding: 10px">
{$mail.body}
</div>
</td></tr>
{/foreach}
</table>
</div>
<div class="buttons">
    <input type='submit' value='Delete' class="btn btn-danger push-5-r push-20" />
{include file='admin/buttons/button.tpl' button_title="Clean queue" href="index.php?target=mail_queue&mode=email&action=clean_spool" class="btn-danger push-5-r push-20"}
{if $extend_flag}
    {include file='admin/buttons/button.tpl' button_title="Extend" onclick="cw_submit_form('mail_spool_form','extend')" class="btn-green push-5-r push-20"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=mail_queue&mode=add" class="btn-green push-5-r push-20"}

</div>
</form>

<div id="pause_email_sending_container" style="padding:10px 0;">
    <label style="font-weight: bold; line-height: 18px;">{$lng.lbl_pause_email_sending} <input type='checkbox' {if $pause_email_sending eq 1}checked="checked"{/if}
                  onchange='var url="index.php?target=mail_queue&mode=email&action=pause_email_send&pause_email_value=" + (this.checked ? "1" : "0");ajaxGet(url, "pause_email_sending_container");'
        /></label>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_spool}


{else}

{capture name=section2}
{capture name=block2}

{*include file="common/subheader.tpl" title=$lng.lbl_add_test_email*}

<form method='POST' class="form-horizontal">
    <input type='hidden' name='mode' value='email' />
    <input type='hidden' name='action' value='check_email' />
<div class="form-group">
    <label class="col-xs-12">Subject:</label>
    <div class="col-xs-12">
        <select name='subject' class="form-control">
        {foreach from=$subjects item=mt}
        <option value="{$mt}">{$mt}</option>
        {/foreach}
    	</select>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12">Body:</label>
    <div class="col-xs-12">
    <select name='body' class="form-control">
        {foreach from=$bodies item=mt}
        <option value="{$mt}">{$mt}</option>
        {/foreach}
    </select>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">E-mail:</label>
    <div class="col-xs-12">
    	<input type='text' name='email' class="form-control" />
    </div>
</div>

    <div class="buttons"><input type='submit' class="btn btn-green push-20" value='Add' /></div>

</form>

<div id="pause_email_sending_container" style="padding:10px 0;">
    <label style="font-weight: bold; line-height: 18px;">{$lng.lbl_pause_email_sending} <input type='checkbox' {if $pause_email_sending eq 1}checked="checked"{/if}
                  onchange='var url="index.php?target=mail_queue&mode=email&action=pause_email_send&pause_email_value=" + (this.checked ? "1" : "0");ajaxGet(url, "pause_email_sending_container");'
        /></label>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section2 title=$lng.lbl_add_test_email}
{/if}
