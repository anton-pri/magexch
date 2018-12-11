{include file="addons/flexible_import/flexible_import_menu.tpl" active="8"}
{capture name=section0}
{capture name=section}

<form action="index.php?target={$current_target}" method="post" name="price_levels_form">
<input type="hidden" name="action" value="price_levels_modify" />

{include file='addons/flexible_import/datahub/price_levels_list.tpl'}

{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form(document.price_levels_form);" button_title=$lng.lbl_add_update style="btn-green push-20 push-5-r"}
{if $price_levels}
{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form(document.price_levels_form,'price_levels_delete');" button_title=$lng.lbl_delete_selected style="btn-danger push-20"}
{/if}

</form>
{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_datahub_price_levels|default:'Price calculation Levels' content=$smarty.capture.section}

{/capture}

{include file="admin/wrappers/section.tpl" content=$smarty.capture.section0 extra='width="100%"' title=$lng.lbl_datahub_price_calculation|default:'DataHub Price Calculation'}
