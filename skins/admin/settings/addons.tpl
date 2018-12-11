{capture name=section}
{capture name=block}

<form action="index.php?target={$current_target}&mode=addons" method="post" name="addons_form">
<input type="hidden" name="action" value="update" />
	{literal}
<style>
.addon_power.addon_core {
	background: url("../skins/images/padlock_closed.png") no-repeat scroll 5px 5px;
}
.addon_status img {
	opacity: 0.3;
}
.addon_power_switch {
	height:16px;
	width: 16px;
	cursor: pointer;
}
.addon_power_switch.on {
	background: url("../skins/images/power_on.png") no-repeat scroll;
}
.addon_power_switch.off {
	background: url("../skins/images/power_off.png") no-repeat scroll;
}
.addon_power.addon_locked .addon_power_switch {
	opacity: 0.2;	
}

</style>
<script>
function addon_update_status(id) {
{/literal}	
	{if $accl.__2501}
		ajaxGet('index.php?target=configuration&action=ajax_update&addon='+id);
	{/if}
{literal}
}
</script>
{/literal}

<div class="box">

<table class="table table-striped addons">

{foreach from=$addons item=m}
<tr{cycle values=" class='cycle',"}>

    <td align="center" colspan="2" class='addon_power {if $m.status le constant("ADDON_TYPE_CORE")}addon_core addon_locked{/if}'>
		<div class='addon_power_switch {if $m.active}on{else}off{/if}' id='{$m.addon}' {if $m.status gt constant("ADDON_TYPE_CORE")}onclick='addon_update_status("{$m.addon}")'{/if} ></div>
    </td>

	<td width="80%"><b>{$m.addon_lng}</b><br />{$m.addon_descr_lng}
                       {if $m.options_url}
                         <a href="index.php?target=settings&cat={$m.addon}">{$lng.lbl_settings}</a>
                       {else}
                         &nbsp;
                       {/if}
    </td>
    <td class='addon_status addon_status_{$m.status}'>&nbsp;

    {if $m.status == constant('ADDON_TYPE_CORE')}<img src="{$ImagesDir}/addon_core.png" alt='core' title='Core addon' />{/if}
    {if $m.status == constant('ADDON_TYPE_DEV')}<img src="{$ImagesDir}/addon_dev.png" alt='development' title='In development'/>{/if}
    {if $m.status == constant('ADDON_TYPE_UNKNOWN')}<img src="{$ImagesDir}/addon_old.png" alt='trash' title='Old/unknown/broken' />{/if}

    </td>
</tr>
{if $m.subaddons}
{foreach from=$m.subaddons item=ms}
<tr{cycle values=" class='cycle',"}>
  <td><div style="width: 20px;"></div></td>

    <td class='addon_power {if $m.status le constant("ADDON_TYPE_CORE")}addon_core addon_locked{/if}{if !$m.active} addon_locked{/if}'>
		<div class='addon_power_switch {if $ms.active}on{else}off{/if}' id='{$ms.addon}' parent='{$m.addon}' {if $ms.status gt constant("ADDON_TYPE_CORE")}onclick='addon_update_status("{$ms.addon}")'{/if} ></div>
	</td>

    <td ><b>{$ms.addon_lng}</b><br />{$ms.addon_descr_lng}
      {if $ms.options_url}
        <a href="index.php?target=settings&cat={$ms.addon}">{$lng.lbl_settings}</a>
     {else}
       &nbsp;
     {/if}
    </td>
    <td class='addon_status  addon_status_{$ms.status}'>
    {if $ms.status == constant('ADDON_TYPE_CORE')}<img src="{$ImagesDir}/addon_core.png" alt='core' title='Core addon' />{/if}
    {if $ms.status == constant('ADDON_TYPE_DEV')}<img src="{$ImagesDir}/addon_dev.png" alt='development' title='In development'/>{/if}
    {if $ms.status == constant('ADDON_TYPE_UNKNOWN')}<img src="{$ImagesDir}/addon_old.png" alt='trash' title='Old/unknown/broken' />{/if}
    
    </td>

</tr>
{/foreach}
{/if}
{/foreach}
</table>

</div>

{*
<div id="sticky_content" class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('addons_form');" acl='__2501' style="btn-green push-20 push-5-r"}
</div>
*}
</form>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=addons_manager" acl='__2501' style="btn-green push-20 push-5-r"}
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block }

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_addons}

