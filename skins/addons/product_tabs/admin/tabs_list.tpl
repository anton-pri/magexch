{capture name=block}
<form action="index.php?target={$current_target}" method="post" name="update_product_tabs_form">
  <input type="hidden" name="mode" value="{$mode}" />
  <input type="hidden" name="action" value="tabs_update" />
  <input type="hidden" name="tab_type" value="{$tab_type}" />
  <input type="hidden" name="product_id" value="{$product.product_id}" />

  <table class="table table-striped dataTable vertical-center" width="100%">
  <thead>
    <tr>
      <th>{if $product_tabs}<input type='checkbox' class='select_all' class_to_select='product_tabs_item' />{else}&nbsp;{/if}</th>
      <th>{$lng.lbl_pt_tab_order}</th>
      <th>{$lng.lbl_pt_tab_title}</th>
      <th style="width:180px;">{$lng.lbl_pt_tab_parse_smarty}</th>
      <th style="width:80px;">{$lng.lbl_pt_tab_active}</th>
    </tr>
  </thead> 

    {if $product_tabs}

    {foreach from=$product_tabs item=tab}
    <tr{cycle values=', class="cycle"'}>
      <td align="center">{if $tab.global ne 1}<input type="checkbox" value="Y" name="tab_ids[{$tab.tab_id}]" class="product_tabs_item" />{else}<a href="index.php?target=product_tabs" title="{$lng.lbl_pt_global}">{$lng.lbl_pt_global_type_title}</a>{/if}</td>
      <td align="center"><input type="text" class="form-control" size="6" maxlength="11" name="pt_tabs[{$tab.tab_id}][number]" value="{$tab.number|default:0}"{if $tab.global eq 1} disabled{/if} /></td>
      <td>{if $tab.global ne 1}<a href="index.php?target={$current_target}&amp;mode={$mode}&amp;product_id={$product_id}&amp;action=tabs_details&amp;tab_id={$tab.tab_id}&amp;tab_type={$tab_type}&amp;js_tab=product_tabs" title="{$lng.lbl_pt_modify}">{/if}{$tab.title}{if $tab.global ne 1}</a>{/if}</td>
      <td align="center"><input type="checkbox" value="Y" name="pt_tabs[{$tab.tab_id}][parse]"{if $tab.parse eq 1} checked{/if}{if $tab.global eq 1} disabled{/if} /></td>
      <td align="center"><input type="checkbox" value="Y" name="pt_tabs[{$tab.tab_id}][active]"{if $tab.active eq 1} checked{/if}{if $tab.global eq 1} disabled{/if} /></td>
    </tr>
    {/foreach}
    {else}
    <tr>
      <td colspan="5" align="center">{$lng.txt_pt_no_elements}</td>
    </tr>
    {/if}
    </table>

<div class='buttons'>
    {if $product_tabs}
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_product_tabs_form');" button_title=$lng.lbl_pt_update_selected style="btn-green push-5-r push-20"}
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_product_tabs_form', 'tabs_delete');" button_title=$lng.lbl_pt_delete_selected style="btn-danger push-5-r push-20"}
    {/if}
    {include file='admin/buttons/button.tpl' href="index.php?target=product_tabs&mode=add" button_title=$lng.lbl_add_new style="btn-green push-20"}
</div>
</form>
{/capture}
{if $tab_type eq 'global'}
{include file='admin/wrappers/block.tpl' title=$lng.txt_pt_top_text_global content=$smarty.capture.block}
{else}
{include file='admin/wrappers/block.tpl' title=$lng.txt_pt_top_text content=$smarty.capture.block}
{/if}
