{*include file='common/page_title.tpl' title=$lng.lbl_carriers*}
{if $smarty.get.mode neq 'add'}

{capture name=section}
{capture name=block2}

<div class="box">
<div class="dialog_title">{$lng.txt_shipping_carriers_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="shipping_carriers_form">
<input type="hidden" name="action" value="update"/>

<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="1%">{$lng.lbl_del}</th>
    <th width="100%">{$lng.lbl_carrier}</th>
    <th>{$lng.lbl_configure}</th>
</tr>
</thead>
{foreach from=$carriers item=carrier}
<tr{cycle values=", class='cycle'"}>
    <td>{if !$carrier.addon}<input type="checkbox" name="del_carriers[{$carrier.carrier_id}]" value="1" >{/if}</td>
    <td><input type="text" class="form-control" name="update_carriers[{$carrier.carrier_id}][carrier]" value="{$carrier.carrier|escape}" size="40"></td>
    <td>{if $carrier.addon}<a href="index.php?target=configuration&mode=addons&addon={$carrier.addon}">{$lng.lbl_configure}</a>{/if}</td>
</tr>
{/foreach}
</table>
</form>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('shipping_carriers_form');" button_title=$lng.lbl_update style="btn-green push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('shipping_carriers_form', 'delete');" button_title=$lng.lbl_delete style="btn-danger push-20 push-5-r"}
{include file='admin/buttons/button.tpl' href="index.php?target=shipping_carriers&mode=add" button_title=$lng.lbl_add_new style="btn-green push-20 push-5-r"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_carriers local_config='Shipping'}

{else}

{capture name=section}
{capture name=block}

<div class="box">
<form action="index.php?target={$current_target}" method="post" name="shipping_carriers_form">
<input type="hidden" name="action" value="update"/>

<table class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="100%">{$lng.lbl_carrier}</th>
</tr>
</thead>
<tr>
    <td><input type="text" class="form-control" name="update_carriers[0][carrier]" value="" size="40"></td>
</tr>
</table>
</form>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('shipping_carriers_form');" button_title=$lng.lbl_add style="btn-green push-20"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title="`$lng.lbl_add` `$lng.lbl_carrier`"}

{/if}
