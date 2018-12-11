{*include file='common/page_title.tpl' title=$lng.lbl_ps_manage_offers*}
{capture name=section}
{capture name=block}
<form action="index.php?target={$current_target}" method="post" name="update_offers_form">
    <input type="hidden" name="mode" value="offers" />
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page" value="{$page}" />

    {if $ps_offers}
    {include file='common/navigation.tpl'}
    {/if}

<div class="box">

    <table class="table table-striped dataTable vertical-center" width="100%">
      <thead>
        <tr valign="top">
            <th width="5%" class="text-center"><input type='checkbox' class='select_all' class_to_select='offer_item' /></th>
            <th width="5%">{$lng.lbl_ps_offer_position}</th>
            <th width="5%">{$lng.lbl_ps_offer_priority}</th>
            <th width="60%">{$lng.lbl_ps_offer_title}&nbsp;/&nbsp;{$lng.lbl_ps_offer_desc}</th>
            <th width="10%">{$lng.lbl_ps_offer_startdate}</th>
            <th width="10%">{$lng.lbl_ps_offer_enddate}</th>
            <th width="5%">{$lng.lbl_ps_offer_active}</th>
        </tr>
      </thead>
        {if $ps_offers}

        {foreach from=$ps_offers item=offer}
        <tr{cycle values=', class="cycle"'}>
            <td align="center"><input type="checkbox" value="Y" name="offer_ids[{$offer.offer_id}]" class="offer_item" /></td>
            <td align="center"><input type="text" class="form-control" size="6" maxlength="11" name="ps_offers[{$offer.offer_id}][position]" value="{$offer.position|default:0}" /></td>
            <td align="center"><input type="text" class="form-control" size="6" maxlength="11" name="ps_offers[{$offer.offer_id}][priority]" value="{$offer.priority|default:0}" /></td>
            <td><div><a href="index.php?target={$current_target}&amp;mode=details&amp;action=details&amp;offer_id={$offer.offer_id}" title="{$lng.lbl_ps_modify}">{$offer.title|escape}</a>
            {if $offer.exclusive eq 1}<span class="ps-exclusive-note">{$lng.lbl_ps_exlusive_note}</span>{/if}</div>
                {if $offer.description}<div>{$offer.description}</div>{/if}</td>
            <td align="right">{$offer.startdate|date_format:$config.Appearance.date_format|escape}</td>
            <td align="right">{$offer.enddate|date_format:$config.Appearance.date_format|escape}</td>
            <td align="center"><input type="checkbox" value="1" name="ps_offers[{$offer.offer_id}][active]"{if $offer.active eq 1} checked{/if} /></td>
        </tr>
        {/foreach}
        {if $navigation.total_pages gt 2}
        <tr>
            <td colspan="6">{include file='common/navigation.tpl'}</td>
        </tr>
		{/if}

        {else}
        <tr>
            <td colspan="6" align="center">{$lng.txt_ps_no_elements}</td>
        </tr>
        {/if}
    </table>
</div>


        <div class="buttons">
        {if $ps_offers}
                {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_offers_form');" button_title=$lng.lbl_update style="btn-green push-20 push-5-r"}
                {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_offers_form', 'delete');" button_title=$lng.lbl_ps_delete_selected style="btn-danger push-20 push-5-r"}
        {/if}
                {include file='admin/buttons/button.tpl' href="index.php?target=promosuite&action=form&offer_id=" button_title=$lng.lbl_add_new style="btn-green push-20"}
        </div>


</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.txt_ps_top_text}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_ps_manage_offers}
