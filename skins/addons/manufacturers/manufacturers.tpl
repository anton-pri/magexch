<script type="text/javascript">
<!--
var txt_manufacturers_delete_msg = "{$lng.txt_manufacturers_delete_msg|escape:javascript}";
-->
{literal}
 var validForm = null;
  $(document).ready(function(){
    validForm = $("#manufacturer_form");
    validForm.validate();
  });
{/literal}  
</script>
{capture name=section}

{if $mode ne 'manufacturer_info'}

{capture name=block}

<p>{$lng.txt_manufacturers_top_text}</p>



<form action="index.php?target={$current_target}" name="search_form" method="post">
<div class="form-horizontal">
<input type="hidden" name="action" value="search" />
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_substring}</label>
    <div class="col-xs-12 col-md-4"><input type="text" name="posted_data[substring]" value="{$search_prefilled.substring|stripslashes|escape}" class="form-control" /></div>

    <div class="col-xs-12 col-md-4">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_search href="javascript: cw_submit_form('search_form');" style="btn-green"}</div>
</div>
</div>
</form>


{include file='common/navigation.tpl'}
<div class="box">

<form action="index.php?target=manufacturers" method="post" name="manuf_form" id="manuf_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page" value="{$page}" />



<table width="100%" class="table table-striped dataTable vertical-center">
<thead>
<tr>
  {if $manufacturers}<th width="10"><input type='checkbox' class='select_all' class_to_select='manufacturers_item' /></th>{/if}
  <th width="70%">{include file='common/sort.tpl' title=$lng.lbl_manufacturer field='manufacturer'}</th>
{if $config.manufacturers.manufacturers_show_cnt_admin eq 'Y'}
  <th width="10%">{include file='common/sort.tpl' title=$lng.lbl_products field='products_count'}</th>
{/if}
  <th width="5%">{$lng.lbl_featured}</th>
  <th width="5%">{$lng.lbl_active}</th>
  <th>&nbsp;</th>
</tr>
</thead>

{foreach from=$manufacturers item=v}
<tr{cycle values=", class='cycle'"}>
  <td align="center"><input type="checkbox" name="to_delete[{$v.manufacturer_id}]" class="manufacturers_item" /></td>
  <td><b><a href="index.php?target=manufacturers&manufacturer_id={$v.manufacturer_id}{if $page}&amp;page={$page}{/if}">{$v.manufacturer}</a></b></td>
{if $config.manufacturers.manufacturers_show_cnt_admin eq 'Y'}
  <td align="center"><a href="index.php?target=products&mode=search&action=search&posted_data[attribute_names][manufacturer_id][]={$v.manufacturer_id}&search_sections[tab_add_search]=1">{$v.products_count|default:$lng.txt_not_available}{if $v.used_by_others gt 0}*{assign var="show_note" value="Y"}{/if}</a></td>
{/if}
  <td align="center"><input type="checkbox" name="records[{$v.manufacturer_id}][featured]" value="1"{if $v.featured} checked="checked"{/if} /></td>
  <td align="center">
    <input type="checkbox" name="records[{$v.manufacturer_id}][avail]" value="1"{if $v.avail} checked="checked"{/if} />
    <input type="hidden" name="records[{$v.manufacturer_id}][update_manufacturer]" value="1" />
  </td>
  <td><a href="index.php?target=manufacturers&manufacturer_id={$v.manufacturer_id}{if $page}&amp;page={$page}{/if}" class="btn btn-xs btn-default">{$lng.lbl_modify}</a></td>
</tr>
{foreachelse}
<tr>
    <td colspan="6" align="center">{$lng.txt_no_manufacturers}</td>
</tr>
{/foreach}
</table>


</form>
</div>

{include file='common/navigation.tpl'}


<div class="buttons">
{if $manufacturers}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('manuf_form')" acl='__1201' style="btn-green push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="javascript: cw_submit_form('manuf_form', 'delete');" acl='__1201' style="btn-danger push-5-r push-20"}
{/if}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=manufacturers&mode=add" acl='__1201' style="btn-green push-5-r push-20"}
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block }

{else}
<div class="block transparent">
{jstabs}
default_tab={$js_tab|default:'info'}

[info]
title={$lng.lbl_manufacturer}
template="addons/manufacturers/manufacturer.tpl"

[fcat]
title={$lng.lbl_featured_categories}
template="addons/clean_urls/manufacturer.tpl"
{/jstabs}


{*include file='addons/manufacturers/manufacturer.tpl'*}
{include file='admin/tabs/js_tabs.tpl' style="default"}

</div>
{/if}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_manufacturers section_id='manufacturer_info'}
