{capture name='section'}
{capture name='block'}

<div class="box">
    <table width="100%" cellpadding="2" cellspacing="1" class="table table-striped dataTable vertical-center">
    <thead>
        
  {if $config.clickatell_sms.sms_pause eq 'Y'}
	<a href='index.php?target=settings&cat=clickatell_sms'><span class="label label-info pull-right">Sending paused</span></a>
  {else}
	<a href='index.php?target=settings&cat=clickatell_sms'><span class="label label-success pull-right">Sending enabled</span></a>
  {/if}          
        <tr>
            <th>SMS ID</th>
            <th>Phone</th>
            <th>Body</th>
            <th>Added</th>
            <th>Status</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$messages item=sms}
        <tr id='sms_{$sms.sms_id}'>
            <td>{$sms.sms_id}</td>
            <td>{$sms.sms_to}</td>
            <td>
                {$sms.body}
                {if $sms.error}
                <br />
                <span class="text-warning text-danger">{$sms.error}</span>
                {/if}
            </td>
            <td>{$sms.date_added|date_format}</td>
            <td>{if $sms.date_send eq 0}To be sent{elseif $sms.date_send>$smarty.const.CURRENT_TIME}Postponed{else}Expired{/if}</td>
            <td class="text-center">
                <a href="index.php?target=clickatell_sms&mode=spool&action=send&sms_id={$sms.sms_id}" class="btn btn-green btn-xs ajax"><span class="fa fa-send"></span> Send</a> 
                <a href="index.php?target=clickatell_sms&mode=spool&action=delete&sms_id={$sms.sms_id}" class="btn btn-danger btn-xs ajax"><span class="fa fa-trash"></span> Del</a>
                </td>
        </tr>
    {/foreach}
    </tbody>
    </table>
</div>

<div id="sticky_content" class="buttons">
  {include file="admin/buttons/button.tpl" href="index.php?target=clickatell_sms&mode=spool&action=clean&type=expired" button_title="Clean Expired" style="btn btn-warning push-20 push-5-r"}
  {include file="admin/buttons/button.tpl" href="index.php?target=clickatell_sms&mode=spool&action=clean" button_title="Clean queue" style="btn btn-danger push-20 push-5-r"}
	<a href='index.php?target=settings&cat=clickatell_sms'><span class="label label-info pull-right push-20">Settings</span></a>

</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title="SMS queue"}
