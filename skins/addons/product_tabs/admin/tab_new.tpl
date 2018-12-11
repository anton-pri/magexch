{capture name=section}

<form action="index.php?target={$current_target}" method="post" name="new_tab_form">
    <input type="hidden" name="mode" value="{$mode}" />
    <input type="hidden" name="action" value="tabs_add" />
    <input type="hidden" name="tab_type" value="{$tab_type}" />
    <input type="hidden" name="product_id" value="{$product.product_id}" />
    <table class="table table-striped dataTable vertical-center">
    <thead>
    <tr>
      <th>{$lng.lbl_pt_tab_order}</th>
      <th>{$lng.lbl_pt_tab_title}&nbsp;{$lng.lbl_pt_field_required}&nbsp;/&nbsp;{$lng.lbl_pt_tab_content}&nbsp;{$lng.lbl_pt_field_required}</th>
      <th style="width:180px;">{$lng.lbl_pt_tab_parse_smarty}</th>
      <th style="width:80px;">{$lng.lbl_pt_tab_active}</th>
    </tr>
    </thead>
    <tr valign="top">
      <td><input type="text" class="form-control" size="6" maxlength="11" maxlength="4" name="new_tab[number]" value="{$_new_tab.number}" /></td>
      <td><input type="text" class="form-control" size="30" maxlength="255" name="new_tab[title]" value="{$_new_tab.title}" /></td>
      <td align="center"><input type="checkbox" name="new_tab[parse]" value="Y"{if $_new_tab.parse eq 1} checked{/if} /></td>
      <td align="center"><input type="checkbox" name="new_tab[active]" value="Y"{if $_new_tab.active eq 1} checked{/if} /></td>
    </tr>
    <tr>
      <td colspan="4" class="newtab">{include file='admin/textarea.tpl' name="new_tab[content]" data="`$_new_tab.content`" init_mode='exact'}</td>
    </tr>
    </table>
    <div class="buttons">{include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('new_tab_form', 'tabs_add');" button_title=$lng.lbl_pt_button_add style="btn-green"}</div></td>
    
    
</form>
<div class="pt-fields-note">{$lng.txt_pt_fields_note|substitute:'symbol_required':"`$lng.lbl_pt_field_required`"}</div>

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_pt_product_tabs content=$smarty.capture.section}